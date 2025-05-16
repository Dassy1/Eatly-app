<?php
/**
 * EATLY API - Bookmarks Endpoint
 * 
 * This file handles all bookmark-related API requests:
 * - GET /api/bookmarks - Get all bookmarks for the current user (requires authentication)
 * - POST /api/bookmarks - Add a recipe to bookmarks (requires authentication)
 * - DELETE /api/bookmarks/{recipe_id} - Remove a recipe from bookmarks (requires authentication)
 */

// Include necessary files
require_once '../config/database.php';
require_once '../includes/functions.php';

// Include the Bookmark model if not already included
if (!class_exists('Bookmark')) {
    require_once '../models/Bookmark.php';
}

// Create Bookmark model instance
$bookmarkModel = new Bookmark($conn);

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Get recipe ID from URL if present
$recipe_id = isset($path_parts[1]) && is_numeric($path_parts[1]) ? (int)$path_parts[1] : null;

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        handleGetRequest($bookmarkModel);
        break;
    case 'POST':
        handlePostRequest($bookmarkModel);
        break;
    case 'DELETE':
        handleDeleteRequest($bookmarkModel, $recipe_id);
        break;
    default:
        send_json_response(['error' => 'Method not allowed'], 405);
        break;
}

/**
 * Handle GET requests for bookmarks
 *
 * @param Bookmark $bookmarkModel The Bookmark model instance
 */
function handleGetRequest($bookmarkModel) {
    // Check if user is authenticated
    if (!isLoggedIn()) {
        send_json_response(['error' => 'Authentication required'], 401);
    }
    
    $user_id = getCurrentUserId();
    
    // Get all bookmarks for the current user
    $bookmarks = $bookmarkModel->getUserBookmarks($user_id);
    
    send_json_response($bookmarks);
}

/**
 * Handle POST requests to add a recipe to bookmarks
 *
 * @param Bookmark $bookmarkModel The Bookmark model instance
 */
function handlePostRequest($bookmarkModel) {
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
    
    // Validate required fields
    if (!isset($data['recipe_id']) || !is_numeric($data['recipe_id'])) {
        send_json_response(['error' => 'Recipe ID is required and must be numeric'], 400);
    }
    
    $user_id = getCurrentUserId();
    $recipe_id = (int)$data['recipe_id'];
    
    // Add bookmark
    $result = $bookmarkModel->add($user_id, $recipe_id);
    
    if (!$result['status']) {
        send_json_response(['error' => $result['errors'][0]], 400);
    }
    
    send_json_response(['message' => $result['message']]);
}

/**
 * Handle DELETE requests to remove a recipe from bookmarks
 *
 * @param Bookmark $bookmarkModel The Bookmark model instance
 * @param int|null $recipe_id The recipe ID
 */
function handleDeleteRequest($bookmarkModel, $recipe_id) {
    // Check if user is authenticated
    if (!isLoggedIn()) {
        send_json_response(['error' => 'Authentication required'], 401);
    }
    
    // Check if recipe ID is provided
    if (!$recipe_id) {
        send_json_response(['error' => 'Recipe ID is required'], 400);
    }
    
    $user_id = getCurrentUserId();
    
    // Remove bookmark
    $result = $bookmarkModel->remove($user_id, $recipe_id);
    
    if (!$result['status']) {
        send_json_response(['error' => $result['errors'][0]], 400);
    }
    
    send_json_response(['message' => $result['message']]);
}
