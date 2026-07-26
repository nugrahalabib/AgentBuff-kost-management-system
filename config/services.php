<?php

return [

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Gerbang marketplace AgentBuff — hanya pembeli terdaftar + akses aktif yang
    // boleh masuk. Lihat App\Services\AgentBuffGate. gate_disabled=true hanya untuk
    // menjalankan KostCloud standalone (dev tanpa AgentBuff).
    'agentbuff' => [
        'entitlement_url' => env('AGENTBUFF_ENTITLEMENT_URL'),
        'partner_secret' => env('AGENTBUFF_PARTNER_SECRET'),
        'product_key' => env('KOSTCLOUD_PRODUCT_KEY', 'kostcloud'),
        'gate_disabled' => env('AGENTBUFF_GATE_DISABLED', false),
    ],

];
