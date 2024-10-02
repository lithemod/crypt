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
}
