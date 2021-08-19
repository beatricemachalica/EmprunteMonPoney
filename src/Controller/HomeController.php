<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            // 'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/conditions-utilisation", name="conditions_utilisation")
     */
    public function conditions(): Response
    {
        return $this->render('home/conditions.html.twig', []);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $contactFormData = $form->getData();

            $message = (new Email())
                ->from($contactFormData['email'])
                ->to('empruntemonponey@gmail.com')
                ->subject('Prise de contact')
                ->text(
                    'Contact : ' . $contactFormData['email'] . \PHP_EOL .
                        $contactFormData['message'],
                    'text/plain'
                );

            $mailer->send($message);

            // flash message
            $this->addFlash('message', 'Votre message a bien été envoyé');
            return $this->redirectToRoute('contact');
        }

        return $this->render('home/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
