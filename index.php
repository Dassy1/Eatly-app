<?php
// Start session
session_start();

// Include configuration and helper files
require_once 'config/database.php';
require_once 'includes/functions.php';

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get database connection
$conn = getDbConnection();

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Include auth controller
    include_once 'controllers/auth_controller.php';
    
    // If login was successful, redirect to home
    if (isset($_SESSION['user_id'])) {
        header('Location: index.php?page=home');
        exit;
    }
}

// Determine which page to display
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Include header
include_once 'views/partials/header.php';

// Load the appropriate page
switch ($page) {
    case 'home':
        include_once 'views/home.php';
        break;
    case 'search':
        include_once 'controllers/search_controller.php';
        break;
    case 'recipe':
        include_once 'controllers/recipe_controller.php';
        break;
    case 'login':
        include_once 'controllers/auth_controller.php';
        include_once 'views/login.php';
        break;
    case 'register':
        include_once 'controllers/auth_controller.php';
        include_once 'views/register.php';
        break;
    case 'profile':
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        include_once 'controllers/profile_controller.php';
        include_once 'views/profile.php';
        break;
    case 'add-recipe':
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        include_once 'controllers/recipe_controller.php';
        include_once 'views/add_recipe.php';
        break;
    case 'payment-methods':
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        include_once 'views/payment_methods.php';
        break;
    case 'bookmarks':
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        include_once 'controllers/bookmark_controller.php';
        include_once 'views/bookmarks.php';
        break;
    default:
        include_once 'views/404.php';
        break;
}

// Include footer
include_once 'views/partials/footer.php';
?>
