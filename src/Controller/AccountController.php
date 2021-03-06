<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Equid;
use App\Form\EquidType;
use App\Form\UserAccountType;
use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 * Require ROLE_USER for every controller methods in this class
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="user_account")
     */
    public function showAccount(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        // get user id
        $userId = $this->getUser()->getId();

        // get user's horses
        $horses = $this->getDoctrine()
            ->getRepository(Equid::class)
            ->findBy(array('user' => $userId), null);

        return $this->render('account/index.html.twig', [
            'user' => $this->getUser()->getUsername(),
            'horses' => $horses,
        ]);
    }

    /**
     * @Route("/account/edit/{id}", name="user_edit")
     */
    public function editAccount(Request $request, User $user = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        if ($this->getUser()->getId() === $user->getId()) {

            $form = $this->createForm(UserAccountType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $user = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                // flash message
                $this->addFlash('message', 'Votre profil a bien ??t?? mis ?? jour.');
                return $this->redirectToRoute('user_account');
            }

            return $this->render('account/editAccount.html.twig', [
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/account/delete/{id}", name="user_delete")
     */
    public function deleteUserAccount(User $user): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        if ($this->getUser()->getId() === $user->getId()) {

            // set token to null or it will throw an error 
            $user = $this->getUser();
            $this->container->get('security.token_storage')->setToken(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @IsGranted("ROLE_PROPRIO")
     * @Route("/newEquid", name="add_equid")
     * @Route("/editEquid/{id}", name="edit_equid")
     */
    public function newEquid(Request $request, Equid $equid = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        if ((!$equid) || (($this->getUser()->getId() === $equid->getUser()->getId()))) {


            if (!$equid) {
                $equid = new Equid();
            }

            $form = $this->createForm(EquidType::class, $equid);

            $form->handleRequest($request);
            // handleRequest() = read data off of the correct PHP superglobals (i.e. $_POST or $_GET) 
            // based on the HTTP method configured on the form (POST is default).

            if ($form->isSubmitted() && $form->isValid()) {

                // flash message
                $idEquid = $equid->getId();
                if ($idEquid == null) {
                    $this->addFlash('message', 'Votre cheval a bien ??t?? enregistr??.');
                } else {
                    $this->addFlash('message', 'Les informations de votre cheval ont bien ??t?? modifi??es.');
                }

                // set the authenticated user (the owner)
                $equid->setUser($this->getUser());

                $equid = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($equid);
                $entityManager->flush();

                return $this->redirectToRoute('user_account');
            }

            return $this->render('account/equid/newEquid.html.twig', [
                'form' => $form->createView(),
                'editMode' => $equid->getId() !== null
            ]);
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @IsGranted("ROLE_PROPRIO")
     * @Route("/deleteEquid/{id}", name="delete_equid")
     */
    public function deleteEquid(Equid $equid): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        if ($this->getUser()->getId() === $equid->getUser()->getId()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($equid);
            $entityManager->flush();

            return $this->redirectToRoute('user_account');
        } else {
            return $this->redirectToRoute('home');
        }
    }
}
