<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PagesController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('pages/home.html.twig');
    }

    #[Route('/generate-password', name: 'app_generate_password')]
    public function generatePassword(Request $request): Response
    {
        $length = $request->query->getInt('length');
        $uppercaseLetters = $request->query->getBoolean('uppercase_letters');
        $digits = $request->query->getBoolean('digits');
        $specialCharacters = $request->query->getBoolean('special_characters');

        $lowercaseLettersAlphabet = range('a', 'z');
        $uppercaseLettersAlphabet = range('A', 'Z');
        $digitsAlphabet = range(0, 9);
        $specialCharactersAlphabet = array_merge(
            range('!', '/'),
            range(':', '@'),
            range('[', '`'),
            range('{', '~'),
        );

        $finalAphabet = $lowercaseLettersAlphabet;

        // On rajoute une lettre en miniscule choisie de manière aléatoire
        $password = [$this->pickRandomItemFromAlphabet($lowercaseLettersAlphabet)];

        if ($uppercaseLetters) {
            $finalAphabet = array_merge($finalAphabet, $uppercaseLettersAlphabet);

            // On rajoute une lettre en majuscule choisie de manière aléatoire
            $password[] = $this->pickRandomItemFromAlphabet($uppercaseLettersAlphabet);
        }

        if ($digits) {
            $finalAphabet = array_merge($finalAphabet, $digitsAlphabet);

            // On rajoute un chiffre choisi de manière aléatoire
            $password[] = $this->pickRandomItemFromAlphabet($digitsAlphabet);
        }

        if ($specialCharacters) {
            $finalAphabet = array_merge($finalAphabet, $specialCharactersAlphabet);

            // On rajoute un caractère spécial choisi de manière aléatoire
            $password[] = $this->pickRandomItemFromAlphabet($specialCharactersAlphabet);
        }

        $numberOfCharactersRemaining = $length - count($password);

        for ($i = 0; $i < $numberOfCharactersRemaining; $i++) { 
            $password[] = $this->pickRandomItemFromAlphabet($finalAphabet);
        }

        $password = $this->secureShuffle($password);

        $password = implode('', $password);

        return $this->render('pages/password.html.twig', compact('password'));
    }

    private function secureShuffle(array $arr): array
    {
        // Source: https://github.com/lamansky/secure-shuffle/blob/master/src/functions.php
        $length = count($arr);

        for ($i = $length - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            $temp = $arr[$i];
            $arr[$i] = $arr[$j];
            $arr[$j] = $temp;
        }

        return $arr;
    }

    private function pickRandomItemFromAlphabet(array $alphabet): string
    {
        return $alphabet[random_int(0, count($alphabet) - 1)];
    }
}
