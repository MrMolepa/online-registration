<?php

namespace App\Mail\Transport;

use League\OAuth2\Client\Provider\Google;
use Swift_SmtpTransport;
use Swift_TransportException;

class OAuthTransport extends Swift_SmtpTransport
{
    public function __construct($clientId, $clientSecret, $refreshToken, $email)
    {
        parent::__construct('smtp.gmail.com', 587, 'tls');

        $this->setStreamOptions([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $provider = new Google([
            'clientId'     => $clientId,
            'clientSecret' => $clientSecret,
        ]);

        try {
            $token = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $refreshToken
            ]);

            $this->setUsername($email);
            $this->setPassword($token->getToken());
            $this->setAuthMode('XOAUTH2');
            $this->setTimeout(30);  // Increased timeout
        } catch (\Exception $e) {
            throw new Swift_TransportException('OAuth2 Token Error: '.$e->getMessage());
        }
    }
}
