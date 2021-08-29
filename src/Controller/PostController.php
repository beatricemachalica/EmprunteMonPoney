<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Equid;
use App\Entity\Photo;
use App\Form\PostType;
use PHPUnit\Util\Json;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\CommentType;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends AbstractController
{
    /**
     * @Route("/posts", name="posts")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findBy(['active' => true], ['createdAt' => 'desc']);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/posts", name="my_post")
     */
    public function myPost(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        // get current user id
        $userId = $this->getUser()->getId();

        // get all user's posts
        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findBy(array('user' => $userId), null);

        // get all user's horses
        $horses = $this->getDoctrine()
            ->getRepository(Equid::class)
            ->findBy(array('user' => $userId), null);

        return $this->render('post/myPost.html.twig', [
            'posts' => $posts,
            'horses' => $horses
        ]);
    }

    /**
     * @Route("/activate/{id}", name="activate_post")
     * this method allow to activate or not a post
     */
    public function activatePost(Post $post)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        // if the post is already activated => false, if it is not activated => true
        $post->setActive(($post->getActive()) ? false : true);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        return new Response("true");
    }

    /**
     * @Route("/newPost", name="add_post")
     * @Route("/editPost/{id}", name="edit_post")
     */
    public function newPost(Request $request, Post $post = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        if (!$post) {
            $post = new Post();
        }

        // get user id
        $userId = $this->getUser()->getId();

        // get user's horses
        // an exact collection of entities that I want to include in the "choice element"
        $horses = $this->getDoctrine()
            ->getRepository(Equid::class)
            ->findBy(array('user' => $userId), null);

        $form = $this->createForm(PostType::class, $post, [
            'horses' => $horses
        ]);

        $form->handleRequest($request);

        // get the array of user's roles
        $userRolesArray = $this->getUser()->getRoles();

        if ($form->isSubmitted() && $form->isValid()) {

            // set the authenticated user
            $post->setUser($this->getUser());

            // set the active attribut to true
            // by default the post will be activated and will appear on the website
            $post->setActive(true);

            // set automatically a category
            // this may no longer be useful if several categories may be implemented to the website

            // get the categories (borrower and owner)
            $categoryEmprunt = $this->getDoctrine()
                ->getRepository(Category::class)
                ->findOneBy(array('name' => 'profil d\'emprunteur'), null);

            $categoryProprio = $this->getDoctrine()
                ->getRepository(Category::class)
                ->findOneBy(array('name' => 'profil d\'un cheval'), null);

            // if the user is a borrower ("ROLE_EMPRUNT")
            if (in_array("ROLE_EMPRUNT", $userRolesArray) && ($this->getUser()->getPosts() == null)) {

                // set the right category
                $post->setCategory($categoryEmprunt);
            } else {
                $this->addFlash('message', 'Vous avez déjà créé une annonce pour trouver un cheval. Adin d\'éviter les doublons les emprunteurs ne peuvent créer qu\'une seule annonce.');
                return $this->redirectToRoute('my_post');
            }

            // if the user is an owner ("ROLE_PROPRIO") and his horse(s) has been correctly registered
            if ((in_array("ROLE_PROPRIO", $userRolesArray)) && (!empty($horses))) {

                // set the right category
                $post->setCategory($categoryProprio);
            } elseif (empty($horses)) {

                // if the owner has fogotten to register at least one horse
                $this->addFlash('error', "Veuillez inscrire au moins un cheval avant de créer une annonce.");
                return $this->redirectToRoute('my_post');
            }
            // end set automatically a category

            // pictures
            $pictures = $form->get('photo')->getData();
            // for each new picture added by the user
            foreach ($pictures as $picture) {
                // create a new name file with md5 & uniquid and set the right extension
                $file = md5(uniqid()) . '.' . $picture->guessExtension();

                // copy this file into uploads
                // move() : first param : destination
                // move() : second param : file
                $picture->move(
                    $this->getParameter('images_directory'),
                    $file
                );

                // save the name on DB
                $img = new Photo();
                $img->setName($file);
                $post->addPhoto($img);
            }

            // flash message in case of edit or a new post
            $idPost = $post->getId();
            if ($idPost == null) {
                $this->addFlash('message', 'L\'annonce a bien été enregistrée.');
            } else {
                $this->addFlash('message', 'Votre annonce a bien été modifiée.');
            }

            // flush data
            $post = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('my_post');
        }

        return $this->render('post/newPost.html.twig', [
            'form' => $form->createView(),
            'editMode' => $post->getId() !== null,
            'proprioPost' => in_array("ROLE_PROPRIO", $userRolesArray)
        ]);
    }

    /**
     * @Route("/showPost/{id}", name="show_post")
     */
    public function showPost(Post $post, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        if (!$post) {
            throw new NotFoundHttpException('L\'annonce n\'a pas été trouvée.');
        }

        // Comments
        // create a comment object
        $com = new Comment;

        // create comments form
        $form = $this->createForm(CommentType::class, $com);

        // get information from $request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // set the right post
            $com->setPost($post);

            // set the current user
            $com->setUser($this->getUser());

            // flush data
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($com);
            $entityManager->flush();

            $this->addFlash('message', 'Votre commentaire a bien été enregistré.');
            return $this->redirectToRoute('show_post', [
                'id' => $post->getId()
            ]);
        }

        return $this->render('post/showPost.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/favoritePosts", name="favorite_post")
     */
    public function showFavorite(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findBy(['active' => true], ['createdAt' => 'desc']);

        return $this->render('post/favoritePosts.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/favorite/add/{id}", name="add_favorite")
     */
    public function addFavorite(Post $post)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        if (!$post) {
            throw new NotFoundHttpException('L\'annonce n\'a pas été trouvée.');
        }

        $post->addFavorite($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        return $this->redirectToRoute('show_post', [
            'id' => $post->getId()
        ]);
    }

    /**
     * @Route("/favorite/remove/{id}", name="remove_favorite")
     */
    public function removeFavorite(Post $post)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        if (!$post) {
            throw new NotFoundHttpException('L\'annonce n\'a pas été trouvée.');
        }

        $post->removeFavorite($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        return $this->redirectToRoute('show_post', [
            'id' => $post->getId()
        ]);
    }

    /**
     * @Route("/deletePicture/{id}", name="delete_picture", methods={"DELETE"})
     */
    public function deletePicture(Photo $photo, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // decode json code (ajax used)
        $data = json_decode($request->getContent(), true);

        // delete the picture only if the deleteToken is valid 
        if ($this->isCsrfTokenValid('delete' . $photo->getId(), $data['_token'])) {
            $name = $photo->getName();
            // delete the file
            unlink($this->getParameter('images_directory') . '/' . $name);

            // delete the picture name in the DB
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($photo);
            $entityManager->flush();

            // json response
            return new JsonResponse(['success' => 1]);
        } else {

            return new JsonResponse(['error' => 'Erreur accès refusé'], 400);
        }
    }

    /**
     * @Route("/deletePost/{id}", name="delete_post")
     */
    public function deletePost(Post $post): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        // flash message
        $this->addFlash('message', 'Votre annonce a bien été supprimée.');
        return $this->redirectToRoute('my_post');
    }
}
