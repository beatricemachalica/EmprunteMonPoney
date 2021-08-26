<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    /**
     * @Route("/messages", name="messages")
     */
    public function index(): Response
    {
        return $this->render('message/index.html.twig', []);
    }

    /**
     * @Route("/send", name="send_message")
     */
    public function sendMessage(Request $request): Response
    {
        // create a new message object
        $message = new Message;

        // create a message form
        $form = $this->createForm(MessageType::class, $message);

        // get the form from request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // set automatically the sender (current user) of this message
            $message->setSender($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            // add flash message
            $this->addFlash("message", "Votre message a bien été envoyé.");
            return $this->redirectToRoute("messages");
        }

        return $this->render("message/sendNewMessage.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/received", name="received")
     */
    public function received(): Response
    {
        return $this->render('message/received.html.twig');
    }


    /**
     * @Route("/sent", name="sent")
     */
    public function sent(): Response
    {
        return $this->render('message/sent.html.twig');
    }

    /**
     * @Route("/read/{id}", name="read")
     */
    public function readMessage(Message $message): Response
    {
        // set "is read" to this message
        $message->setIsRead(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        return $this->render('message/read.html.twig', compact("message"));
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function deleteMessage(Message $message): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($message);
        $em->flush();

        return $this->redirectToRoute("received");
    }
}
