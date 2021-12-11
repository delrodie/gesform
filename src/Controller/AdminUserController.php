<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user")
 */
class AdminUserController extends AbstractController
{
	private $hasher;
	
	public function __construct(UserPasswordHasherInterface $hasher)
	{
		$this->hasher = $hasher;
	}
	
    /**
     * @Route("/", name="admin_user_index", methods={"GET", "POST"})
     */
    public function index(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
	    $user = new User();
	    $form = $this->createForm(UserType::class, $user);
	    $form->handleRequest($request);
	
	    if ($form->isSubmitted() && $form->isValid()) {
			$password = $this->hasher->hashPassword($user, $user->getPassword());
			$user->setPassword($password);
		    $entityManager->persist($user);
		    $entityManager->flush();
		
		    return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
	    }
		//dd($userRepository->findAll());
        return $this->renderForm('admin_user/index.html.twig', [
            'users' => $userRepository->findAll(),
	        'user' => $user,
	        'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="admin_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('admin_user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
	        $password = $this->hasher->hashPassword($user, $user->getPassword());
	        $user->setPassword($password);
            $entityManager->flush();

            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
	        'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
