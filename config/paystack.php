<?php
/**
 * Paystack Configuration
 */

return [
    'public_key' => 'your-paystack-public-key',
    'secret_key' => 'your-paystack-secret-key',
    'payment_url' => 'https://api.paystack.co',
    'callback_url' => 'https://your-domain.com/paystack/callback',
    'environment' => 'test' // Change to 'live' for production
];
