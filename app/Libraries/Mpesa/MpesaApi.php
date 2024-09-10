<?php

namespace App\Libraries\Mpesa;

require_once('PortalSDK/api.php');

use \APIContext;

use \APIMethodType;

use \APIRequest;

use \Exception;



class MpesaApi

{



    // Generate SessionKey

    // This API should always be the first call to be used as it will return the SessionKey

    //  that needs to be used in conjunction with all of the other API calls from the API documentation.

    public function C2BMpesa($mobile_number, $amount)

    {
        // This is to ensure browser does not timeout after 30 seconds
        ini_set('max_execution_time', 300);
        set_time_limit(300);
        // Public key on the API listener used to encrypt keys
        $public_key = 'MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAietPTdEyyoV/wvxRjS5pSn3ZBQH9hnVtQC9SFLgM9IkomEX9Vu9fBg2MzWSSqkQlaYIGFGH3d69Q5NOWkRo+Y8p5a61sc9hZ+ItAiEL9KIbZzhnMwi12jUYCTff0bVTsTGSNUePQ2V42sToOIKCeBpUtwWKhhW3CSpK7S1iJhS9H22/BT/pk21Jd8btwMLUHfVD95iXbHNM8u6vFaYuHczx966T7gpa9RGGXRtiOr3ScJq1515tzOSOsHTPHLTun59nxxJiEjKoI4Lb9h6IlauvcGAQHp5q6/2XmxuqZdGzh39uLac8tMSmY3vC3fiHYC3iMyTb7eXqATIhDUOf9mOSbgZMS19iiVZvz8igDl950IMcelJwcj0qCLoufLE5y8ud5WIw47OCVkD7tcAEPmVWlCQ744SIM5afw+Jg50T1SEtu3q3GiL0UQ6KTLDyDEt5BL9HWXAIXsjFdPDpX1jtxZavVQV+Jd7FXhuPQuDbh12liTROREdzatYWRnrhzeOJ5Se9xeXLvYSj8DmAI4iFf2cVtWCzj/02uK4+iIGXlX7lHP1W+tycLS7Pe2RdtC2+oz5RSSqb5jI4+3iEY/vZjSMBVk69pCDzZy4ZE8LBgyEvSabJ/cddwWmShcRS+21XvGQ1uXYLv0FCTEHHobCfmn2y8bJBb/Hct53BaojWUCAwEAAQ==';
        // Create Context with API to request a SessionKey
        $context = new APIContext();
        // Api key
        $context->set_api_key('VNxhg7YhR7sXRIEvMY28o1866cDMatY9');
        // Public key
        $context->set_public_key($public_key);
        // Use ssl/https
        $context->set_ssl(true);
        // Method type (can be GET/POST/PUT)
        $context->set_method_type(APIMethodType::GET);
        // API address
        $context->set_address('openapi.m-pesa.com');
        // API Port
        $context->set_port(443);
        // API Path
        $context->set_path('/openapi/ipg/v2/vodacomLES/getSession/');
        // Add/update headers
        $context->add_header('Origin', '*');
        // Parameters can be added to the call as well that on POST will be in JSON format and on GET will be URL parameters
        // context->add_parameter('key', 'value');
        // Create a request object
        $request = new APIRequest($context);
        // Do the API call and put result in a response packet
        $response = null;
        try {
            $response = $request->execute();
        } catch (exception $e) {
            echo 'Call failed: ' . $e->getMessage() . '<br>';
        }
        if ($response->get_body() == null) {
            throw new Exception('SessionKey call failed to get result. Please check.');
        }
        $decoded = json_decode($response->get_body());
        $thirdPartyConversationID = "Mpesa" . time();
        $context = new APIContext();
        $context->set_api_key($decoded->output_SessionID);
        $context->set_public_key($public_key);
        $context->set_ssl(true);
        $context->set_method_type(APIMethodType::POST);
        $context->set_address('openapi.m-pesa.com');
        $context->set_port(443);
        $context->set_path('/openapi/ipg/v2/vodacomLES/c2bPayment/singleStage/');
        $context->add_header('Origin', '*');
        $context->add_parameter('input_Amount', $amount);
        $context->add_parameter('input_Country', 'LES');
        $context->add_parameter('input_Currency', 'LSL');
        $context->add_parameter('input_CustomerMSISDN','266'.$mobile_number);
        $context->add_parameter('input_ServiceProviderCode', '201986');
        $context->add_parameter('input_ThirdPartyConversationID', $thirdPartyConversationID);
        $context->add_parameter('input_TransactionReference', 'T1234C');
        $context->add_parameter('input_PurchasedItemsDesc', 'Examinations Fees');
        $request = new APIRequest($context);
        // SessionID can take up to 30 seconds to become 'live' in the system and will be invalid until it is
        sleep(30);
        $response = null;
        try {

            $response = $request->execute();

        } catch (Exception $e) {

            echo 'Call failed: ' . $e->getMessage() . '<br>';

        }
        if ($response->get_body() == null) {

            throw new Exception('API call failed to get result. Please check.');

        }



        return $response;

    }

}

