<?php
/**
 * Helper functions for the application
 */

/**
 * Sanitize user input
 * 
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Redirect to a specific page
 * 
 * @param string $page Page to redirect to
 * @param array $params Optional URL parameters
 * @return void
 */
function redirect($page, $params = []) {
    $url = 'index.php?page=' . $page;
    
    if (!empty($params)) {
        foreach ($params as $key => $value) {
            $url .= '&' . $key . '=' . urlencode($value);
        }
    }
    
    header('Location: ' . $url);
    exit;
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 * 
 * @return int|null User ID if logged in, null otherwise
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Format recipe data for display
 * 
 * @param array $recipe Recipe data
 * @return array Formatted recipe data
 */
function formatRecipe($recipe) {
    // Calculate cooking time in hours and minutes
    $hours = floor($recipe['cooking_time'] / 60);
    $minutes = $recipe['cooking_time'] % 60;
    
    $recipe['formatted_time'] = '';
    if ($hours > 0) {
        $recipe['formatted_time'] .= $hours . ' hr ';
    }
    if ($minutes > 0) {
        $recipe['formatted_time'] .= $minutes . ' min';
    }
    
    return $recipe;
}

/**
 * Fetch recipe ingredients
 * 
 * @param int $recipeId Recipe ID
 * @param mysqli $conn Database connection
 * @return array Ingredients for the recipe
 */
function getRecipeIngredients($recipeId, $conn) {
    $sql = "SELECT * FROM ingredients WHERE recipe_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $ingredients = [];
    while ($row = $result->fetch_assoc()) {
        $ingredients[] = $row;
    }
    
    return $ingredients;
}

/**
 * Check if a recipe is bookmarked by the current user
 * 
 * @param int $recipeId Recipe ID
 * @param mysqli $conn Database connection
 * @return bool True if bookmarked, false otherwise
 */
function isBookmarked($recipeId, $conn) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userId = getCurrentUserId();
    $sql = "SELECT id FROM bookmarks WHERE user_id = ? AND recipe_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $recipeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

/**
 * Format error messages for display
 * 
 * @param array $errors Array of error messages
 * @return string HTML formatted error messages
 */
function formatErrors($errors) {
    if (empty($errors)) {
        return '';
    }
    
    $html = '<div class="alert alert-danger" role="alert">';
    $html .= '<ul class="mb-0">';
    
    foreach ($errors as $error) {
        $html .= '<li>' . $error . '</li>';
    }
    
    $html .= '</ul>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Format success message for display
 * 
 * @param string $message Success message
 * @return string HTML formatted success message
 */
function formatSuccess($message) {
    if (empty($message)) {
        return '';
    }
    
    $html = '<div class="alert alert-success" role="alert">';
    $html .= $message;
    $html .= '</div>';
    
    return $html;
}
?>
