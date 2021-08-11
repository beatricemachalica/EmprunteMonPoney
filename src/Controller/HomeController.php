<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/a-propos", name="a_propos")
     */
    public function apropos(): Response
    {
        return $this->render('home/apropos.html.twig', []);
    }
}
