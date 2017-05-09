<?php
namespace Paystack\Cards;

use Paystack\Cards\Validation\CardValidation;
use Paystack\Cards\Exception\IncompleteCardDetailsException;

class Card
{
    public $pan;
    public $cvc;
    public $exp_month;
    public $exp_year;

    public function __construct($pan, $cvc, $exp_month, $exp_year)
    {
        $this->pan = $pan;
        $this->cvc = $cvc;
        $this->exp_month = $exp_month;
        $this->exp_year = $exp_year;
        if (!CardValidation::validateCard($this)) {
            throw new IncompleteCardDetailsException();
        }
    }
}
