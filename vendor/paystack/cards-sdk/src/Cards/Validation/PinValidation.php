<?php

namespace Paystack\Cards\Validation;

use Respect\Validation\Validator as v;

class PinValidation
{
    public static function validate($pin)
    {
        return v::digit()->length(4)->validate($pin);
    }
}
