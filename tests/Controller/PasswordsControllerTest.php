<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PasswordsControllerTest extends WebTestCase
{
    /** @test */
    public function homepage_is_displayed_successfully(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Password Generator');
        $this->assertPageTitleSame('Password Generator');
    }

    /** @test */
    public function generated_password_page_should_redirect_to_home_if_requirements_is_missing_from_session(): void
    {
        $client = static::createClient();

        $client->request('GET', '/password-generated');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/');
    }

    /** @test */
    public function cookies_are_not_present_when_visiting_homepage_for_the_first_time(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');
        
        $this->assertBrowserNotHasCookie('app_length');
        $this->assertBrowserNotHasCookie('app_uppercase_letters');
        $this->assertBrowserNotHasCookie('app_digits');
        $this->assertBrowserNotHasCookie('app_special_characters');
    }

    /** @test */
    public function cookies_are_set_when_generating_new_password(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $client->submitForm('Generate Password');

        $client->followRedirect();
        
        $this->assertBrowserHasCookie('app_length');
        $this->assertBrowserHasCookie('app_uppercase_letters');
        $this->assertBrowserHasCookie('app_digits');
        $this->assertBrowserHasCookie('app_special_characters');
    }

    /** @test */
    public function password_generation_form_should_work(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $client->submitForm('Generate Password');

        $crawler = $client->followRedirect();

        $this->assertRouteSame('app_passwords_show');
        $this->assertSame(12, mb_strlen($crawler->filter('.alert.alert-success > strong')->text()));

        $client->clickLink('« Go back to the homepage');
        $this->assertRouteSame('app_home');
    }

    /** @test */
    public function should_generate_new_password_each_time_we_refresh(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $client->submitForm('Generate Password');

        $crawler = $client->followRedirect();

        $this->assertRouteSame('app_passwords_show');
        
        $password1 = $crawler->filter('.alert.alert-success > strong')->text();

        $crawler = $client->request('GET', '/password-generated');

        $password2 = $crawler->filter('.alert.alert-success > strong')->text();

        $this->assertSame(12, mb_strlen($password1));
        $this->assertSame(12, mb_strlen($password2));
        $this->assertNotSame($password1, $password2);
    }

    /** @test */
    public function password_generation_form_with_values_should_work(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $client->submitForm('Generate Password', [
            'password_requirements[length]' => 15,
            'password_requirements[uppercase_letters]' => false,
            'password_requirements[digits]' => true,
            'password_requirements[special_characters]' => true
        ]);

        $crawler = $client->followRedirect();

        $this->assertRouteSame('app_passwords_show');
        $this->assertSame(
            15, mb_strlen($crawler->filter('.alert.alert-success > strong')->text())
        );
        $crawler = $client->clickLink('« Go back to the homepage');
        $this->assertRouteSame('app_home');

        $this->assertBrowserCookieValueSame('app_length', '15');
        $this->assertBrowserCookieValueSame('app_uppercase_letters', '0');
        $this->assertBrowserCookieValueSame('app_digits', '1');
        $this->assertBrowserCookieValueSame('app_special_characters', '1');

        $this->assertSame('15', $crawler->filter('select[name="password_requirements[length]"] > option[selected]')->attr('value'));
        $this->assertCheckboxNotChecked('password_requirements[uppercase_letters]');
        $this->assertCheckboxChecked('password_requirements[digits]');
        $this->assertCheckboxChecked('password_requirements[special_characters]');
    }

    /** @test */
    public function password_min_length_should_be_8(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->expectException(\InvalidArgumentException::class);

        $client->submitForm('Generate Password', [
            'password_requirements[length]' => 2
        ]);
    }

    /** @test */
    public function password_max_length_should_be_60(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->expectException(\InvalidArgumentException::class);
        
        $client->submitForm('Generate Password', [
            'password_requirements[length]' => 100
        ]);
    }
}
