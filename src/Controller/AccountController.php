<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Equid;
use App\Form\EquidType;
use App\Form\UserAccountType;
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
        return $this->render('account/index.html.twig', [
            'user' => $this->getUser()->getUsername(),
        ]);
    }

    /**
     * @Route("/account/edit/{id}", name="user_edit")
     */
    public function editAccount(Request $request, User $user = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$user) {
            $user = new User();
        }

        $form = $this->createForm(UserAccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // flash message
            $this->addFlash('message', 'Votre profil a bien été mis à jour.');
            return $this->redirectToRoute('user_account');
        }

        return $this->render('account/editAccount.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/delete/{id}", name="user_delete")
     */
    public function deleteUserAccount(User $user): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        // $entityManager = $this->getDoctrine()->getManager();
        // $entityManager->remove($user);
        // $entityManager->flush();

        $user = $this->getUser();
        $this->container->get('security.token_storage')->setToken(null);

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @IsGranted("ROLE_PROPRIO")
     * @Route("/account/equid", name="user_equid")
     */
    public function showEquid(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        return $this->render('account/equid/myEquid.html.twig', []);
    }

    /**
     * @IsGranted("ROLE_PROPRIO")
     * @Route("/newEquid", name="add_equid")
     * @Route("/editEquid/{id}", name="edit_equid")
     */
    public function new(Request $request, Equid $equid = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$equid) {
            $equid = new Equid();
        }

        $form = $this->createForm(EquidType::class, $equid);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // flash message
            $idEquid = $equid->getId();
            if ($idEquid == null) {
                $this->addFlash('message', 'Votre cheval a bien été enregistré.');
            } else {
                $this->addFlash('message', 'Les informations de votre cheval ont bien été modifiées.');
            }

            // set the authenticated user (the owner)
            $equid->setUser($this->getUser());

            $equid = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($equid);
            $entityManager->flush();

            return $this->redirectToRoute('user_equid');
        }

        return $this->render('account/equid/newEquid.html.twig', [
            'form' => $form->createView(),
            'editMode' => $equid->getId() !== null
        ]);
    }

    /**
     * @IsGranted("ROLE_PROPRIO")
     * @Route("/deleteEquid/{id}", name="delete_equid")
     */
    public function delete(Equid $equid): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($equid);
        $entityManager->flush();

        return $this->redirectToRoute('user_equid');
    }
}
