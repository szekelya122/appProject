<?php

use PHPUnit\Framework\TestCase;

class RegistrationPageTest extends TestCase
{
    public function testPageTitle()
    {
        $output = $this->getPageContent('register.php');
        $this->assertStringContainsString('<title>Felhasználói Regisztráció</title>', $output);
    }

    public function testHeading()
    {
        $output = $this->getPageContent('register.php');
        $this->assertStringContainsString('<h1 class="text-center text-gold mb-4">Regisztráció</h1>', $output);
    }

    public function testFormElements()
    {
        $output = $this->getPageContent('register.php');
        $this->assertStringContainsString('<form method="POST"', $output);
        $this->assertStringContainsString('<label for="email" class="form-label text-gold">Email:</label>', $output);
        $this->assertStringContainsString('<input type="email" id="email" name="email" class="form-control bg-black text-white border-gold" required>', $output);
        $this->assertStringContainsString('<label for="username" class="form-label text-gold">Felhasználónév:</label>', $output);
        $this->assertStringContainsString('<input type="text" id="username" name="username" class="form-control bg-black text-white border-gold" required>', $output);
        $this->assertStringContainsString('<label for="password" class="form-label text-gold">Jelszó:</label>', $output);
        $this->assertStringContainsString('<input type="password" id="password" name="password" class="form-control bg-black text-white border-gold" required>', $output);
        $this->assertStringContainsString('<label for="passwordConfirm" class="form-label text-gold">Jelszó megerősités:</label>', $output);
        $this->assertStringContainsString('<input type="password" id="passwordConfirm" name="passwordConfirm" class="form-control bg-black text-white border-gold" required>', $output);
        $this->assertStringContainsString('<button type="submit" class="btn btn-gold w-100">Regisztráció</button>', $output);
    }

    public function testLoginLink()
    {
        $output = $this->getPageContent('register.php');
        $this->assertStringContainsString('<p>Van már fiókod? <a href="logIn.php">Bejelentkezés</a></p>', $output);
    }

    // Helper function to get the content of the PHP file
    private function getPageContent(string $filename): string
    {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
}