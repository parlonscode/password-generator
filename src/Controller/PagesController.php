<?php

namespace App\Controller;

use App\Service\PasswordGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PagesController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(Request $request): Response
    {
        return $this->render('pages/home.html.twig', [
            'password_default_length' => $request->getSession()->get('app.length', $this->getParameter('app.password_default_length')),
            'password_min_length' => $this->getParameter('app.password_min_length'),
            'password_max_length' => $this->getParameter('app.password_max_length')
        ]);
    }

    #[Route('/generate-password', name: 'app_generate_password')]
    public function generatePassword(Request $request, PasswordGenerator $passwordGenerator): Response
    {
        // We make sure that the password length is always 
        // at minimum {app.password_min_length} 
        // and at maximum {app.password_max_length}.
        $length = max(
            min($request->query->getInt('length'), $this->getParameter('app.password_max_length')),
            $this->getParameter('app.password_min_length')
        );
        $uppercaseLetters = $request->query->getBoolean('uppercase_letters');
        $digits = $request->query->getBoolean('digits');
        $specialCharacters = $request->query->getBoolean('special_characters');

        $session = $request->getSession();

        $session->set('app.length', $length);
        $session->set('app.uppercaseLetters', $uppercaseLetters);
        $session->set('app.digits', $digits);
        $session->set('app.specialCharacters', $specialCharacters);

        $password = $passwordGenerator->generate(
            $length,
            $uppercaseLetters,
            $digits,
            $specialCharacters,
        );

        return $this->render('pages/password.html.twig', compact('password'));
    }
}
