<?php
require_once '../config/database.php';
require_once '../controllers/PaystackController.php';

// Get database connection
$conn = getDbConnection();

// Create Paystack controller
$paystack = new PaystackController($conn);

// Handle callback
$paystack->handleCallback();
