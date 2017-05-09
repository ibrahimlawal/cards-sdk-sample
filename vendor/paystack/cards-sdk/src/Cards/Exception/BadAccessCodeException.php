<?php
namespace Paystack\Cards\Exception;

use Yabacon\Paystack\Exception\PaystackException;

class BadAccessCodeException extends PaystackException
{
    public $errors;
    public function __construct($message = "Bad Access Code", array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }
}
