<?php
/**
 * Recipe Controller
 */

// Include Recipe model
require_once 'models/Recipe.php';

// Initialize variables
$errors = [];
$success = '';
$recipe = null;
$recipes = [];

// Create Recipe model instance
$recipeModel = new Recipe($conn);

// Handle search
if (isset($_GET['page']) && $_GET['page'] === 'search' && isset($_GET['query'])) {
    $query = sanitize($_GET['query']);
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    
    // Get total results for pagination
    $totalResults = $recipeModel->getRecipeCount();
    $totalPages = ceil($totalResults / $limit);
    $prevPage = $page > 1 ? $page - 1 : null;
    $nextPage = $page < $totalPages ? $page + 1 : null;
    
    $recipes = $recipeModel->search($query, $limit, $offset);
    
    if (empty($recipes)) {
        $errors[] = 'No recipes found';
    } else {
        // Calculate offset for pagination
        $offset = ($page - 1) * $limit;
    }
}

// Handle recipe view
if (isset($_GET['page']) && $_GET['page'] === 'recipe' && isset($_GET['id'])) {
    $recipeId = (int)$_GET['id'];
    $recipe = $recipeModel->getById($recipeId);
    
    if (!$recipe) {
        $errors[] = 'Recipe not found';
    }
    
    // Include recipe view
    include_once 'views/recipe.php';
    exit;
}

// Handle recipe creation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_recipe'])) {
    // Check if user is logged in
    if (!isLoggedIn()) {
        redirect('login');
    }
    
    // Get form data
    $title = sanitize($_POST['title']);
    $imageUrl = sanitize($_POST['image_url']);
    $sourceUrl = sanitize($_POST['source_url']);
    $publisher = sanitize($_POST['publisher']);
    $cookingTime = (int)$_POST['cooking_time'];
    $servings = (int)$_POST['servings'];
    
    // Process ingredients
    $ingredients = [];
    $ingredientCount = count($_POST['ingredient_description']);
    
    for ($i = 0; $i < $ingredientCount; $i++) {
        if (!empty($_POST['ingredient_description'][$i])) {
            $ingredients[] = [
                'quantity' => !empty($_POST['ingredient_quantity'][$i]) ? (float)$_POST['ingredient_quantity'][$i] : null,
                'unit' => !empty($_POST['ingredient_unit'][$i]) ? sanitize($_POST['ingredient_unit'][$i]) : null,
                'description' => sanitize($_POST['ingredient_description'][$i])
            ];
        }
    }
    
    // Create recipe data
    $recipeData = [
        'title' => $title,
        'image_url' => $imageUrl,
        'source_url' => $sourceUrl,
        'publisher' => $publisher,
        'cooking_time' => $cookingTime,
        'servings' => $servings,
        'ingredients' => $ingredients
    ];
    
    // Create recipe
    $result = $recipeModel->create(
        $title,
        $imageUrl,
        $sourceUrl,
        $publisher,
        $cookingTime,
        $servings,
        $ingredients
    );
    
    if ($result) {
        redirect('recipe', ['id' => $recipeId, 'success' => 'Recipe created successfully']);
    } else {
        $errors[] = 'Failed to create recipe';
    }
}

// Handle recipe update form submission
if (isset($_GET['page']) && $_GET['page'] === 'search' && isset($_GET['query'])) {
    $query = sanitize($_GET['query']);
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    $recipes = $recipeModel->search($query, $limit, $offset);
    
    if (empty($recipes)) {
        $errors[] = 'No recipes found';
    }
}

// Handle recipe update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_recipe'])) {
    // Check if user is logged in
    if (!isLoggedIn()) {
        redirect('login');
    }
    
    $recipeId = (int)$_POST['recipe_id'];
    
    // Get form data
    $title = sanitize($_POST['title']);
    $imageUrl = sanitize($_POST['image_url']);
    $sourceUrl = sanitize($_POST['source_url']);
    $publisher = sanitize($_POST['publisher']);
    $cookingTime = (int)$_POST['cooking_time'];
    $servings = (int)$_POST['servings'];
    $ingredients = [];
    
    // Process ingredients
    $ingredientCount = count($_POST['ingredient_description']);
    
    for ($i = 0; $i < $ingredientCount; $i++) {
        if (!empty($_POST['ingredient_description'][$i])) {
            $ingredients[] = [
                'quantity' => !empty($_POST['ingredient_quantity'][$i]) ? (float)$_POST['ingredient_quantity'][$i] : null,
                'unit' => !empty($_POST['ingredient_unit'][$i]) ? sanitize($_POST['ingredient_unit'][$i]) : null,
                'description' => sanitize($_POST['ingredient_description'][$i])
            ];
        }
    }
    
    // Update recipe
    $result = $recipeModel->update(
        $recipeId,
        $title,
        $imageUrl,
        $sourceUrl,
        $publisher,
        $cookingTime,
        $servings,
        $ingredients
    );
    
    if ($result && $result['status']) {
        redirect('recipe', ['id' => $recipeId, 'success' => $result['message']]);
    } else {
        $errors = $result ? $result['errors'] : [];
        $recipe = $recipeModel->getById($recipeId);
    }
}

// Handle recipe deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_recipe'])) {
    // Check if user is logged in
    if (!isLoggedIn()) {
        redirect('login');
    }
    
    // Get recipe ID
    $recipeId = (int)$_POST['recipe_id'];
    $userId = getCurrentUserId();
    
    // Delete recipe
    $result = $recipeModel->delete($recipeId, $userId);
    
    if ($result) {
        redirect('my-recipes', ['success' => 'Recipe deleted successfully']);
    } else {
        $errors[] = 'Failed to delete recipe';
    }
}

// Get popular recipes for home page
if ($page === 'home') {
    $popularRecipes = $recipeModel->getPopular();
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
