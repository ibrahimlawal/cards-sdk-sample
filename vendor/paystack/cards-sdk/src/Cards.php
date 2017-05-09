<?php
namespace Paystack;

use Paystack\Cards\Card;
use Paystack\Cards\Exception\BadAccessCodeException;
use Paystack\Cards\Transport\Handler;
use Paystack\Cards\Transport\Request;
use Paystack\Cards\Transport\ValidateRequest;

use Paystack\Cards\Validation\PhoneValidation;
use Paystack\Cards\Validation\PinValidation;
use Paystack\Cards\Validation\TokenValidation;

use Yabacon\Paystack as PaystackPhp;
use Yabacon\Paystack\Exception\ApiException;
use Yabacon\Paystack\Exception\ValidationException;

class Cards
{
    const VERSION="0.1.0";

    protected $api;
    protected $handler;

    public function __construct(
        $secret_key,
        \Closure $successCallback,
        \Closure $failedCallback,
        \Closure $pinCallback,
        \Closure $otpCallback,
        \Closure $phoneCallback,
        \Closure $timeoutCallback,
        \Closure $threedsCallback
    ) {
        $this->api = new PaystackPhp($secret_key);
        $this->api->disableFileGetContentsFallback();
        $this->handler=new Handler(
            $successCallback,
            $failedCallback,
            $pinCallback,
            $otpCallback,
            $phoneCallback,
            $timeoutCallback,
            $threedsCallback,
            $this->api
        );
    }

    public function startTransactionReturningAccessCode($email, $amount)
    {
        $trx = $this->api->transaction->initialize(['email'=>$email,'amount'=>$amount]);
        return $trx->data->access_code;
    }

    protected function chargeRequestClass()
    {
        return 'Paystack\Cards\Transport\ChargeRequest';
    }

    protected function validateRequestClass()
    {
        return 'Paystack\Cards\Transport\ValidateRequest';
    }

    public function chargeCard($access_code, $device, Card $card, $pin = null)
    {
        if (($pin !== '') && ($pin !== null) && !PinValidation::validate($pin)) {
            throw new ValidationException('Invalid pin provided');
        }
        $trans = $this->transForAccessCode($access_code);
        $chargeRequestClass = $this->chargeRequestClass();
        $chargeRequest = new $chargeRequestClass($trans, $device, $card, $pin);
        $this->makeRequest($trans, $chargeRequest);
    }

    protected function makeRequest($trans, Request $request)
    {
        $resp = $request->run();
        $this->handler->handle($trans, $resp);
    }

    public function validatePhone($access_code, $phone)
    {
        if (!PhoneValidation::validate($phone)) {
            throw new ValidationException('Invalid Phone number provided');
        }
        $this->validateInput($access_code, $phone, 'phone');
    }

    public function validateOtp($access_code, $otp)
    {
        if (!TokenValidation::validate($otp)) {
            throw new ValidationException('Invalid otp provided');
        }
        $this->validateInput($access_code, $otp, 'otp');
    }

    private function validateInput($access_code, $token, $type)
    {
        $trans = $this->transForAccessCode($access_code);
        $validateRequestClass = $this->validateRequestClass();
        $validateRequest = new $validateRequestClass($trans, $token, $type);
        $this->makeRequest($trans, $validateRequest);
    }

    private function verifyAccessCode($access_code)
    {
        try {
            return $this->api->transaction->verifyAccessCode(['access_code'=>$access_code]);
        } catch (ApiException $a) {
            throw new BadAccessCodeException($a->getMessage());
        }
    }

    private function transForAccessCode($access_code)
    {
        $access = $this->verifyAccessCode($access_code);
        return $access->data->id;
    }
}
