<?php
/**
 * Bookmark Controller
 */

// Include Bookmark model
require_once 'models/Bookmark.php';

// Initialize variables
$errors = [];
$success = '';
$bookmarks = [];

// Create Bookmark model instance
$bookmarkModel = new Bookmark($conn);

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login');
}

// Handle bookmark action
if (isset($_GET['action']) && isset($_GET['recipe_id'])) {
    $recipeId = (int)$_GET['recipe_id'];
    $userId = getCurrentUserId();
    
    if ($_GET['action'] === 'add') {
        // Add bookmark
        $result = $bookmarkModel->add($userId, $recipeId);
        
        if ($result['status']) {
            $success = $result['message'];
        } else {
            $errors = $result['errors'];
        }
    } elseif ($_GET['action'] === 'remove') {
        // Remove bookmark
        $result = $bookmarkModel->remove($userId, $recipeId);
        
        if ($result['status']) {
            $success = $result['message'];
        } else {
            $errors = $result['errors'];
        }
    }
    
    // If AJAX request, return JSON response
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => empty($errors),
            'message' => empty($errors) ? $success : $errors[0]
        ]);
        exit;
    }
    
    // Otherwise, redirect back to the recipe page
    redirect('recipe', ['id' => $recipeId]);
}

// Get user bookmarks
$bookmarks = $bookmarkModel->getUserBookmarks(getCurrentUserId());

// Handle success message from redirect
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

// Handle error message from redirect
if (isset($_GET['error'])) {
    $errors[] = $_GET['error'];
}
?>
