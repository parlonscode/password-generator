<?php

namespace App\Service;

class PasswordGenerator
{
    public function generate(int $length, bool $uppercaseLetters = false, bool $digits = false, bool $specialCharacters = false): string
    {
        $lowercaseLettersAlphabet = range('a', 'z');
        $uppercaseLettersAlphabet = range('A', 'Z');
        $digitsAlphabet = range(0, 9);
        $specialCharactersAlphabet = array_merge(
            range('!', '/'),
            range(':', '@'),
            range('[', '`'),
            range('{', '~'),
        );

        $finalAlphabet = [$lowercaseLettersAlphabet];

        // On rajoute une lettre en miniscule choisie de manière aléatoire
        $password = [$this->pickRandomItemFromAlphabet($lowercaseLettersAlphabet)];
        
        $constraintMapping = [
            [$uppercaseLetters, $uppercaseLettersAlphabet],
            [$digits, $digitsAlphabet],
            [$specialCharacters, $specialCharactersAlphabet]
        ];

        foreach ($constraintMapping as [$constraintEnabled, $constraintAlphabet]) {
            if ($constraintEnabled) {
                $finalAlphabet[] = $constraintAlphabet;

                $password[] = $this->pickRandomItemFromAlphabet($constraintAlphabet);
            }
        }

        $finalAlphabet = array_merge(...$finalAlphabet);

        $numberOfCharactersRemaining = $length - count($password);

        for ($i = 0; $i < $numberOfCharactersRemaining; $i++) { 
            $password[] = $this->pickRandomItemFromAlphabet($finalAlphabet);
        }

        $password = $this->secureShuffle($password);

        return implode('', $password);
    }

    private function pickRandomItemFromAlphabet(array $alphabet): string
    {
        return $alphabet[random_int(0, count($alphabet) - 1)];
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
}
