<?php
/**
 * EATLY API - Main entry point
 * 
 * This file serves as the main entry point for the EATLY API.
 * It handles routing and request processing for all API endpoints.
 */

// Set headers for API responses
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Start session
session_start();

// Include configuration and helper files
require_once '../config/database.php';
require_once '../includes/functions.php';

// Parse the request URI
$request_uri = $_SERVER['REQUEST_URI'];
$uri_parts = explode('/api/', $request_uri, 2);

if (count($uri_parts) < 2) {
    send_json_response(['error' => 'Invalid API request'], 400);
    exit;
}

$path = trim($uri_parts[1], '/');
$path_parts = explode('/', $path);
$endpoint = $path_parts[0] ?? '';

// Handle API requests based on endpoint
switch ($endpoint) {
    case 'recipes':
        require_once 'recipes.php';
        break;
    case 'users':
        require_once 'users.php';
        break;
    case 'bookmarks':
        require_once 'bookmarks.php';
        break;
    case 'payments':
        require_once 'payments.php';
        break;
    case '':
        // API root - show available endpoints
        $available_endpoints = [
            'recipes' => '/api/recipes',
            'users' => '/api/users',
            'bookmarks' => '/api/bookmarks',
            'payments' => '/api/payments'
        ];
        send_json_response([
            'name' => 'EATLY API',
            'version' => '1.0.0',
            'endpoints' => $available_endpoints
        ]);
        break;
    default:
        send_json_response(['error' => 'Endpoint not found'], 404);
        break;
}

/**
 * Send a JSON response with appropriate headers
 *
 * @param mixed $data The data to send as JSON
 * @param int $status_code HTTP status code
 */
function send_json_response($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}
