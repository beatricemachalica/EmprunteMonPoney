<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @IsGranted("ROLE_USER")
     * Require ROLE_USER in order to update a password
     * 
     * @Route("/passwordUpdate", name="update_password")
     */
    public function update_user_password(EntityManagerInterface $manager, Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        // $this->getUser get the connected user
        $user = $this->getUser();

        $passwordUpdate = new PasswordUpdate;

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // on va vérifier que l'ancien mdp du formulaire correspond bien à celui de l'utilisateur
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())) {
                // message d'erreur
                $form->get('old_password')->addError(new FormError("Le mot de passe est incorrect."));
            } else {

                $newPassword = $passwordUpdate->getNewPassword();
                $password = $passwordHasher->hashPassword($user, $newPassword);

                $user->setPassword($password);

                $manager->persist($user);
                $manager->flush();

                // message add flash de confirmation
                $this->addFlash('message', 'Le mot de passe a bien été modifié.');
                return $this->redirectToRoute('user_account');
            }
        }
        return $this->render('security/updatePassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
