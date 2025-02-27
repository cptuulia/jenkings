<?php

namespace App\Services\Traits;


/**
 * This trait handles the matches db_codes
 */
trait TEncrypt
{
    private string $ciphering = "AES-128-CTR";
    private string $decryptionIv = '1234567891011121';

    public function encrypt(string $simple_string, string $encryption_key): string
    {
        $encryption = openssl_encrypt(
            $simple_string,
            $this->ciphering,
            $encryption_key,
            0,
            $this->decryptionIv
        );
        return $encryption;
    }

    public function decrypt(string $encryption, string $decryption_key): string
    {
        $decryption = openssl_decrypt(
            $encryption,
            $this->ciphering,
            $decryption_key,
            0,
            $this->decryptionIv
        );
        return $decryption;
    }


    private static function adminSalt(): string
    {
        $config = require __DIR__ . '/../../../config/config.php';
        return $config['admin']['passwordSalt'];
    }

    private function crypt(string $item, string $salt): string
    {
        return crypt($item, $salt);
    }

    private static function generateToken(int $length = 90): string
    {
        return substr(
            str_shuffle(
                str_repeat(
                    $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    ceil($length / strlen($x))
                )
            ),
            1,
            $length
        );
    }

}