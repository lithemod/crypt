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
            // Retrieve the APP_KEY from the environment configuration
            $key = Env::get('APP_KEY');
            if (!$key) {
                throw new CryptException('APP_KEY environment variable not set.');
            }

            // Decode the base64 encoded key
            $decodedKey = base64_decode($key, true);
            // Ensure the decoded key is 32 bytes (required for AES-256 encryption)
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
     * @param bool $useSameIV Whether to use a fixed IV (useful for unique values like emails).
     * @return string Encrypted data in base64 format.
     * @throws CryptException If an error occurs while encrypting the data.
     */
    public static function encrypt(?string $data, bool $useSameIV = false): string
    {
        try {
            // Generate a random initialization vector (IV) unless a fixed one is used
            if ($useSameIV) {
                // If using the same IV, generate it deterministically from the data
                $iv = substr(hash('sha256', $data ?? ''), 0, openssl_cipher_iv_length('AES-256-CBC'));
            } else {
                // Generate a random IV for encryption
                $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
            }

            // Encrypt the data with AES-256-CBC using the key and IV
            $encryptedData = openssl_encrypt($data ?? '', 'AES-256-CBC', static::key(), 0, $iv);

            // Return the base64 encoded string, including the IV for decryption purposes
            return base64_encode($iv . $encryptedData);
        } catch (\Exception $e) {
            // Log and throw an exception if encryption fails
            $error = 'Error encrypting data: ' . $e->getMessage();
            Log::error($error);
            throw new CryptException($error);
        }
    }

    /**
     * Decrypts the provided data.
     * 
     * @param string|null $encryptedData Encrypted data in base64 format.
     * @param bool $useSameIV Whether to use a fixed IV (matching the encryption process).
     * @param string|null $originalData The original unencrypted data (used to generate the same IV).
     * @return string|null Decrypted data. Returns null if decryption fails or if data is empty.
     * @throws CryptException If an error occurs while decrypting the data.
     */
    public static function decrypt(?string $encryptedData, bool $useSameIV = false, ?string $originalData = null): ?string
    {
        try {
            if (empty($encryptedData)) {
                return null;
            }

            // Decode the base64 encoded encrypted data
            $decodedData = base64_decode($encryptedData);
            $ivLength = openssl_cipher_iv_length('AES-256-CBC');

            // Determine how to get the IV based on whether it's a fixed or random IV
            if ($useSameIV && $originalData) {
                // Generate the same IV based on the original data (useful for consistent encryption results)
                $iv = substr(hash('sha256', $originalData), 0, $ivLength);
            } else {
                // Extract the IV from the beginning of the decoded data (for random IVs)
                $iv = substr($decodedData, 0, $ivLength);
            }

            // Extract the encrypted portion of the data (after the IV)
            $encryptedData = substr($decodedData, $ivLength);

            // Decrypt the data using AES-256-CBC with the key and IV
            return openssl_decrypt($encryptedData, 'AES-256-CBC', static::key(), 0, $iv);
        } catch (\Exception $e) {
            // Log and throw an exception if decryption fails
            $error = 'Error decrypting data: ' . $e->getMessage();
            Log::error($error);
            throw new CryptException($error);
        }
    }
}
