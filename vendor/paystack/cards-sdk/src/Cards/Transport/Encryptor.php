<?php
namespace Paystack\Cards\Transport;

use Paystack\Cards\Exception\EncryptionException;
use Paystack\Cards\Card;

class Encryptor
{
    private static $public_key = '-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBALhZs/7hP0g0+hrqTq0hFyGVxgco0NMx
ZD8nPS6ihxap0yNFjzdyUuZED6P4/aK9Ezl5ajEI9pcx5/1BrEE+F3kCAwEAAQ==
-----END PUBLIC KEY-----';

    public static function encrypt($value)
    {
        $crypted = null;
        if (openssl_public_encrypt($value, $crypted, Encryptor::$public_key, OPENSSL_PKCS1_PADDING)) {
            return base64_encode($crypted);
        } else {
            throw new EncryptionException('Trouble encoding: ' . error_get_last());
        }
    }

    public static function encryptCard(Card $card)
    {
        $pan = $card->pan;
        $cvc = $card->cvc;
        $exp_month = intval($card->exp_month) % 12;
        if ($exp_month === 0) {
            $exp_month = 12;
        }
        $exp_month = str_pad(strval($exp_month), 2, '0', STR_PAD_LEFT);
        $exp_year = strval(2000 + (intval($card->exp_year)%2000));
        return Encryptor::encrypt("{$pan}*{$cvc}*{$exp_month}*{$exp_year}");
    }
}
