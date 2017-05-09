<?php
namespace Paystack\Cards\Transport;

use Yabacon\Paystack\Http\Request as HRequest;
use Paystack\Cards\Exception\TransportException;
use Paystack\Cards\Validation\ChargeRequestValidation;
use Paystack\Cards\Card;

class ChargeRequest extends Request
{
    public $verb = 'charge';
    protected $method = 'POST';

    protected $clientdata;
    protected $trans;
    protected $last4;
    protected $device;
    protected $handle = '';

    public function __construct($trans, $device, Card $card, $pin)
    {
        $this->device = $device;
        $this->trans = $trans;
        $this->clientdata = Encryptor::encryptCard($card);
        $this->last4 = substr($card->pan, '-4');
        if ($pin !== null) {
            $this->handle = Encryptor::encrypt($pin);
        }
    }

    public function run()
    {
        $pack = new HRequest();
        $pack->body = http_build_query([
            'clientdata' => $this->clientdata,
            'trans' => $this->trans,
            'last4' => $this->last4,
            'device' => $this->device,
            'handle' => $this->handle,
        ]);
        return $this->send($pack);
    }
}
