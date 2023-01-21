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
        // Alphabets
        $lowercaseLettersAlphabet = range('a', 'z');
        $uppercaseLettersAlphabet = range('A', 'Z');
        $digitsAlphabet = range(0, 9);
        $specialCharactersAlphabet = str_split('!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~');

        # Password alphabet defaults to all lowercase letters alphabet
        $passwordAlphabet = $lowercaseLettersAlphabet;

        # Start by adding a lowercase letter
        $password = $this->randomItemFromAlphabet($lowercaseLettersAlphabet);

        # We make sure that the final password contains at least
        # one {uppercase letter and/or digit and/or special character}
        # based on user's requested constraints.
        # We also grow at the same time the password alphabet with 
        # the alphabet of the requested constraint.

        if ($uppercaseLetters) {
            $passwordAlphabet = array_merge($passwordAlphabet, $uppercaseLettersAlphabet);
            $password .= $this->randomItemFromAlphabet($uppercaseLettersAlphabet);
        }
        
        if ($digits) {
            $passwordAlphabet = array_merge($passwordAlphabet, $digitsAlphabet);
            $password .= $this->randomItemFromAlphabet($digitsAlphabet);
        }

        if ($specialCharacters) {
            $passwordAlphabet = array_merge($passwordAlphabet, $specialCharactersAlphabet);
            $password .= $this->randomItemFromAlphabet($specialCharactersAlphabet);
        }

        $numberOfCharactersRemaining = $length - mb_strlen($password);

        for ($i = 0; $i < $numberOfCharactersRemaining; $i++) {
            $password .= $this->randomItemFromAlphabet($passwordAlphabet);
        }
        
        # We do a shuffle at the end to make the order 
        # of the final password characters unpredictable
        return $this->randomizer->shuffleBytes($password);
    }

    private function randomItemFromAlphabet(array $alphabet): string
    {
        return $alphabet[$this->randomizer->getInt(0, count($alphabet) - 1)];
    }
}
