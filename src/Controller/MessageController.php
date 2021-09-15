<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/send/{id}", name="send_message")
     */
    public function sendMessage(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        // create a new message object
        $message = new Message;

        // create a message form
        $form = $this->createForm(MessageType::class, $message);

        // get the form from request
        $form->handleRequest($request);

        // get the recipient (post author)
        $recipient = $post->getUser()->getPseudo();

        if ($form->isSubmitted() && $form->isValid()) {

            // set automatically the sender (current user) of this message
            $message->setSender($this->getUser());

            // set automatically the recipient
            $message->setRecipient($post->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            // add flash message
            $this->addFlash("message", "Votre message a bien été envoyé.");
            return $this->redirectToRoute('show_post', [
                'id' => $post->getId()
            ]);
        }

        return $this->render("message/sendNewMessage.html.twig", [
            "form" => $form->createView(),
            "recipient" => $recipient
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/received", name="received")
     */
    public function received(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        return $this->render('message/received.html.twig');
    }


    /**
     * @IsGranted("ROLE_USER")
     * @Route("/sent", name="sent")
     */
    public function sent(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        return $this->render('message/sent.html.twig');
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/read/{id}", name="read")
     */
    public function readMessage(Message $message): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        // set "is read" to this message
        $message->setIsRead(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        return $this->render('message/read.html.twig', compact("message"));
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/delete/{id}", name="delete")
     */
    public function deleteMessage(Message $message): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        // get the current user
        $user = $this->getUser()->getId();

        // if the current user want to delete a sent message
        if ($user === $message->getSender()->getId()) {

            // set "deleted_sender"
            $message->setDeletedSender(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();
        } elseif ($user === $message->getRecipient()->getId()) {
            // if the current user want to delete a received message

            // set "deleted_recipient"       
            $message->setDeletedRecipient(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();
        } else {
            return $this->redirectToRoute('home');
        }

        if (($message->getDeletedSender() == true) && ($message->getDeletedRecipient() == true)) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($message);
            $em->flush();
        }

        return $this->redirectToRoute("received");
    }
}
