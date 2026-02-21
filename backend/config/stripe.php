<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe API Keys
    |--------------------------------------------------------------------------
    */

    'secret_key' => env('STRIPE_SECRET_KEY'),
    'public_key' => env('STRIPE_PUBLIC_KEY'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Price IDs (Test Mode)
    |--------------------------------------------------------------------------
    | Product: Adeguati Assetti (prod_TuOHu3WrI6qvJh)
    */

    'prices' => [
        // New business model: single plan for clients
        'impresa49' => [
            'monthly' => env('STRIPE_PRICE_IMPRESA49_MONTHLY'),
        ],
        // Legacy plans (kept for existing subscribers)
        'pro' => [
            'monthly' => env('STRIPE_PRICE_PRO_MONTHLY', 'price_1SwZUo1Aln4lEvRNJLHjSYPn'),
            'annual' => env('STRIPE_PRICE_PRO_ANNUAL', 'price_1SwZUp1Aln4lEvRNxNO23A98'),
        ],
        'studio' => [
            'monthly' => env('STRIPE_PRICE_STUDIO_MONTHLY', 'price_1SwZUp1Aln4lEvRNzhbFJ1AA'),
            'annual' => env('STRIPE_PRICE_STUDIO_ANNUAL', 'price_1SwZUp1Aln4lEvRNdSVXzzwK'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Events
    |--------------------------------------------------------------------------
    */

    'webhook_events' => [
        'checkout.session.completed',
        'customer.subscription.deleted',
        'invoice.payment_failed',
        'customer.subscription.updated',
    ],
];
