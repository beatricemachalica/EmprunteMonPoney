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

            // checking the captcha
            if(empty($_POST['recaptcha-response'])){
                
                // if recaptcha token is empty redirect to the home page
                // first protection against bots
                return $this->redirectToRoute('home');
            } else {

                // if a special kind of bots manage to file these form inputs, we have to check the response
                $url = "https://www.google.com/recaptcha/api/siteverify?secret=6LcBG1IcAAAAAFYSBJ69tsvnXaNO4KXD6sHCMGDX&response={$_POST['recaptcha-response']}";

                if(function_exists('curl_version')){
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_HEADER, false);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_TIMEOUT, 1);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($curl);
                } else {
                    $response = file_get_contents($url); 
                }
                // dd(json_decode($response)); // gives an object
                
                // check the response
                if(empty($response) || is_null($response)){
                    // if the response is empty or is null
                    return $this->redirectToRoute('home');
                } else {
                    $data = json_decode($response);

                    if($data->success){
                      
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
                    
                    } else {
                        // if the response is empty os is null
                        return $this->redirectToRoute('home');
                    }
                }
            };
        }

        return $this->render('home/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
