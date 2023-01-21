<?php

namespace App\Controller;

use App\Form\PasswordRequirementsType;
use App\Model\PasswordRequirements;
use DateTimeImmutable;
use App\Utils\PasswordGenerator;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PasswordsController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(PasswordRequirementsType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $passwordRequirements = $form->getData();

            $request->getSession()->set('app_password_requirements', $passwordRequirements);
    
            return $this->redirectToRoute('app_passwords_show');
        }

        return $this->render('passwords/create.html.twig', compact('form'));
    }

    #[Route('/password-generated', name: 'app_passwords_show', methods: ['GET'])]
    public function show(Request $request): Response
    {
        $passwordRequirements = $request->getSession()->get('app_password_requirements');

        if (!$passwordRequirements) {
            return $this->redirectToRoute('app_home');
        }

        $password = PasswordGenerator::fromPasswordRequirements($passwordRequirements);

        $response = $this->render('passwords/show.html.twig', compact('password'));

        $this->savePasswordRequirements($response, $passwordRequirements);
    
        return $response;
    }

    private function savePasswordRequirements(
        Response $response, PasswordRequirements $passwordRequirements
    ): void
    {
        $fiveYearsFromNow = new DateTimeImmutable('+5 years');

        $response->headers->setCookie(
            new Cookie('app_length', $passwordRequirements->getLength(), $fiveYearsFromNow)
        );

        $response->headers->setCookie(
            new Cookie('app_uppercase_letters', $passwordRequirements->getUppercaseLetters() ? '1' : '0', $fiveYearsFromNow)
        );

        $response->headers->setCookie(
            new Cookie('app_digits', $passwordRequirements->getDigits() ? '1' : '0', $fiveYearsFromNow)
        );

        $response->headers->setCookie(
            new Cookie('app_special_characters', $passwordRequirements->getSpecialCharacters() ? '1' : '0', $fiveYearsFromNow)
        );
    }
}
