<?php
namespace Paystack\Cards\Transport;

use Paystack\Cards\Exception\TransportException;

class Handler
{
    private $successCallback;
    private $failedCallback;
    private $pinCallback;
    private $otpCallback;
    private $phoneCallback;
    private $timeoutCallback;
    private $threedsCallback;
    public $api;
    protected $requeries = 0;
    protected $requery_delay = 5;
    protected $max_requeries = 5;

    public function __construct(
        $successCallback,
        $failedCallback,
        $pinCallback,
        $otpCallback,
        $phoneCallback,
        $timeoutCallback,
        $threedsCallback,
        $api
    ) {
         $this->successCallback = $successCallback;
         $this->failedCallback= $failedCallback;
         $this->pinCallback= $pinCallback;
         $this->otpCallback = $otpCallback;
         $this->phoneCallback = $phoneCallback;
         $this->timeoutCallback= $timeoutCallback;
         $this->threedsCallback = $threedsCallback;
         $this->api = $api;
    }

    public function handle($trans, $response)
    {
        if (is_null($response)) {
            return $this->handleNull($response);
        }
        if (isset($response->status)) {
            switch ($response->status) {
                case 'requery':
                    return $this->handleRequery($trans, $response);
                case 'timeout':
                    return call_user_func($this->timeoutCallback, $response->message);
                case 'success':
                    return $this->handleSuccess($response);
            }
        }
        if (isset($response->auth)) {
            switch ($response->auth) {
                case 'pin':
                    return call_user_func($this->pinCallback, $response->message);
                case 'otp':
                    return call_user_func($this->otpCallback, $response->otpmessage);
                case '3DS':
                    return call_user_func($this->threedsCallback, $response->otpmessage);
                case 'phone':
                    return call_user_func($this->phoneCallback, $response->otpmessage);
            }
        }
        $this->handleFailed($response);
    }

    protected function handleFailed($response)
    {
        $message = isset($response->message) ? $response->message : 'Something is wrong please try again later';
        call_user_func($this->failedCallback, $message);
    }

    protected function handleNull($response)
    {
        throw new TransportException('There was an error completing the request');
    }

    protected function requestClass()
    {
        return 'Paystack\Cards\Transport\Request';
    }

    protected function handleRequery($trans, $response)
    {
        $this->requeries++;
        if ($this->requeries >= $this->max_requeries) {
            return $this->handleFailed($response);
        }
        $wait = $this->requery_delay * $this->requeries;
        sleep($wait);
        $requestClass = $this->requestClass();
        $req = new $requestClass();
        $req->verb = 'requery/' + $trans;
        $this->postPrepRequeryRequest($req);
        return $this->handle($trans, $req->runDefault());
    }

    protected function postPrepRequeryRequest($request)
    {
    }

    protected function handleSuccess($response)
    {
        $verify = $this->api->transaction->verify(['reference'=>$response->reference]);
        call_user_func($this->successCallback, $verify);
    }
}
