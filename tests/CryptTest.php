<?php

namespace Tests\Support\Security;

use Lithe\Support\Security\Crypt;
use PHPUnit\Framework\TestCase;
use Lithe\Support\Env;

class CryptTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Load environment variables before the tests
        Env::load(__DIR__ . '/../');
    }

    public function testEncryptDecrypt()
    {
        $data = "some sensitive data";
        $encrypted = Crypt::encrypt($data);
        $decrypted = Crypt::decrypt($encrypted);

        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptWithNullData()
    {
        $encrypted = Crypt::encrypt(null);
        $decrypted = Crypt::decrypt($encrypted);

        $this->assertEquals('', $decrypted);
    }

    public function testEncryptWithFixedIV()
    {
        $data = "email@example.com";
        // Encrypt with fixed IV (deterministic encryption)
        $encrypted1 = Crypt::encrypt($data, true);
        $encrypted2 = Crypt::encrypt($data, true);

        // The encrypted data should be the same for the same input when using fixed IV
        $this->assertEquals($encrypted1, $encrypted2);

        // Decrypt the data and check if it's the same as original
        $decrypted = Crypt::decrypt($encrypted1, true);
        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptDecryptWithDifferentIVs()
    {
        $data = "unique sensitive data";
        // Encrypt using random IV
        $encrypted1 = Crypt::encrypt($data);
        $encrypted2 = Crypt::encrypt($data);

        // The encrypted data should be different due to random IVs
        $this->assertNotEquals($encrypted1, $encrypted2);

        // Both should decrypt back to the original data
        $decrypted1 = Crypt::decrypt($encrypted1);
        $decrypted2 = Crypt::decrypt($encrypted2);

        $this->assertEquals($data, $decrypted1);
        $this->assertEquals($data, $decrypted2);
    }

    public function testEncryptDecryptEmptyString()
    {
        $data = "";
        $encrypted = Crypt::encrypt($data);
        $decrypted = Crypt::decrypt($encrypted);

        // Even for an empty string, it should correctly decrypt to an empty string
        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptDecryptSpecialCharacters()
    {
        $data = "Sensitive data with special characters: !@#$%^&*()_+";
        $encrypted = Crypt::encrypt($data);
        $decrypted = Crypt::decrypt($encrypted);

        // Ensure data with special characters can be encrypted and decrypted
        $this->assertEquals($data, $decrypted);
    }
}
