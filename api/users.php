<?php
/**
 * EATLY API - Users Endpoint
 * 
 * This file handles all user-related API requests:
 * - GET /api/users/profile - Get the current user's profile (requires authentication)
 * - POST /api/users/login - Login a user
 * - POST /api/users/register - Register a new user
 * - PUT /api/users/profile - Update the current user's profile (requires authentication)
 */

// Include necessary files
require_once '../config/database.php';
require_once '../includes/functions.php';

// Include the User model if not already included
if (!class_exists('User')) {
    require_once '../models/User.php';
}

// Create User model instance
$userModel = new User($conn);

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Get the action from URL if present
$action = isset($path_parts[1]) ? $path_parts[1] : '';

// Handle different HTTP methods and actions
switch ($method) {
    case 'GET':
        handleGetRequest($userModel, $action);
        break;
    case 'POST':
        handlePostRequest($userModel, $action);
        break;
    case 'PUT':
        handlePutRequest($userModel, $action);
        break;
    default:
        send_json_response(['error' => 'Method not allowed'], 405);
        break;
}

/**
 * Handle GET requests for users
 *
 * @param User $userModel The User model instance
 * @param string $action The action to perform
 */
function handleGetRequest($userModel, $action) {
    switch ($action) {
        case 'profile':
            // Get current user's profile (requires authentication)
            if (!isLoggedIn()) {
                send_json_response(['error' => 'Authentication required'], 401);
            }
            
            $user_id = getCurrentUserId();
            $user = $userModel->getById($user_id);
            
            if (!$user) {
                send_json_response(['error' => 'User not found'], 404);
            }
            
            // Remove sensitive data
            unset($user['password']);
            
            send_json_response($user);
            break;
            
        default:
            send_json_response(['error' => 'Invalid endpoint'], 404);
            break;
    }
}

/**
 * Handle POST requests for users
 *
 * @param User $userModel The User model instance
 * @param string $action The action to perform
 */
function handlePostRequest($userModel, $action) {
    switch ($action) {
        case 'login':
            // Login a user
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            
            if (!$data) {
                send_json_response(['error' => 'Invalid JSON data'], 400);
            }
            
            // Validate required fields
            if (!isset($data['username']) || !isset($data['password'])) {
                send_json_response(['error' => 'Username and password are required'], 400);
            }
            
            // Attempt login
            $username = sanitize($data['username']);
            $password = $data['password'];
            
            $result = $userModel->login($username, $password);
            
            if (!$result['status']) {
                send_json_response(['error' => $result['errors'][0]], 401);
            }
            
            // Generate API token
            $token = bin2hex(random_bytes(32));
            $expiry = time() + (86400 * 30); // 30 days
            
            // Store token in session
            $_SESSION['api_token'] = $token;
            $_SESSION['api_token_expiry'] = $expiry;
            
            // Get user data
            $user = $userModel->getById($result['user_id']);
            
            // Remove sensitive data
            unset($user['password']);
            
            send_json_response([
                'user' => $user,
                'token' => $token,
                'expires_at' => date('Y-m-d H:i:s', $expiry)
            ]);
            break;
            
        case 'register':
            // Register a new user
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            
            if (!$data) {
                send_json_response(['error' => 'Invalid JSON data'], 400);
            }
            
            // Validate required fields
            $required_fields = ['username', 'email', 'password', 'confirm_password'];
            foreach ($required_fields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    send_json_response(['error' => "Missing required field: $field"], 400);
                }
            }
            
            // Check if passwords match
            if ($data['password'] !== $data['confirm_password']) {
                send_json_response(['error' => 'Passwords do not match'], 400);
            }
            
            // Register user
            $result = $userModel->register($data['username'], $data['email'], $data['password']);
            
            if (!$result['status']) {
                send_json_response(['error' => $result['errors'][0]], 400);
            }
            
            // Get the registered user
            $user = $userModel->getById($result['user_id']);
            
            // Remove sensitive data
            unset($user['password']);
            
            send_json_response($user, 201);
            break;
            
        case 'logout':
            // Logout a user
            if (!isLoggedIn()) {
                send_json_response(['error' => 'Not logged in'], 400);
            }
            
            // Clear session
            session_unset();
            session_destroy();
            
            send_json_response(['message' => 'Logged out successfully']);
            break;
            
        default:
            send_json_response(['error' => 'Invalid endpoint'], 404);
            break;
    }
}

/**
 * Handle PUT requests for users
 *
 * @param User $userModel The User model instance
 * @param string $action The action to perform
 */
function handlePutRequest($userModel, $action) {
    switch ($action) {
        case 'profile':
            // Update current user's profile (requires authentication)
            if (!isLoggedIn()) {
                send_json_response(['error' => 'Authentication required'], 401);
            }
            
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            
            if (!$data) {
                send_json_response(['error' => 'Invalid JSON data'], 400);
            }
            
            $user_id = getCurrentUserId();
            
            // Update user
            $result = $userModel->update($user_id, $data);
            
            if (!$result['status']) {
                send_json_response(['error' => $result['errors'][0]], 400);
            }
            
            // Get the updated user
            $user = $userModel->getById($user_id);
            
            // Remove sensitive data
            unset($user['password']);
            
            send_json_response($user);
            break;
            
        default:
            send_json_response(['error' => 'Invalid endpoint'], 404);
            break;
    }
}
