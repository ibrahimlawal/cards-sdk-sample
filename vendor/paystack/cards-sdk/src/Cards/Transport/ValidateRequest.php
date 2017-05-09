<?php
namespace Paystack\Cards\Transport;

use Yabacon\Paystack\Http\Request as HRequest;
use Paystack\Cards\Exception\TransportException;
use Paystack\Cards\Validation\ValidateRequestValidation;

class ValidateRequest extends Request
{
    public $verb = 'charge/validate';
    protected $method = 'POST';

    protected $trans;
    protected $token;
    protected $type;

    public function __construct($trans, $token, $type)
    {
        $this->trans = $trans;
        $this->token = $token;
        $this->type  = $type ;
    }

    public function run()
    {
        $pack = new HRequest();
        $pack->body = http_build_query([
            'trans' => $this->trans,
            'token' => $this->token,
        ]);
        return $this->send($pack);
    }
}
