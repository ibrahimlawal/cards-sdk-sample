<?php

namespace Paystack\Cards\Exception;

use Yabacon\Paystack\Exception\PaystackException;

class TransportException extends PaystackException
{
    public $errors;
    public function __construct($message, array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }
}
