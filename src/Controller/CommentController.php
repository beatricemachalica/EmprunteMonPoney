<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment", name="comment")
     */
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    /**
     * @Route("/editComment/{id}", name="comment_edit")
     */
    public function editComment(Request $request, Comment $com): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // create form
        $form = $this->createForm(CommentType::class, $com);

        // get information from $request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($com);
            $entityManager->flush();

            // flash message
            $this->addFlash('message', 'Votre commentaire a bien été mis à jour.');
            return $this->redirectToRoute('show_post', [
                'id' => $com->getPost()->getId()
            ]);
        }

        return $this->render('comment/editComment.html.twig', [
            'form' => $form->createView(),
        ]);
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

        if ($currentUserId == $com->getUser()->getId()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($com);
            $entityManager->flush();
        } else {
            $this->addFlash('message', 'Vous n\'êtes pas autorisé.e à supprimer ce commentaire.');
        }

        $this->addFlash('message', 'Votre commentaire a bien été supprimé.');
        return $this->redirectToRoute('show_post', [
            'id' => $postId
        ]);
    }
}
