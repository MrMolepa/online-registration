<?php
namespace App\Libraries\SMS;


class SmsApi
{


    public static function message($msisdn, $message)
    {


        $phone = SmsApi::validatePhoneNumber($msisdn);
        if ($phone) {
            $apiKey = "6294b23b-a0ea-4200-9535-ba4e8cd4725c";
            $companyId = "8";
            $url = "https://my.1click.africa/api/message/";
            $postData = json_encode(array(
                "companyId" => $companyId,
                "msisdn" =>   $phone,
                "message" =>  $message,
            ));
            $curl = curl_init();

            //Set your auth headers
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-API-KEY: ' . $apiKey,
                'Content-Type: application/json'
            ));
            //setup the request, you can also use CURLOPT_URL
            curl_setopt($curl, CURLOPT_URL,   $url);
            // Returns the data/output as a string instead of raw data
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
            // get stringified data/output. See CURLOPT_RETURNTRANSFER


            $response = curl_exec($curl);
            // close curl resource to free up system resources

            if (curl_errno($curl)) {
                $response = 'Error:' . curl_error($curl);
            } else {
                $response = 'success';
            }
            curl_close($curl);
            return  $response;
        } else {
            return  false;
        }
    }

    private  static function validatePhoneNumber($phone)
    {
        // Remove all non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', trim($phone));
        // Check if the cleaned number has between 8-15 digits (common range)
        if (strlen("266$cleaned") >= 11) {
            return "266$cleaned";
        } else {
            return false;
        }
    }
}
