<?php

namespace App\Controller;

use DateTime;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Equid;
use App\Entity\Photo;
use App\Form\PostType;
use PHPUnit\Util\Json;
use App\Entity\Comment;
use App\Data\SearchData;
use App\Entity\Category;
use App\Form\SearchType;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/posts", name="posts")
     */
    public function index(PostRepository $postRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        // data initialization
        $data = new SearchData();

        // paginator
        $data->page = $request->get('page', 1);

        // form creation
        $form = $this->createForm(SearchType::class, $data);

        $form->handleRequest($request);
        // handleRequest() = read data off of the correct PHP superglobals (i.e. $_POST or $_GET) 
        // based on the HTTP method configured on the form (POST is default).

        $posts = $postRepository->findSearch($data);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView('post/_indexContent.html.twig', ['posts' => $posts])
            ]);
        }

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/myPosts", name="my_posts")
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
     * @Route("/activate", name="activate_post")
     * this method allow to activate or not a post
     */
    public function activatePost(PostRepository $repo)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        $json = file_get_contents('php://input');

        // on d??code la r??ponse json
        $data = json_decode($json);

        if (isset($data)) {

            $postId = $data->postId;

            $post = $repo->findOneBy(array('id' => $postId));

            // if the post is already activated => false, if it is not activated => true
            $post->setActive(($post->getActive()) ? false : true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->json("ok");
        } else {
            return $this->json("error");
        }
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/newPost", name="add_post")
     * @Route("/editPost/{id}", name="edit_post")
     * add or new post for "ROLE_EMPRUNT" user
     */
    public function newPost(Request $request, Post $post = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        $addNewPostMode = false;

        if (!$post) {
            $addNewPostMode = true;
        }

        if ((!$post) || (($addNewPostMode === false) && ($this->getUser()->getId() === $post->getUser()->getId()))) {

            if (!$post) {
                $post = new Post();
            }

            $form = $this->createForm(PostType::class, $post);

            $form->handleRequest($request);
            // handleRequest() = read data off of the correct PHP superglobals (i.e. $_POST or $_GET) 
            // based on the HTTP method configured on the form (POST is default).

            // get the array of user's roles
            $userRolesArray = $this->getUser()->getRoles();

            $userPosts = $this->getDoctrine()
                ->getRepository(Post::class)
                ->findBy(array('user' => $this->getUser()->getId()), null);

            if ($form->isSubmitted() && $form->isValid()) {

                // set the authenticated user
                $post->setUser($this->getUser());

                // set the active attribut to true
                // by default the post will be activated and will appear on the website
                $post->setActive(true);

                // set automatically a category
                // this may no longer be useful if several categories may be implemented to the website

                // get the category borrower

                $categoryEmprunt = $this->getDoctrine()
                    ->getRepository(Category::class)
                    ->findOneBy(array('name' => 'profil d\'emprunteur'), null);

                // if the user is a borrower ("ROLE_EMPRUNT") AND the user want to add a new post

                if (in_array("ROLE_EMPRUNT", $userRolesArray) && (empty($userPosts)) && ($addNewPostMode)) {

                    // set the right category
                    $post->setCategory($categoryEmprunt);
                } elseif (in_array("ROLE_EMPRUNT", $userRolesArray) && (!empty($userPosts)) && $addNewPostMode) {

                    // if the user is a borrower ("ROLE_EMPRUNT") and already has one post
                    $this->addFlash('message', 'Vous avez d??j?? cr???? une annonce pour trouver un cheval. Afin d\'??viter les doublons les emprunteurs ne peuvent cr??er qu\'une seule annonce.');
                    return $this->redirectToRoute('my_posts');
                }

                // flash message in case of edit or a new post
                $idPost = $post->getId();
                if ($idPost == null) {
                    $this->addFlash('message', 'L\'annonce a bien ??t?? enregistr??e.');
                } else {
                    $this->addFlash('message', 'Votre annonce a bien ??t?? modifi??e.');
                }

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

                // flush data
                $post = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($post);
                $entityManager->flush();

                return $this->redirectToRoute('my_posts');
                // end set automatically a category

            }

            return $this->render('post/newPost.html.twig', [
                'form' => $form->createView(),
                'editMode' => $post->getId() !== null,
                'proprioPost' => in_array("ROLE_PROPRIO", $userRolesArray)
            ]);
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/newHorsePost/{id}", name="add_horse_post")
     */
    public function newHorsePost(Request $request, Equid $equid): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        if ($this->getUser()->getId() === $equid->getUser()->getId()) {

            $post = new Post();

            $form = $this->createForm(PostType::class, $post);

            $form->handleRequest($request);
            // handleRequest() = read data off of the correct PHP superglobals (i.e. $_POST or $_GET) 
            // based on the HTTP method configured on the form (POST is default).

            // get the array of user's roles
            $userRolesArray = $this->getUser()->getRoles();

            if ($form->isSubmitted() && $form->isValid()) {

                // set automatically a category
                // this may no longer be useful if several categories may be implemented to the website

                // get the category owner
                $categoryProprio = $this->getDoctrine()
                    ->getRepository(Category::class)
                    ->findOneBy(array('name' => 'profil d\'un cheval'), null);

                // if the user truly is an owner ("ROLE_PROPRIO") and his horse has been correctly registered
                if ((in_array("ROLE_PROPRIO", $userRolesArray)) && (!empty($equid))) {

                    // set the right category
                    $post->setCategory($categoryProprio);

                    // set the right horse
                    $post->setEquid($equid);

                    // set the authenticated user
                    $post->setUser($this->getUser());

                    // set the active attribut to true
                    // by default the post will be activated and will appear on the website
                    $post->setActive(true);
                } elseif (empty($equid)) {

                    $this->addFlash('message', "Veuillez inscrire au moins un cheval avant de cr??er une annonce.");
                    return $this->redirectToRoute('user_account');
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

                // flush data
                $post = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($post);
                $entityManager->flush();

                // flash message
                $this->addFlash('message', 'L\'annonce a bien ??t?? enregistr??e.');
                return $this->redirectToRoute('my_posts');
            }

            return $this->render('post/newPost.html.twig', [
                'form' => $form->createView(),
                'editMode' => $post->getId() !== null,
                'horse' => $equid,
                'proprioPost' => in_array("ROLE_PROPRIO", $userRolesArray)
            ]);
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/editHorsePost/{id}", name="edit_horse_post")
     */
    public function editHorsePost(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        if ($this->getUser()->getId() === $post->getUser()->getId()) {

            $form = $this->createForm(PostType::class, $post);

            $form->handleRequest($request);
            // handleRequest() = read data off of the correct PHP superglobals (i.e. $_POST or $_GET) 
            // based on the HTTP method configured on the form (POST is default).

            // get the array of user's roles
            $userRolesArray = $this->getUser()->getRoles();


            if ($form->isSubmitted() && $form->isValid()) {

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

                // flush data
                $post = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($post);
                $entityManager->flush();

                // flash message
                $this->addFlash('message', 'Votre annonce a bien ??t?? modifi??e.');
                return $this->redirectToRoute('my_posts');
            }

            return $this->render('post/newPost.html.twig', [
                'form' => $form->createView(),
                'editMode' => $post->getId() !== null,
                'proprioPost' => in_array("ROLE_PROPRIO", $userRolesArray)
            ]);
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/showPost/{id}", name="show_post")
     */
    public function showPost(Post $post, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        if (!$post) {
            throw new NotFoundHttpException('L\'annonce n\'a pas ??t?? trouv??e.');
        }

        // Comments
        // create a comment object
        $com = new Comment;

        // create comments form
        $form = $this->createForm(CommentType::class, $com);

        // get information from $request
        $form->handleRequest($request);
        // handleRequest() = read data off of the correct PHP superglobals (i.e. $_POST or $_GET) 
        // based on the HTTP method configured on the form (POST is default).

        if ($form->isSubmitted() && $form->isValid()) {

            // set the right post
            $com->setPost($post);

            // set the current user
            $com->setUser($this->getUser());

            // flush data
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($com);
            $entityManager->flush();

            $this->addFlash('message', 'Votre commentaire a bien ??t?? enregistr??.');
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

        $user = $this->getUser();
        $posts = $user->getFavorites();

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
            throw new NotFoundHttpException('L\'annonce n\'a pas ??t?? trouv??e.');
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
            throw new NotFoundHttpException('L\'annonce n\'a pas ??t?? trouv??e.');
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
     * @Route("/editComment/{id}", name="comment_edit")
     */
    public function editComment(Request $request, Comment $com): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // security
        if ($this->getUser()->getId() === $com->getUser()->getId()) {

            // create form
            $form = $this->createForm(CommentType::class, $com);

            // get information from $request
            $form->handleRequest($request);
            // handleRequest() = read data off of the correct PHP superglobals (i.e. $_POST or $_GET) 
            // based on the HTTP method configured on the form (POST is default).

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($com);
                $entityManager->flush();

                // flash message
                $this->addFlash('message', 'Votre commentaire a bien ??t?? mis ?? jour.');
                return $this->redirectToRoute('show_post', [
                    'id' => $com->getPost()->getId()
                ]);
            }

            return $this->render('comment/editComment.html.twig', [
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/deleteComment/{id}", name="delete_comment")
     */
    public function deleteComment(Comment $com): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // get current user
        $currentUserId = $this->getUser()->getId();
        // get post Id
        $postId = $com->getPost()->getId();

        if ($currentUserId === $com->getUser()->getId()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($com);
            $entityManager->flush();

            $this->addFlash('message', 'Votre commentaire a bien ??t?? supprim??.');
            return $this->redirectToRoute('show_post', [
                'id' => $postId
            ]);
        } else {
            $this->addFlash('message', 'Vous n\'??tes pas autoris??.e ?? supprimer ce commentaire.');
            return $this->redirectToRoute('show_post', [
                'id' => $postId
            ]);
        }
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

            return new JsonResponse(['error' => 'Erreur acc??s refus??'], 400);
        }
    }

    /**
     * @Route("/deletePost/{id}", name="delete_post")
     */
    public function deletePost(Post $post): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        if ($this->getUser()->getId() === $post->getUser()->getId()) {


            // get all pictures
            $pictures = $post->getPhotos();

            if ($pictures) {

                foreach ($pictures as $picture) {
                    // get the picture path
                    $pictureName = $this->getParameter("images_directory") . '/' . $picture->getName();

                    // delete this picture if this file exists
                    if (file_exists($pictureName)) {
                        unlink($pictureName);
                    }
                }
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();

            // flash message
            $this->addFlash('message', 'Votre annonce a bien ??t?? supprim??e.');
            return $this->redirectToRoute('my_posts');
        } else {
            return $this->redirectToRoute('home');
        }
    }
}
