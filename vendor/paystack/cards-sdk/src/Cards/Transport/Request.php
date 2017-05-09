<?php
namespace Paystack\Cards\Transport;

use Yabacon\Paystack\Http\Request as HRequest;
use Paystack\Cards\Exception\TransportException;
use Paystack\Cards;

class Request
{
    private $root = 'https://standard.paystack.co/';
    protected $method = 'GET';
    public $verb = 'requery';

    protected function send(HRequest $request)
    {
        $request->endpoint = $this->root . $this->verb;
        $request->method = $this->method;
        $request->headers["X-Paystack-Cards-Version"] = Cards::VERSION;
        $response = $this->sendRequest($request);
        $body = $this->fetchBody($response);
        if ($body === false) {
            throw new TransportException('There was an error completing the request: ' . implode(',', $response->messages), $response->messages);
        }
        $decoded_body = json_decode($body);
        return $decoded_body;
    }

    protected function sendRequest($request)
    {
        return $request->send();
    }

    public function runDefault()
    {
        $pack = new HRequest();
        return $this->send($pack);
    }

    protected function fetchBody($response)
    {
        return $response->wrapUp();
    }
}
