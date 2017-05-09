<?php

namespace Paystack\Cards\Exception;

use Yabacon\Paystack\Exception\PaystackException;

class EncryptionException extends PaystackException
{
    public $errors;
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
