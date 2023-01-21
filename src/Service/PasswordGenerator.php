<?php

namespace App\Service;

use Random\Randomizer;

class PasswordGenerator
{
    private readonly Randomizer $randomizer;

    public function __construct()
    {
        $this->randomizer = new Randomizer;
    }

    public function generate(
        int $length,
        bool $uppercaseLetters = false,
        bool $digits = false,
        bool $specialCharacters = false
    ): string
    {
        // Define Alphabets
        $lowercaseLettersAlphabet = 'abcdefghijklmnopqrstuvwxyz';
        $uppercaseLettersAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digitsAlphabet = '0123456789';
        $specialCharactersAlphabet = '!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~';

        # Final alphabet defaults to all lowercase letters alphabet
        $alphabet = $lowercaseLettersAlphabet;

        # Start by adding a lowercase letter
        $password = $this->randomCharFromString($lowercaseLettersAlphabet);

        # We make sure that the final password contains at least
        # one {uppercase letter and/or digit and/or special character}
        # based on user's requested constraints.
        # We also grow at the same time the final alphabet with 
        # the alphabet of the requested constraint.

        if ($uppercaseLetters) {
            $alphabet .= $uppercaseLettersAlphabet;
            $password .= $this->randomCharFromString($uppercaseLettersAlphabet);
        }
        
        if ($digits) {
            $alphabet .= $digitsAlphabet;
            $password .= $this->randomCharFromString($digitsAlphabet);
        }

        if ($specialCharacters) {
            $alphabet .= $specialCharactersAlphabet;
            $password .= $this->randomCharFromString($specialCharactersAlphabet);
        }

        $numberOfCharactersRemaining = $length - mb_strlen($password);

        for ($i = 0; $i < $numberOfCharactersRemaining; $i++) {
            $password .= $this->randomCharFromString($alphabet);
        }
        
        # We do a shuffle to make the password characters order unpredictable
        return $this->randomizer->shuffleBytes($password);
    }

    private function randomCharFromString(string $str): string
    {
        return substr($this->randomizer->shuffleBytes($str), 0, 1);
    }
}
