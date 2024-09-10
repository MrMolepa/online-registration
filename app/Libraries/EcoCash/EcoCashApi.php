<?php

namespace App\Libraries\EcoCash;

use Illuminate\Http\Request;
use \Exception;

class EcoCashApi
{




    public function payMerchant($phone_number, $amount)
    {
        $token = $this->generateECoshToken();
        $access_token = $token->access_token;
        $requestId = "EcoCash$phone_number" . time();
        $url = "http://197.155.192.226:8083/openapi/PayMerchant";
        $postData = json_encode(array(
            "msisdn" => $phone_number,
            "merchantNumber" => "69065108",
            "merchCode" => "27596",
            "amount" => $amount,
            "requestId" =>   $requestId,
            "vendor_code" => "ecol",
            "api_key" => "",
            "checksum" => "",
            "callbackurl" => ""
        ));

        $curl = curl_init();

        //Set your auth headers
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token
        ));
        //setup the request, you can also use CURLOPT_URL
        curl_setopt($curl, CURLOPT_URL,   $url);

        // Returns the data/output as a string instead of raw data
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        // get stringified data/output. See CURLOPT_RETURNTRANSFER
        $output = "";
        $response = false;
        $output = curl_exec($curl);
        // close curl resource to free up system resources
        curl_close($curl);
        if( $output!== false) {
            $responseObject =(object) json_decode($output, true) ;
            $responseObject->requestId = $requestId;
            $responseObject->access_token= $access_token;
            $response = $responseObject;
        }
        return  $response;
    }


    public function getEcoCashResponse($phone_number, $amount){
        $payMerchant=$this->payMerchant($phone_number, $amount);
        sleep(30);
        if ($payMerchant) {
            $requestId= $payMerchant->requestId;
            $access_token= $payMerchant->access_token;
            $done = true;
            $response=false;
            while($done){
                $status=$this->checkTransactionStatus($requestId,$access_token);
                if ($status->txnstatus!='417' && $status->message!=='Pending' ) {
                    $response= $status;
                    $done = false;
                }
            }
         return   $response ;
        }
        return false;
    }


    public function checkTransactionStatus($requestId,$access_token)
    {
        $url = "http://197.155.192.226:8083/openapi/QueryTransactionStatus";
        $postData = json_encode(array(
            "transactionId" => $requestId,
            "vendor_code" => "ecol",
            "api_key" => "",
            "checksum" => "",
        ));

        $curl = curl_init();

         //Set your auth headers
         curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token
        ));
        //setup the request, you can also use CURLOPT_URL
        curl_setopt($curl, CURLOPT_URL,   $url);
        // Returns the data/output as a string instead of raw data
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        // get stringified data/output. See CURLOPT_RETURNTRANSFER
        $output = "";
        $response = false;
        $output = curl_exec($curl);
        // close curl resource to free up system resources
        curl_close($curl);
        if( $output!== false) {
            $responseObject =(object) json_decode($output, true) ;
            $responseObject->requestId = $requestId;
            $response = $responseObject;
        }
        return  $response;
    }



    public  function generateECoshToken()
    {
        //Generate Token
        //API URL
        $url = "http://197.155.192.226:8083/token";
        //Prepare you post parameters
        $postData = array(
            'username' => 'ecol',
            'password' => 'ecolVZE$!#$e!j1LE#$#exam',
            'grant_type' => 'password'
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL,   $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
        $output = "";
        $response = false;
        $output = curl_exec($curl);
        // close curl resource to free up system resources
        curl_close($curl);
        if( $output!== false) {
            $response =(object) json_decode($output, true) ;
        }
        return  $response;
    }


    public function ecoCashCallBackUrl(Request $request)
    {
        // store call back data
        $callback = file_get_contents('php://input');
        $callbackurl = json_decode($callback, true);
        return $callbackurl;
    }

    //Cryptophraohy extension -> OpenSSL -> base64
    public function ecoCashChecksum($uniqieID)
    {
        $data = "ECOL" . "30626c006b435422a78445b524e6f436c231739d6b31d8d3bf1a10d47c63f9c3" . "000006" . $uniqieID;
        // 3coCa2ho83C01s02i
        // fetch private key from file and ready it
        $fp = fopen(public_path('cetificate/cert.key'), "r");
        $priv_key = fread($fp, 8192);
        fclose($fp);
        $binary_signature = "";
        openssl_sign($data, $binary_signature, $priv_key);
        $binary_signature = base64_encode($binary_signature);
        return   $binary_signature;
        // ==================== END Of our code ===================//
    }
}
