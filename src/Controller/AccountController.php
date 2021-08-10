<?php

namespace App\Controller;

use App\Entity\User;
// use App\Form\UserAccountType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="user_account")
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
            'user' => $this->getUser()->getUsername(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="user_edit")
     */
    public function new(Request $request, User $user = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$user) {
            $user = new User();
        }

        $form = $this->createForm(UserAccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            // $user->setPassword(
            //     $passwordEncoder->encodePassword(
            //         $user,
            //         $form->get('plainPassword')->getData()
            //     )
            // );
            $user = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/editAccount.html.twig', [
            'formEditUser' => $form->createView(),
        ]);
    }
}
