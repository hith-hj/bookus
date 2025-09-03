<?php

namespace App\Http\Traits;

use Twilio\Exceptions\ConfigurationException as TwilioConfigurationException;
use Twilio\Exceptions\RestException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client as TClient;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

trait SmsNotification {
    public function sendSms($mobile, $msg){
        $account_sid = "";
        $auth_token = "";
        $twilio_number = "+17163551905";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.twilio.com/2010-04-01/Accounts/' . $account_sid . '/Messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded',]);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $account_sid. ':' . $auth_token);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'Body='.$msg.'&From=' .$twilio_number . '&To=' . $mobile);
        
        $response = curl_exec($ch);
        
        curl_close($ch);
        return $response;
    }
}