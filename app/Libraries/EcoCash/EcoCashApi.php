<?php

namespace App\Libraries\EcoCash;

use Illuminate\Http\Request;
use \Exception;

class EcoCashApi
{




    public function payMerchant($phone_number, $amount,$transactionReference='',$itemsDesc = '')
    {
        $token = $this->generateECoshToken();
        if (!$token) {
            return false;
        }


        // Check if token exists and assign it, otherwise return false
        $access_token = $token->token ?? false;

        //Generate Token
        //API URL
        $url = "https://dt-externalproxy-1.etl.co.ls/etl/ecoussd/pay-merchant";
        $requestId = "Eco-$phone_number-$transactionReference-" . time();

        // Prepare your post parameters as JSON
        $postData = [
            'msisdn' => "266$phone_number",
            'short_code' => "99247", //99247,27596
            'amount' => $amount,
            'request_id' =>  $requestId,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token
        ));

        $output = curl_exec($curl);

        if (curl_errno($curl)) {
            $responseData = (object) json_decode($curl, true);
        }
        curl_close($curl);
        $responseData = json_decode($output, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $responseData = (object) json_decode($output, true);
        }
        return (object) $responseData;
    }


    public function getEcoCashResponse($phone_number, $amount,$transactionReference='',$itemsDesc = '')
    {
        $payMerchant = $this->payMerchant($phone_number, $amount,$transactionReference,$itemsDesc);
        if ($payMerchant) {
            return   $payMerchant;
        }
        return false;
    }


    public function checkTransactionStatus($requestId, $access_token)
    {


        $url = "https://dt-externalproxy-1.etl.co.ls/etl/ecoussd/check/transaction/$requestId";
        $curl = curl_init();
        // Set your auth headers
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token
        ));

        // Setup the request URL
        curl_setopt($curl, CURLOPT_URL, $url);

        // Make it a GET request (default is GET, but explicitly setting it)
        curl_setopt($curl, CURLOPT_HTTPGET, true);

        // Return the data/output as a string instead of printing it
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $output = curl_exec($curl);

        // Close curl resource to free up system resources
        curl_close($curl);

        $response = false;

        if ($output !== false) {
            $responseObject = (object) json_decode($output, true);
            $responseObject->requestId = $requestId;
            $response = $responseObject;
        }

        return $response;
    }






    public  function generateECoshToken()
    {
        //Generate Token
        //API URL
        $url = "https://dt-externalproxy-1.etl.co.ls/etl/ecoussd/auth/login";

        // Prepare your post parameters as JSON
        $postData = [
            'username' => 'ecol',
            'password' => 'syvx444S',
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json' // optional, good practice
        ]);

        $output = curl_exec($curl);

        if (curl_errno($curl)) {
            $responseData = (object) json_decode($curl, true);
        }
        curl_close($curl);
        $responseData = json_decode($output, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $responseData = (object) json_decode($output, true);
        }
        return (object) $responseData;
    }
}
