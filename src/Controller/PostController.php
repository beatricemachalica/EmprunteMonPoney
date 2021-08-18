<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }
    /**
     * @Route("/post", name="my_post")
     */
    public function showPost(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findAll();

        return $this->render('post/showMyPost.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/new", name="add_post")
     * @Route("/edit/{id}", name="edit_post")
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

            // get the authenticated user
            $post->setUser($this->getUser());
            // $post->setActive(false);

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

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
            'editMode' => $post->getId() !== null
        ]);
    }

    /**
     * @Route("/delete/{id}", name="Post_delete")
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
