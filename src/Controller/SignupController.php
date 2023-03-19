<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignupFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
/**
 * This controller handles sign up
 */
class SignupController extends AbstractController
{
    #[Route('/signup', name: 'app_signup')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, FormLoginAuthenticator $formLoginAuthenticator): Response
    {   // Don't let logged in users create a new account
        if ($this->getUser() !== null) {
            return $this->redirectToRoute('app_home');
        }
        $user = new User();
        $form = $this->createForm(SignupFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            $userAuthenticator->authenticateUser(
                $user,
                $formLoginAuthenticator,
                $request,
            );
            return $this->redirectToRoute('app_home');
        }

        return $this->render('signup/index.html.twig', [
            'signupForm' => $form->createView(),
            'headerText' => SignupFormType::getActionText(),
        ]);
    }
}