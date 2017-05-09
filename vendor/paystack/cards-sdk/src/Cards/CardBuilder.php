<?php
namespace Paystack\Cards;

class CardBuilder
{
    private $pan;
    private $cvc;
    private $exp_month;
    private $exp_year;

    public function withPan($pan)
    {
        $this->pan = $pan;
        return $this;
    }

    public function withCvc($cvc)
    {
        $this->cvc = $cvc;
        return $this;
    }

    public function withExpiryMonth($exp_month)
    {
        $this->exp_month = $exp_month;
        return $this;
    }

    public function withExpiryYear($exp_year)
    {
        $this->exp_year = $exp_year;
        return $this;
    }

    public function build()
    {
        return new Card($this->pan, $this->cvc, $this->exp_month, $this->exp_year);
    }
}
