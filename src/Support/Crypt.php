<?php

namespace Lithe\Support\Security;

use Lithe\Exceptions\Encryption\CryptException;
use Lithe\Support\Env;
use Lithe\Support\Log;

class Crypt
{
    protected static $key;

    /**
     * Returns the encryption key.
     *
     * @return string
     * @throws CryptException If the encryption key is invalid or not set.
     */
    protected static function key(): string
    {
        if (!static::$key) {
            $key = Env::get('APP_KEY');
            if (!$key) {
                throw new CryptException('APP_KEY environment variable not set.');
            }

            // Decode the base64 encoded key
            $decodedKey = base64_decode($key, true);
            if ($decodedKey === false || strlen($decodedKey) !== 32) {
                throw new CryptException('Invalid APP_KEY. Ensure it is a valid base64 encoded key with a length of 32 bytes.');
            }

            static::$key = $decodedKey;
        }

        return static::$key;
    }

    /**
     * Encrypts the provided data.
     *
     * @param string|null $data Data to be encrypted. Can be null.
     * @return string Encrypted data in base64 format.
     * @throws CryptException If an error occurs while encrypting the data.
     */
    public static function encrypt(?string $data): string
    {
        try {
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
            $encryptedData = openssl_encrypt($data ?? '', 'AES-256-CBC', static::key(), 0, $iv);
            // Return base64 encoded string including the IV for later decryption
            return base64_encode($iv . $encryptedData);
        } catch (\Exception $e) {
            $error = 'Error encrypting data: ' . $e->getMessage();
            Log::error($error);
            throw new CryptException($error);
        }
    }

    /**
     * Decrypts the provided data.
     *
     * @param string|null $encryptedData Encrypted data in base64 format.
     * @return string|null Decrypted data. Returns null if decryption fails or if data is empty.
     * @throws CryptException If an error occurs while decrypting the data.
     */
    public static function decrypt(?string $encryptedData): ?string
    {
        try {
            if (empty($encryptedData)) {
                return null;
            }

            $decodedData = base64_decode($encryptedData);
            $ivLength = openssl_cipher_iv_length('AES-256-CBC');
            $iv = substr($decodedData, 0, $ivLength);
            $encryptedData = substr($decodedData, $ivLength);

            return openssl_decrypt($encryptedData, 'AES-256-CBC', static::key(), 0, $iv);
        } catch (\Exception $e) {
            $error = 'Error decrypting data: ' . $e->getMessage();
            Log::error($error);
            throw new CryptException($error);
        }
    }
}
