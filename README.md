# Lithe Crypt

Lithe Crypt is a simple encryption and decryption utility for PHP, designed to work with the Lithe framework. It utilizes the AES-256-CBC algorithm for secure data handling.

## Installation

To install the Lithe Crypt package, you can use Composer. If you haven't already, make sure you have Composer installed. Then run the following command in your project directory:

```bash
composer require lithemod/crypt
```

## Requirements

- **PHP 8 or higher**
- **OpenSSL extension** enabled in your PHP installation

## Usage

### Loading Environment Variables

Before using the Crypt class, you need to load your environment variables. Use the following code to load your `.env` file:

```php
use Lithe\Support\Env;

// Load environment variables
Env::load(__DIR__); // Adjust the path as necessary
```

### Setting the APP_KEY

Ensure that the `APP_KEY` environment variable is set. This key should be a base64 encoded string of 32 bytes. You can configure it in your `.env` file or directly in your server environment.

**Example of a valid base64 key:**

```plaintext
YXNkZmFnc2Rhc2RmYWdlcyBhc2RmYWdlcyBhYXNkZmFnc2Q=
```

### Encrypting Data

To encrypt data, use the `encrypt` method of the Crypt class. You can also specify whether you want to use a fixed IV (initialization vector) for encryption:

```php
use Lithe\Support\Security\Crypt;

$data = "sensitive data";

// Encrypt without fixed IV
$encrypted = Crypt::encrypt($data);
echo "Encrypted Data: " . $encrypted;

// Encrypt with fixed IV (useful for unique values like emails)
$encryptedWithSameIV = Crypt::encrypt($data, true);
echo "Encrypted Data with Fixed IV: " . $encryptedWithSameIV;
```

### Decrypting Data

To decrypt the previously encrypted data, use the `decrypt` method. You must specify the same parameters used during encryption to ensure proper decryption:

```php
use Lithe\Support\Security\Crypt;

// Decrypt without fixed IV
$decrypted = Crypt::decrypt($encrypted);
echo "Decrypted Data: " . $decrypted;

// Decrypt with fixed IV
$decryptedWithSameIV = Crypt::decrypt($encryptedWithSameIV, true, $data);
echo "Decrypted Data with Fixed IV: " . $decryptedWithSameIV;
```

### Exception Handling

If the `APP_KEY` is not set or is invalid, the Crypt class will throw a `CryptException`. It's essential to handle this exception in your code to avoid unexpected errors:

```php
use Lithe\Exceptions\Encryption\CryptException;

try {
    $encrypted = Crypt::encrypt($data);
    // Decrypt without fixed IV
    $decrypted = Crypt::decrypt($encrypted);
} catch (CryptException $e) {
    echo "Encryption Error: " . $e->getMessage();
}
```

## Unit Tests

To ensure the functionality of the Crypt class, unit tests are provided. You can run the tests using PHPUnit:

```bash
./vendor/bin/phpunit
```

### Test Cases

- `testEncryptDecrypt`: Tests the encryption and decryption process.
- `testEncryptWithNullData`: Tests encryption with null data.
- `testEncryptWithSameIV`: Tests the behavior of encryption with a fixed IV.

## Contributing

If you would like to contribute to the Lithe Crypt project, please follow these guidelines:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Write tests for your changes.
4. Submit a pull request with a clear description of your changes.

## License

This project is licensed under the MIT License.