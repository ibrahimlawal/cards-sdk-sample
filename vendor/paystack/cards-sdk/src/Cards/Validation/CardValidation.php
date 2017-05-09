<?php

namespace Paystack\Cards\Validation;

use Respect\Validation\Validator as v;
use Paystack\Cards\Card;

class CardValidation
{
    public static function validateCard(Card $card)
    {
        return CardValidation::validatePan($card->pan)
            && CardValidation::validateCvc($card->cvc)
            && CardValidation::validateExpiryMonth($card->exp_month)
            && CardValidation::validateExpiryYear($card->exp_year);
    }
    
    public static function validatePan($pan)
    {
        return CardValidation::validateVervePan($pan)
                || CardValidation::validateMasterCardPan($pan)
                || CardValidation::validateVisaPan($pan);
    }

    public static function validateCvc($cvc)
    {
        return v::digit()->length(3, 4)->validate($cvc);
    }

    public static function validateExpiryMonth($exp_month)
    {
        $sent_month = intval($exp_month);
        return v::intVal()->between(1, 12)->validate($sent_month);
    }

    public static function validateExpiryYear($exp_year)
    {
        $comp_year = intval(date('Y')) % 2000;
        $sent_year = intval($exp_year) % 2000;
        return v::intVal()->between($comp_year, 99)->validate($sent_year);
    }

    public static function validateVisaPan($pan)
    {
        return v::creditCard('Visa')->validate($pan);
    }

    public static function validateMasterCardPan($pan)
    {
        return v::creditCard('MasterCard')->validate($pan);
    }

    public static function validateVervePan($pan)
    {
        return v::digit()->startsWith('5060')->length(16, 19)->validate($pan)
                || v::digit()->startsWith('5061')->length(16, 19)->validate($pan)
                || v::digit()->startsWith('5078')->length(16, 19)->validate($pan)
                || v::digit()->startsWith('5079')->length(16, 19)->validate($pan)
                || v::digit()->startsWith('6500')->length(16)->validate($pan);
    }
}
