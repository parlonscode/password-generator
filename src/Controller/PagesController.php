<?php

namespace App\Controller;

use App\Service\PasswordGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PagesController extends AbstractController
{
    const PASSWORD_DEFAULT_LENGTH = 12;
    const PASSWORD_MIN_LENGTH = 8;
    const PASSWORD_MAX_LENGTH = 60;

    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('pages/home.html.twig', [
            'password_default_length' => PagesController::PASSWORD_DEFAULT_LENGTH,
            'password_min_length' => PagesController::PASSWORD_MIN_LENGTH,
            'password_max_length' => PagesController::PASSWORD_MAX_LENGTH
        ]);
    }

    #[Route('/generate-password', name: 'app_generate_password')]
    public function generatePassword(Request $request, PasswordGenerator $passwordGenerator): Response
    {
        $length = max(
            min($request->query->getInt('length'), PagesController::PASSWORD_MAX_LENGTH),
            PagesController::PASSWORD_MIN_LENGTH
        );

        $password = $passwordGenerator->generate(
            $length,
            $request->query->getBoolean('uppercase_letters'),
            $request->query->getBoolean('digits'),
            $request->query->getBoolean('special_characters'),
        );

        return $this->render('pages/password.html.twig', compact('password'));
    }
}
