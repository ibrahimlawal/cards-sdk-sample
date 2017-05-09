<?php

namespace Paystack\Cards\Validation;

use Respect\Validation\Validator as v;

class PhoneValidation
{
    public static function validate($phone)
    {
        return v::digit()->length(11, 13)->validate($phone);
    }
}
