<?php

namespace Paystack\Cards\Validation;

use Respect\Validation\Validator as v;

class TokenValidation
{
    public static function validate($token)
    {
        return v::digit()->length(4, 13)->validate($token);
    }
}
