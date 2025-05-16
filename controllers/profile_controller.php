<?php
/**
 * Profile Controller
 */

// Include User and Recipe models
require_once 'models/User.php';
require_once 'models/Recipe.php';

// Initialize variables
$errors = [];
$success = '';
$user = null;
$userRecipes = [];

// Create model instances
$userModel = new User($conn);
$recipeModel = new Recipe($conn);

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login');
}

// Get current user
$user = $userModel->getById($_SESSION['user_id']);

if (!$user) {
    // User not found, log out
    session_unset();
    session_destroy();
    redirect('login', ['error' => 'User not found. Please log in again.']);
}

// Get user recipes
$userRecipes = $recipeModel->getUserRecipes($_SESSION['user_id']);

// Handle profile update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Check if passwords match if provided
    if (!empty($password) && $password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    } else {
        $userData = [
            'username' => $username,
            'email' => $email
        ];
        
        if (!empty($password)) {
            $userData['password'] = $password;
        }
        
        $result = $userModel->update($_SESSION['user_id'], $userData);
        
        if ($result['status']) {
            $success = $result['message'];
            
            // Refresh user data
            $user = $userModel->getById($_SESSION['user_id']);
        } else {
            $errors = $result['errors'];
        }
    }
}

// Handle success message from redirect
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

// Handle error message from redirect
if (isset($_GET['error'])) {
    $errors[] = $_GET['error'];
}
?>
