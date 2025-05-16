<?php
/**
 * Payment Model
 */
class Payment {
    private $conn;
    private $paystack;
    
    // Constructor
    public function __construct($conn) {
        $this->conn = $conn;
        require_once dirname(__FILE__) . '/../controllers/PaystackController.php';
        $this->paystack = new PaystackController($conn);
    }
    
    /**
     * Get payment methods for a user
     * 
     * @param int $userId User ID
     * @return array Payment methods
     */
    public function getPaymentMethods($userId) {
        $sql = "SELECT * FROM payment_methods WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $methods = [];
        while ($row = $result->fetch_assoc()) {
            $methods[] = [
                'id' => $row['id'],
                'type' => $row['type'],
                'last_four' => substr($row['card_number'], -4),
                'expiry_date' => $row['expiry_date'],
                'created_at' => $row['created_at']
            ];
        }
        
        return $methods;
    }
    
    /**
     * Process a payment
     * 
     * @param int $userId User ID
     * @param string $paymentMethod Payment method (credit_card, paypal, etc.)
     * @param float $amount Amount to charge
     * @param array $paymentDetails Payment details (card number, expiry, etc.)
     * @return array Result with status and message
     */
    public function processPayment($userId, $paymentMethod, $amount, $paymentDetails) {
        // Validate input
        $errors = [];
        
        if (empty($userId) || !is_numeric($userId)) {
            $errors[] = "User ID is required";
        }
        
        if (empty($paymentMethod)) {
            $errors[] = "Payment method is required";
        }
        
        if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
            $errors[] = "Valid amount is required";
        }
        
        if (empty($paymentDetails)) {
            $errors[] = "Payment details are required";
        }
        
        if (!empty($errors)) {
            return [
                'status' => false,
                'errors' => $errors
            ];
        }

        // Convert amount to kobo (multiply by 100)
        $amountInKobo = $amount * 100;

        // Generate a unique reference
        $reference = uniqid('ref_');

        // Store the payment in the database with pending status
        $sql = "INSERT INTO payments (user_id, payment_method, amount, transaction_id, status) 
                VALUES (?, ?, ?, ?, 'pending')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isds", $userId, $paymentMethod, $amount, $reference);
        
        if (!$stmt->execute()) {
            return [
                'status' => false,
                'errors' => ["Failed to process payment: " . $stmt->error]
            ];
        }

        // For bank transfer, use Paystack
        if ($paymentMethod === 'bank_transfer') {
            // Initialize bank transfer with Paystack
            $paystackResponse = $this->paystack->initializeBankTransfer(
                $amountInKobo,
                $paymentDetails['email'],
                $reference
            );

            if (!$paystackResponse['status']) {
                return [
                    'status' => false,
                    'errors' => ['Failed to initialize bank transfer: ' . $paystackResponse['message']]
                ];
            }

            // Return Paystack response with redirect URL
            return [
                'status' => true,
                'message' => "Bank transfer initialized successfully",
                'redirect_url' => $paystackResponse['data']['authorization_url'],
                'reference' => $reference
            ];
        }

        // For other payment methods, maintain existing logic
        $paymentId = $stmt->insert_id;
        
        return [
            'status' => true,
            'message' => "Payment processed successfully",
            'payment_id' => $paymentId,
            'transaction_id' => $reference
        ];
    }
    
    /**
     * Get payment by ID
     * 
     * @param int $paymentId Payment ID
     * @return array|null Payment data or null if not found
     */
    public function getById($paymentId) {
        $sql = "SELECT * FROM payments WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $paymentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get user payments
     * 
     * @param int $userId User ID
     * @return array User payments
     */
    public function getUserPayments($userId) {
        $sql = "SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $payments = [];
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
        
        return $payments;
    }
    
    /**
     * Get payment methods for a user
     * 
     * @param int $userId User ID
     * @return array Payment methods
     */
    public function getUserPaymentMethods($userId) {
        $sql = "SELECT * FROM payment_methods WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $methods = [];
        while ($row = $result->fetch_assoc()) {
            // Mask sensitive information
            if (isset($row['card_number'])) {
                $row['card_number'] = 'xxxx-xxxx-xxxx-' . substr($row['card_number'], -4);
            }
            $methods[] = $row;
        }
        
        return $methods;
    }
    
    /**
     * Add a payment method
     * 
     * @param int $userId User ID
     * @param string $type Payment method type (credit_card, paypal, etc.)
     * @param array $details Payment method details
     * @return array Result with status and message
     */
    public function addPaymentMethod($userId, $type, $details) {
        // Validate input
        $errors = [];
        
        if (empty($userId) || !is_numeric($userId)) {
            $errors[] = "User ID is required";
        }
        
        if (empty($type)) {
            $errors[] = "Payment method type is required";
        }
        
        if (empty($details)) {
            $errors[] = "Payment method details are required";
        }
        
        if (!empty($errors)) {
            return [
                'status' => false,
                'errors' => $errors
            ];
        }
        
        // Process based on payment method type
        switch ($type) {
            case 'credit_card':
                // Validate credit card details
                if (!isset($details['card_number']) || !isset($details['expiry_month']) || 
                    !isset($details['expiry_year']) || !isset($details['cvv'])) {
                    return [
                        'status' => false,
                        'errors' => ["Missing required credit card details"]
                    ];
                }
                
                // In a real application, we would validate the card details with a payment processor
                // For this example, we'll just store the details (securely in a real app)
                
                // Store the payment method
                $sql = "INSERT INTO payment_methods (user_id, type, card_number, expiry_month, expiry_year, name_on_card) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("isssss", 
                    $userId, 
                    $type, 
                    $details['card_number'], 
                    $details['expiry_month'], 
                    $details['expiry_year'], 
                    $details['name_on_card']
                );
                break;
                
            case 'paypal':
                // Validate PayPal details
                if (!isset($details['email'])) {
                    return [
                        'status' => false,
                        'errors' => ["PayPal email is required"]
                    ];
                }
                
                // Store the payment method
                $sql = "INSERT INTO payment_methods (user_id, type, paypal_email) 
                        VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("iss", 
                    $userId, 
                    $type, 
                    $details['email']
                );
                break;
                
            default:
                return [
                    'status' => false,
                    'errors' => ["Unsupported payment method type"]
                ];
        }
        
        if ($stmt->execute()) {
            $methodId = $stmt->insert_id;
            
            return [
                'status' => true,
                'message' => "Payment method added successfully",
                'method_id' => $methodId
            ];
        } else {
            return [
                'status' => false,
                'errors' => ["Failed to add payment method: " . $stmt->error]
            ];
        }
    }
    
    /**
     * Remove a payment method
     * 
     * @param int $userId User ID
     * @param int $methodId Payment method ID
     * @return array Result with status and message
     */
    public function removePaymentMethod($userId, $methodId) {
        // Check if payment method exists and belongs to user
        $sql = "SELECT id FROM payment_methods WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $methodId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'status' => false,
                'errors' => ["Payment method not found or doesn't belong to you"]
            ];
        }
        
        // Remove the payment method
        $sql = "DELETE FROM payment_methods WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $methodId);
        
        if ($stmt->execute()) {
            return [
                'status' => true,
                'message' => "Payment method removed successfully"
            ];
        } else {
            return [
                'status' => false,
                'errors' => ["Failed to remove payment method: " . $stmt->error]
            ];
        }
    }
}
?>
