<?php
/**
 * Search Controller
 */

// Include Recipe model
require_once 'models/Recipe.php';

// Initialize variables
$query = '';
$recipes = [];
$totalResults = 0;
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$errors = []; // Initialize errors array

// Get database connection
$conn = getDbConnection();
if (!$conn) {
    $errors[] = 'Database connection failed';
}

// Create Recipe model instance
$recipeModel = new Recipe($conn);

// Handle search query
$query = isset($_GET['q']) ? sanitize($_GET['q']) : '';

if (!empty($query)) {
    // Get recipes matching the query
    $recipes = $recipeModel->search($query, $limit, $offset);
    
    // Count total results for pagination
    $sql = "SELECT COUNT(DISTINCT r.id) as total 
            FROM recipes r 
            LEFT JOIN ingredients i ON r.id = i.recipe_id 
            WHERE r.title LIKE ? 
            OR r.publisher LIKE ? 
            OR i.description LIKE ? 
            OR r.source_url LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$query%";
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalResults = $row['total'];
} else {
    // If no query, show all recipes
    $recipes = $recipeModel->getAllRecipes($limit, $offset);
    $totalResults = $recipeModel->getRecipeCount();
}

// Calculate pagination
$totalPages = ceil($totalResults / $limit);
$prevPage = $page > 1 ? $page - 1 : null;
$nextPage = $page < $totalPages ? $page + 1 : null;

// If no recipes found and query exists, show error
if (empty($recipes) && !empty($query)) {
    $errors[] = 'No recipes found for "' . htmlspecialchars($query) . '"';
}
?>
