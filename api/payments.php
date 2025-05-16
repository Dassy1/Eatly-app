<?php
/**
 * EATLY API - Payments Endpoint
 * 
 * This file handles all payment-related API requests:
 * - GET /api/payments - Get all payments for the current user (requires authentication)
 * - GET /api/payments/methods - Get all payment methods for the current user (requires authentication)
 * - POST /api/payments/process - Process a payment (requires authentication)
 * - POST /api/payments/methods - Add a payment method (requires authentication)
 * - DELETE /api/payments/methods/{method_id} - Remove a payment method (requires authentication)
 */

// Include necessary files
require_once '../config/database.php';
require_once '../includes/functions.php';

// Include the Payment model if not already included
if (!class_exists('Payment')) {
    require_once '../models/Payment.php';
}

// Create Payment model instance
$paymentModel = new Payment($conn);

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Get method ID from URL if present
$method_id = isset($path_parts[2]) && is_numeric($path_parts[2]) ? (int)$path_parts[2] : null;

// Get the action from URL if present
$action = isset($path_parts[1]) ? $path_parts[1] : '';

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        handleGetRequest($paymentModel, $action);
        break;
    case 'POST':
        handlePostRequest($paymentModel, $action);
        break;
    case 'DELETE':
        handleDeleteRequest($paymentModel, $method_id);
        break;
    default:
        send_json_response(['error' => 'Method not allowed'], 405);
        break;
}

/**
 * Handle GET requests for payments
 *
 * @param Payment $paymentModel The Payment model instance
 * @param string $action The action to perform
 */
function handleGetRequest($paymentModel, $action) {
    // Check if user is authenticated
    if (!isLoggedIn()) {
        send_json_response(['error' => 'Authentication required'], 401);
    }
    
    $user_id = getCurrentUserId();
    
    switch ($action) {
        case 'methods':
            // Get all payment methods for the current user
            $methods = $paymentModel->getUserPaymentMethods($user_id);
            send_json_response($methods);
            break;
            
        default:
            // Get all payments for the current user
            $payments = $paymentModel->getUserPayments($user_id);
            send_json_response($payments);
            break;
    }
}

/**
 * Handle POST requests for payments
 *
 * @param Payment $paymentModel The Payment model instance
 * @param string $action The action to perform
 */
function handlePostRequest($paymentModel, $action) {
    // Check if user is authenticated
    if (!isLoggedIn()) {
        send_json_response(['error' => 'Authentication required'], 401);
    }
    
    // Get JSON data from request body
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    if (!$data) {
        send_json_response(['error' => 'Invalid JSON data'], 400);
    }
    
    $user_id = getCurrentUserId();
    
    switch ($action) {
        case 'process':
            // Process a payment
            if (!isset($data['payment_method']) || !isset($data['amount']) || !isset($data['payment_details'])) {
                send_json_response(['error' => 'Missing required payment information'], 400);
            }
            
            $result = $paymentModel->processPayment(
                $user_id,
                $data['payment_method'],
                $data['amount'],
                $data['payment_details']
            );
            
            if (!$result['status']) {
                send_json_response(['error' => $result['errors'][0]], 400);
            }
            
            send_json_response([
                'message' => $result['message'],
                'payment_id' => $result['payment_id'],
                'transaction_id' => $result['transaction_id']
            ]);
            break;
            
        case 'methods':
            // Add a payment method
            if (!isset($data['type']) || !isset($data['details'])) {
                send_json_response(['error' => 'Missing required payment method information'], 400);
            }
            
            $result = $paymentModel->addPaymentMethod(
                $user_id,
                $data['type'],
                $data['details']
            );
            
            if (!$result['status']) {
                send_json_response(['error' => $result['errors'][0]], 400);
            }
            
            send_json_response([
                'message' => $result['message'],
                'method_id' => $result['method_id']
            ]);
            break;
            
        default:
            send_json_response(['error' => 'Invalid endpoint'], 404);
            break;
    }
}

/**
 * Handle DELETE requests to remove a payment method
 *
 * @param Payment $paymentModel The Payment model instance
 * @param int|null $method_id The payment method ID
 */
function handleDeleteRequest($paymentModel, $method_id) {
    // Check if user is authenticated
    if (!isLoggedIn()) {
        send_json_response(['error' => 'Authentication required'], 401);
    }
    
    // Check if method ID is provided
    if (!$method_id) {
        send_json_response(['error' => 'Payment method ID is required'], 400);
    }
    
    $user_id = getCurrentUserId();
    
    // Remove payment method
    $result = $paymentModel->removePaymentMethod($user_id, $method_id);
    
    if (!$result['status']) {
        send_json_response(['error' => $result['errors'][0]], 400);
    }
    
    send_json_response(['message' => $result['message']]);
}
?>
