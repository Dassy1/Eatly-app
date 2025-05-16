<?php
/**
 * EATLY API - Recipes Endpoint
 * 
 * This file handles all recipe-related API requests:
 * - GET /api/recipes - Get all recipes
 * - GET /api/recipes/{id} - Get a specific recipe
 * - GET /api/recipes/search?q={query} - Search for recipes
 * - GET /api/recipes/popular - Get popular recipes
 * - POST /api/recipes - Create a new recipe (requires authentication)
 * - PUT /api/recipes/{id} - Update a recipe (requires authentication)
 * - DELETE /api/recipes/{id} - Delete a recipe (requires authentication)
 */

// Include necessary files
require_once '../config/database.php';
require_once '../includes/functions.php';

// Include the Recipe model if not already included
if (!class_exists('Recipe')) {
    require_once '../models/Recipe.php';
}

// Create Recipe model instance
$recipeModel = new Recipe($conn);

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Get recipe ID from URL if present
$recipe_id = isset($path_parts[1]) && is_numeric($path_parts[1]) ? (int)$path_parts[1] : null;

// Get the action from URL if present
$action = isset($path_parts[1]) && !is_numeric($path_parts[1]) ? $path_parts[1] : '';

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        handleGetRequest($recipeModel, $recipe_id, $action);
        break;
    case 'POST':
        handlePostRequest($recipeModel);
        break;
    case 'PUT':
        handlePutRequest($recipeModel, $recipe_id);
        break;
    case 'DELETE':
        handleDeleteRequest($recipeModel, $recipe_id);
        break;
    default:
        send_json_response(['error' => 'Method not allowed'], 405);
        break;
}

/**
 * Handle GET requests for recipes
 *
 * @param Recipe $recipeModel The Recipe model instance
 * @param int|null $recipe_id The recipe ID
 * @param string $action The action to perform
 */
function handleGetRequest($recipeModel, $recipe_id, $action) {
    // If recipe ID is provided, get that specific recipe
    if ($recipe_id) {
        $recipe = $recipeModel->getById($recipe_id);
        
        if (!$recipe) {
            send_json_response(['error' => 'Recipe not found'], 404);
        }
        
        send_json_response($recipe);
        return;
    }
    
    // Handle specific actions
    switch ($action) {
        case 'search':
            // Search for recipes
            if (!isset($_GET['q'])) {
                send_json_response(['error' => 'Search query is required'], 400);
            }
            
            $query = $_GET['q'];
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            
            $recipes = $recipeModel->search($query, $limit, $offset);
            send_json_response($recipes);
            break;
            
        case 'popular':
            // Get popular recipes
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
            $recipes = $recipeModel->getPopular($limit);
            send_json_response($recipes);
            break;
            
        case 'recent':
            // Get recent recipes
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
            $recipes = $recipeModel->getRecent($limit);
            send_json_response($recipes);
            break;
            
        default:
            // Get all recipes with pagination
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            $user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
            
            // Get recipes with pagination
            if ($user_id) {
                // Get total count first
                $allUserRecipes = $recipeModel->getUserRecipes($user_id);
                $total = count($allUserRecipes);
                
                // Get paginated recipes
                $recipes = array_slice($allUserRecipes, $offset, $limit);
            } else {
                // Get all recipes with pagination
                $recipes = $recipeModel->getAllRecipes($limit, $offset);
                $total = $recipeModel->getRecipeCount();
            }
            
            // Prepare response with pagination metadata
            $response = [
                'data' => $recipes,
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ],
                'links' => [
                    'first' => '/api/recipes?page=1&limit=' . $limit,
                    'last' => '/api/recipes?page=' . ceil($total / $limit) . '&limit=' . $limit
                ]
            ];
            
            // Add next/prev links if applicable
            if ($page < ceil($total / $limit)) {
                $response['links']['next'] = '/api/recipes?page=' . ($page + 1) . '&limit=' . $limit;
            }
            
            if ($page > 1) {
                $response['links']['prev'] = '/api/recipes?page=' . ($page - 1) . '&limit=' . $limit;
            }
            
            send_json_response($response);
            break;
    }
}

/**
 * Handle POST requests to create a new recipe
 *
 * @param Recipe $recipeModel The Recipe model instance
 */
function handlePostRequest($recipeModel) {
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
    
    // Create recipe
    $user_id = getCurrentUserId();
    $result = $recipeModel->create($data, $user_id);
    
    if (!$result['status']) {
        send_json_response(['error' => $result['errors']], 400);
    }
    
    // Get the created recipe
    $recipe = $recipeModel->getById($result['recipe_id']);
    
    send_json_response($recipe, 201);
}

/**
 * Handle PUT requests to update a recipe
 *
 * @param Recipe $recipeModel The Recipe model instance
 * @param int|null $recipe_id The recipe ID
 */
function handlePutRequest($recipeModel, $recipe_id) {
    // Check if user is authenticated
    if (!isLoggedIn()) {
        send_json_response(['error' => 'Authentication required'], 401);
    }
    
    // Check if recipe ID is provided
    if (!$recipe_id) {
        send_json_response(['error' => 'Recipe ID is required'], 400);
    }
    
    // Get JSON data from request body
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    if (!$data) {
        send_json_response(['error' => 'Invalid JSON data'], 400);
    }
    
    // Get the recipe to check ownership
    $recipe = $recipeModel->getById($recipe_id);
    
    if (!$recipe) {
        send_json_response(['error' => 'Recipe not found'], 404);
    }
    
    $user_id = getCurrentUserId();
    
    // Check if user owns the recipe
    if ($recipe['user_id'] != $user_id) {
        send_json_response(['error' => 'You do not have permission to update this recipe'], 403);
    }
    
    // Update recipe
    $result = $recipeModel->update($recipe_id, $data, $user_id);
    
    if (!$result['status']) {
        send_json_response(['error' => $result['errors']], 400);
    }
    
    // Get the updated recipe
    $updated_recipe = $recipeModel->getById($recipe_id);
    
    send_json_response($updated_recipe);
}

/**
 * Handle DELETE requests to delete a recipe
 *
 * @param Recipe $recipeModel The Recipe model instance
 * @param int|null $recipe_id The recipe ID
 */
function handleDeleteRequest($recipeModel, $recipe_id) {
    // Check if user is authenticated
    if (!isLoggedIn()) {
        send_json_response(['error' => 'Authentication required'], 401);
    }
    
    // Check if recipe ID is provided
    if (!$recipe_id) {
        send_json_response(['error' => 'Recipe ID is required'], 400);
    }
    
    // Check if recipe exists and belongs to the current user
    $recipe = $recipeModel->getById($recipe_id);
    
    if (!$recipe) {
        send_json_response(['error' => 'Recipe not found'], 404);
    }
    
    $user_id = getCurrentUserId();
    
    // Check if user owns the recipe
    if ($recipe['user_id'] != $user_id) {
        send_json_response(['error' => 'You do not have permission to delete this recipe'], 403);
    }
    
    // Delete recipe
    $result = $recipeModel->delete($recipe_id, $user_id);
    
    if (!$result['status']) {
        send_json_response(['error' => $result['errors']], 400);
    }
    
    send_json_response(['message' => 'Recipe deleted successfully']);
}
