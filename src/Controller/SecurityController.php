<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

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
     * @Route("/security/forgotten_Password", name="forgotten_password")
     */
    public function forgottenPassword(User $user = null, EntityManagerInterface $manager, Request $request, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, UserRepository $userRepository): Response
    {
        // on va instancier la variable form
        $form = $this->createForm(ForgottenPasswordType::class);

        $form->handleRequest($request);

        $email = $form->get('emailResetPassword')->getData();
        $user = $userRepository->findOneByEmail($email);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->isMethod('POST')) {

                // on génère un token unique
                $token = $tokenGenerator->generateToken();
                try {
                    $user->setResetToken($token);
                    $manager->flush();
                } catch (\Exception $e) {
                    $this->addFlash('Warning', $e->getMessage());
                    return $this->redirectToRoute('app_login');
                }
                // on génère une URL ayant une route qui va permettre de changer le mot de passe
                $url = $this->generateUrl('resetPassword', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

                // on envoie le mail
                $message = (new Email())
                    ->from('dogacademytestsymfony@gmail.com')
                    ->to($user->getEmail())
                    ->subject('DogAcadémie mot de passe oublié')
                    ->text("Voici le lien de récupération de votre mot de passe : " . $url, 'text/html')
                    ->html("<p> Ceci est un test : " . $url, 'text/html' . "</p>");

                $mailer->send($message);
                $this->addFlash('info', 'Le mail de récupération du mot de passe a bien été envoyé.');
            }
        }

        return $this->render('security/forgottenPassword.html.twig', [
            'form' => $form->createView(),
            'titre' => "Réinitialisation du mot de passe"
        ]);
    }

    /**
     * Mot de passe oublié
     * 
     * @Route("resetPassword/{token}", name="resetPassword")
     */
    public function resetPassword(User $user = null, EntityManagerInterface $manager, Request $request, string $token, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        // on va redéfinir le user
        $user = $userRepository->findOneByResetToken($token);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->isMethod('POST')) {

                // on va effacer le token car il va être utilisé pour reset le mdp
                $user->setResetToken(NULL);
                // on récupère le mdp dans l'input du form
                $newPassword = $form->get('password')->getData();
                // on set le nouveau password
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $newPassword)
                );
                $manager->flush();
                // message add flash de confirmation
                $this->addFlash('info', 'Votre mot de passe a bien été réinitialisé.');
                return $this->redirectToRoute('app_login');
            }
        }
        return $this->render('security/resetPassword.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
            'title' => "Réinitialisation du mot de passe."
        ]);
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
                $this->addFlash('info', 'Le mdp a bien été changé.');
                return $this->redirectToRoute('user_index');
            }
        }
        return $this->render('security/updatePassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
