<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 * Require ROLE_ADMIN for every controller methods in this class
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_home")
     */
    public function adminPanel(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('admin/index.html.twig', [
            'actual' => $this->getUser()->getPseudo(),
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function allAccounts(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('admin/users/users.html.twig', [
            'users' => $users,
        ]);
    }

    /** 
     * @Route("/admin/users/new", name="add_user")
     * @Route("/admin/users/edit/{id}", name="edit_user")
     */
    public function newUser(Request $request, User $user = null, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$user) {
            $user = new User();
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // flash message
            $idUser = $user->getId();
            if ($idUser == null) {
                // $this->addFlash('message', 'L\'utilisateur a bien été enregistré.');
            } else {
                $this->addFlash('message', 'Les informations de l\'utilisateur ont bien été modifiées.');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/newUser.html.twig', [
            'form' => $form->createView(),
            // 'editMode' => $user->getId() !== null
        ]);
    }


    /**
     * @Route("/admin/users/delete/{id}", name="delete_user")
     */
    public function deleteUser(User $user): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        // flash message
        $this->addFlash('message', 'L\'utilisateur a bien été supprimé.');
        return $this->redirectToRoute('admin_users');
    }

    /**
     * @Route("/admin/categories", name="admin_categories")
     */
    public function allCategories(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('admin/categories/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    /** 
     * @Route("/admin/categories/new", name="add_category")
     * @Route("/admin/categories/edit/{id}", name="edit_category")
     */
    public function newCategory(Request $request, Category $category = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$category) {
            $category = new Category();
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // flash message
            $idCategory = $category->getId();
            if ($idCategory == null) {
                $this->addFlash('message', 'La nouvelle catégorie a bien été enregistrée.');
            } else {
                $this->addFlash('message', 'La catégorie a bien été modifiée.');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/categories/newCategory.html.twig', [
            'form' => $form->createView(),
            'editMode' => $category->getId() !== null
        ]);
    }


    /**
     * @Route("/admin/categories/delete/{id}", name="delete_categoy")
     */
    public function deleteCategory(Category $category): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // deny the access if the user is not completely authenticated

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        // flash message
        $this->addFlash('message', 'La catégorie a bien été supprimée.');
        return $this->redirectToRoute('admin_categories');
    }
}
