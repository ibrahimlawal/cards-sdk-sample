<?php

namespace Paystack\Cards\Exception;

use Yabacon\Paystack\Exception\PaystackException;

class IncompleteCardDetailsException extends PaystackException
{
    public $errors;
    public function __construct($message = "Incomplete Card Details", array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }
}
