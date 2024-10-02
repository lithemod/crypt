# Lithe Crypt

Lithe Crypt is a simple encryption and decryption utility for PHP, designed to work with the Lithe framework. It utilizes the AES-256-CBC algorithm for secure data handling.

## Installation

To install the Lithe Crypt package, you can use Composer. If you haven't already, make sure you have Composer installed. Then run the following command in your project directory:

```bash
composer require lithemod/crypt
```

## Requirements

- PHP 8 or higher
- OpenSSL extension enabled in PHP

## Usage

### Loading Environment Variables

Before using the Crypt class, you need to load the environment variables. Use the following line of code to load your `.env` file:

```php
use Lithe\Support\Env;

// Load environment variables
Env::load(__DIR__); // Adjust the path as necessary
```

### Setting the APP_KEY

Ensure you have set the `APP_KEY` environment variable. This key should be a base64 encoded string of 32 bytes. You can set it in your `.env` file or directly in your server environment.

Example of generating a valid APP_KEY:

```bash
# Generate a random 32-byte key and encode it in base64
echo -n $(openssl rand -base64 32) > .env
```

### Encrypting Data

To encrypt data, use the `encrypt` method of the `Crypt` class:

```php
use Lithe\Support\Security\Crypt;

$data = "some sensitive data";
$encrypted = Crypt::encrypt($data);
echo "Encrypted Data: " . $encrypted;
```

### Decrypting Data

To decrypt data, use the `decrypt` method:

```php
use Lithe\Support\Security\Crypt;

$decrypted = Crypt::decrypt($encrypted);
echo "Decrypted Data: " . $decrypted;
```

### Handling Exceptions

If the APP_KEY is not set or is invalid, the `Crypt` class will throw a `CryptException`. Make sure to handle this exception in your code:

```php
use Lithe\Exceptions\Encryption\CryptException;

try {
    $encrypted = Crypt::encrypt($data);
} catch (CryptException $e) {
    echo "Encryption Error: " . $e->getMessage();
}
```

## Unit Testing

To ensure the functionality of the Crypt class, unit tests are provided. You can run the tests using PHPUnit:

```bash
./vendor/bin/phpunit
```

### Test Cases

- `testEncryptDecrypt`: Tests the encryption and decryption process.
- `testEncryptWithNullData`: Tests encryption with null data.

## Contributing

If you wish to contribute to the Lithe Crypt project, please follow these guidelines:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Write tests for your changes.
4. Submit a pull request with a clear description of your changes.

## License

This project is licensed under the MIT License.