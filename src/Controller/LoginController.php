<?php

namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\FormError;
/**
 * This controller handles logging in
 */
class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
     public function login(AuthenticationUtils $authenticationUtils): Response
    {
         // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

         // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginFormType::class);

        if ($error && $error->getMessageKey() == 'Invalid credentials.') {
            $form->addError(new FormError('Your username or password is incorrect'));
        } elseif ($error) {
            $form->addError(new FormError($error->getMessageKey()));
        }
        $formView = $form->createView();

        return $this->render('login/index.html.twig', [
             'lastUsername'  => $lastUsername,
             'loginForm'     => $formView,
             'headerText'    => LoginFormType::getActionText(),
        ]);
    }
}