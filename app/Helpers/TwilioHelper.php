<?php

namespace App\Helpers;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;
use Exception;
use Http;
use Request;


class TwilioHelper
{

    protected $sid;
    protected $token;
    protected $twilioNumber;
    protected $client;

    public function __construct()
    {
        $this->sid = Helper::_get_settings('TWILIO_SID');
        $this->token = Helper::_get_settings('TWILIO_AUTH_TOKEN');
        $this->twilioNumber = Helper::_get_settings('TWILIO_PHONE_NUMBER');
        $this->client = new Client($this->sid, $this->token);
    }

    
    public static function sendSMS($to, $message)
    {

        $client = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        
        try {
            return $client->messages->create( $to, [
                'from' => config('services.twilio.phone_number'),
                'body' => "",
            ]);
            
        } catch (Exception $e) {    
            return ['error' => $e->getMessage()];
        }

    }

    
    public function sendMMS($to, $message, $mediaUrl)
    {
        try {
            return $this->client->messages->create("$to", [
                'from'     => $this->twilioNumber,
                'body'     => $message,
                'mediaUrl' => [$mediaUrl],
            ]);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function makeCall($to, $twimlUrl)
    {
        try {
            return $this->client->calls->create($to, $this->twilioNumber, [
                'url' => $twimlUrl,
            ]);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
        
    }

    
    public function validateNumber($phoneNumber)
    {
        try {
            return $this->client->lookups->v1->phoneNumbers($phoneNumber)->fetch(['type' => 'carrier']);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function fetchSMSDetails($messageSid)
    {
        try {
            return $this->client->messages($messageSid)->fetch();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    
    public function fetchAllMessages()
    {
        try {
            return $this->client->messages->read();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function fetchCallDetails($callSid)
    {
        try {
            return $this->client->calls($callSid)->fetch();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function createValidationRequest($phoneNumber)
    {  $client = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        try {
            $validationRequest = $client->validationRequests->create(
                $phoneNumber, // Phone Number
                ["friendlyName" => "test name"] // Optional friendly name
            );

            return "Validation request created successfully. Account SID: " . $validationRequest->accountSid;
        } catch (\Exception $e) {
            return 'Error creating validation request: ' . $e->getMessage();
        }
    }
}

