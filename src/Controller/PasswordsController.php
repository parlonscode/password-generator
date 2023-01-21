<?php

namespace App\Controller;

use App\Form\PasswordType;
use DateTimeImmutable;
use App\Utils\RangeLimiter;
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
        $form = $this->createForm(PasswordType::class, [
            'length' => $request->cookies->getInt(
                'app_length', $this->getParameter('app.password_default_length')
            ),
            'uppercaseLetters' => $request->cookies->getBoolean('app_uppercase_letters', false),
            'digits' => $request->cookies->getBoolean('app_digits', false),
            'specialCharacters' => $request->cookies->getBoolean('app_length', false),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
        }

        return $this->render('passwords/create.html.twig', compact('form'));
    }

    #[Route('/generate-password', name: 'app_generate_password', methods: ['POST'])]
    public function generatePassword(Request $request): Response
    {
        $length = RangeLimiter::clamp(
            $request->query->getInt('length'),
            $this->getParameter('app.password_min_length'),
            $this->getParameter('app.password_max_length')
        );
        $uppercaseLetters = $request->query->getBoolean('uppercase_letters');
        $digits = $request->query->getBoolean('digits');
        $specialCharacters = $request->query->getBoolean('special_characters');

        $password = PasswordGenerator::generate(
            $length,
            $uppercaseLetters,
            $digits,
            $specialCharacters,
        );

        $response = $this->render('pages/password.html.twig', compact('password'));

        $this->saveUserPasswordRequirements(
            $response, $length, $uppercaseLetters, $digits, $specialCharacters
        );

        return $response;
    }

    private function saveUserPasswordRequirements(Response $response, int $length, bool $uppercaseLetters, bool $digits, bool $specialCharacters): void
    {
        $fiveYearsFromNow = new DateTimeImmutable('+5 years');

        $response->headers->setCookie(
            new Cookie('app_length', $length, $fiveYearsFromNow)
        );

        $response->headers->setCookie(
            new Cookie('app_uppercase_letters', $uppercaseLetters ? '1' : '0', $fiveYearsFromNow)
        );

        $response->headers->setCookie(
            new Cookie('app_digits', $digits ? '1' : '0', $fiveYearsFromNow)
        );

        $response->headers->setCookie(
            new Cookie('app_special_characters', $specialCharacters ? '1' : '0', $fiveYearsFromNow)
        );
    }
}
