<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use League\OAuth2\Client\Provider\Google;

class GetGoogleOAuthToken extends Command
{
    protected $signature = 'google:get-token';
    protected $description = 'Get Google OAuth2 token';

    public function handle()
    {

        $web_url= env('APP_URL');
        $clientId = config('mail.mailers.gmail.client_id');
        $clientSecret = config('mail.mailers.gmail.client_secret');
        $redirectUri = "http://127.0.0.1:8000/auth/google/callback";

        // config('mail.mailers.gmail.client_id'),
        // config('mail.mailers.gmail.client_secret'),
        // config('mail.mailers.gmail.refresh_token'),
        // config('mail.mailers.gmail.email')
        $provider = new Google([
            'clientId'     => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri'  => $redirectUri,
        ]);

        $authUrl = $provider->getAuthorizationUrl([
            'scope' => ['https://mail.google.com/', 'https://www.googleapis.com/auth/gmail.send'],
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);

        $this->info("Open this URL in your browser and authenticate:");
        $this->line($authUrl);

        $authCode = $this->ask('Enter the authorization code from the URL:');

        try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $authCode
            ]);

            $this->info("Refresh Token: " . $accessToken->getRefreshToken());
            $this->info("Add this to your .env as GOOGLE_REFRESH_TOKEN");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
