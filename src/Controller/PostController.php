<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Photo;
use App\Entity\Post;
use App\Form\PostType;
use PHPUnit\Util\Json;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostController extends AbstractController
{
    /**
     * @Route("/posts", name="posts")
     */
    public function index(): Response
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findAll();

        // récupérer l'id (objet proxy) pour afficher les informations

        return $this->render('post/index.html.twig', [
            'cards' => $posts,
        ]);
    }

    /**
     * @Route("/post", name="my_post")
     */
    public function showPost(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        return $this->render('post/myPost.html.twig', []);
    }

    /**
     * @Route("/post/activate/{id}", name="activate_post")
     * this method allow to activate or not a post
     */
    public function activatePost(Post $post)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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

        if (!$post) {
            $post = new Post();
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // set the authenticated user
            $post->setUser($this->getUser());

            // set the active attribut to true
            // by default the post will be activated and will appear on the website
            $post->setActive(true);

            // get the owner's horse
            $usersHorse = $this->getUser()->getEquid();

            // get the array of roles
            $userRoles = $this->getUser()->getRoles();

            // get the categories
            $categoryEmprunt = $this->getDoctrine()
                ->getRepository(Category::class)
                ->findOneBy(array('name' => 'profil d\'emprunteur'), null);

            $categoryProprio = $this->getDoctrine()
                ->getRepository(Category::class)
                ->findOneBy(array('name' => 'profil d\'un cheval'), null);

            // if the user is a borrower ("ROLE_EMPRUNT")
            if (in_array("ROLE_EMPRUNT", $userRoles)) {
                // set the right category
                $post->setCategory($categoryEmprunt);
            }

            // if the user is an owner ("ROLE_PROPRIO") and his horse has been correctly registered
            if ((in_array("ROLE_PROPRIO", $userRoles)) && ($usersHorse !== null)) {
                // set the right category
                $post->setCategory($categoryProprio);
                // set the equid
                $post->setEquid($this->getUser()->getEquid());
            } elseif ($usersHorse == null) {
                // if the owner has fogotten to register his horse first
                $this->addFlash('error', "Veuillez inscrire votre cheval avant de créer une annonce.");
            }

            // pictures
            $pictures = $form->get('photo')->getData();
            // for each new picture added by the user
            foreach ($pictures as $picture) {
                // create a new name file and set the right extension
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

            // flash message
            $idPost = $post->getId();
            if ($idPost == null) {
                $this->addFlash('message', 'L\'annonce a bien été enregistrée.');
            } else {
                $this->addFlash('message', 'Votre annonce a bien été modifiée.');
            }

            $post = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('my_post');
        }

        return $this->render('post/newPost.html.twig', [
            'form' => $form->createView(),
            'editMode' => $post->getId() !== null
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

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        // flash message
        $this->addFlash('message', 'Votre annonce a bien été supprimée.');
        return $this->redirectToRoute('my_post');
    }
}
