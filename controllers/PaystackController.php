<?php
/**
 * Paystack Controller for handling bank transfer payments
 */

class PaystackController {
    private $config;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        require_once dirname(__FILE__) . '/../config/paystack.php';
        $this->config = require 'config/paystack.php';
    }

    /**
     * Initialize bank transfer payment
     * 
     * @param float $amount Amount in kobo (multiply naira by 100)
     * @param string $email User's email
     * @param string $reference Unique transaction reference
     * @return array Response from Paystack
     */
    public function initializeBankTransfer($amount, $email, $reference) {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->config['payment_url'] . '/transaction/initialize',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'email' => $email,
                'amount' => $amount,
                'reference' => $reference,
                'callback_url' => $this->config['callback_url'],
                'channels' => ['bank_transfer'],
                'metadata' => [
                    'custom_fields' => [
                        [
                            'display_name' => 'Payment For',
                            'variable_name' => 'payment_for',
                            'value' => 'Recipe Subscription'
                        ]
                    ]
                ]
            ]),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->config['secret_key'],
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return ['status' => false, 'message' => 'Curl error: ' . $err];
        }

        return json_decode($response, true);
    }

    /**
     * Verify bank transfer payment
     * 
     * @param string $reference Transaction reference
     * @return array Payment verification response
     */
    public function verifyPayment($reference) {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->config['payment_url'] . '/transaction/verify/' . $reference,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->config['secret_key'],
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return ['status' => false, 'message' => 'Curl error: ' . $err];
        }

        return json_decode($response, true);
    }

    /**
     * Handle callback from Paystack
     */
    public function handleCallback() {
        $reference = $_GET['reference'] ?? null;
        if (!$reference) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Reference required']);
            return;
        }

        $verification = $this->verifyPayment($reference);
        
        if ($verification['status'] && $verification['data']['status'] === 'success') {
            // Update payment status in database
            $sql = "UPDATE payments SET status = 'completed' WHERE transaction_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $reference);
            $stmt->execute();

            echo json_encode(['status' => true, 'message' => 'Payment verified']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Payment verification failed']);
        }
    }
}
