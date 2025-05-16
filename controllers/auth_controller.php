<?php
/**
 * Authentication Controller
 */

// Get the root directory
$root = dirname(dirname(__FILE__));

// Include necessary files
require_once $root . '/config/database.php';
require_once $root . '/includes/functions.php';
require_once $root . '/models/User.php';

// Get database connection
$conn = getDbConnection();

// Initialize variables
$errors = [];
$success = '';
$user = null;

// Create User model instance
$userModel = new User($conn);

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    $result = $userModel->login($username, $password);
    
    if ($result['status']) {
        // Set session variables
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['username'] = $result['username'];
        
        // Redirect to home page after successful login
        redirect('home');
    } else {
        $errors = $result['errors'];
    }
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Check if passwords match
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    } else {
        $result = $userModel->register($username, $email, $password);
        
        if ($result['status']) {
            // Set session variables after successful registration
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['username'] = $username;
            
            // Redirect to home page
            redirect('home', ['success' => 'Registration successful!']);
        } else {
            $errors = $result['errors'];
        }
    }
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy session
    session_destroy();
    
    // Redirect to home page
    redirect('home', ['success' => 'You have been logged out successfully']);
}

// Get current user if logged in
if (isLoggedIn()) {
    $user = $userModel->getById($_SESSION['user_id']);
}

// Handle success message from redirect
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
?>
