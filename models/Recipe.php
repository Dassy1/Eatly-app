<?php
/**
 * Recipe Model
 */
class Recipe {
    private $conn;
    
    // Constructor
    public function __construct($conn) {
        if (!$conn) {
            throw new Exception("Database connection not established");
        }
        $this->conn = $conn;
    }
    
    /**
     * Get popular recipes
     * 
     * @param int $limit Maximum number of recipes to return
     * @return array Popular recipes
     */
    public function getPopularRecipes($limit = 6) {
        // Get recipes with most bookmarks
        $sql = "SELECT r.*, COUNT(b.id) as bookmark_count 
                FROM recipes r 
                LEFT JOIN bookmarks b ON r.id = b.recipe_id 
                GROUP BY r.id 
                ORDER BY bookmark_count DESC, r.created_at DESC 
                LIMIT ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $recipes = [];
            while ($row = $result->fetch_assoc()) {
                $recipes[] = $this->formatRecipe($row);
            }
            
            return $recipes;
        } catch (Exception $e) {
            error_log("Error fetching popular recipes: " . $e->getMessage());
            return [];
        }
    }

    function getRecipeIngredients($recipeId) {
        // get ingredients for a specific recipe
        $sql = "SELECT * FROM ingredients WHERE recipe_id = ?";
        $stmt = $this->conn->prepare($sql);
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
     * Search for recipes
     * 
     * @param string $query Search query
     * @param int $limit Limit results
     * @param int $offset Offset for pagination
     * @return array Recipes matching the search query
     */
    public function search($query, $limit = 10, $offset = 0) {
        try {
            $searchTerm = "%$query%";
            
            // Search in title, publisher, ingredients description, and source_url
            $sql = "SELECT DISTINCT r.* 
                    FROM recipes r 
                    LEFT JOIN ingredients i ON r.id = i.recipe_id 
                    WHERE r.title LIKE ? 
                    OR r.publisher LIKE ? 
                    OR i.description LIKE ? 
                    OR r.source_url LIKE ?
                    ORDER BY r.created_at DESC 
                    LIMIT ? OFFSET ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssssi", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $recipes = [];
            while ($row = $result->fetch_assoc()) {
                $recipes[] = $this->formatRecipe($row);
            }
            
            return $recipes;
        } catch (Exception $e) {
            error_log("Error searching recipes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get recipe by ID
     * 
     * @param int $id Recipe ID
     * @return array|null Recipe data or null if not found
     */
    public function getById($id) {
        try {
            $sql = "SELECT * FROM recipes WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return null;
            }
            
            $recipe = $result->fetch_assoc();
            
            // Get ingredients
          //  $recipe['ingredients'] = $this->getRecipeIngredients($id, $this->conn);
            
            // Check if bookmarked
            $recipe['bookmarked'] = isBookmarked($id, $this->conn);
            
            // Format recipe data
            $recipe = $this->formatRecipe($recipe);
            
            return $recipe;
            
        } catch (Exception $e) {
            error_log("Error getting recipe: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new recipe
     * 
     * @param string $title Recipe title
     * @param string $imageUrl Recipe image URL
     * @param string $sourceUrl Recipe source URL
     * @param string $publisher Recipe publisher
     * @param int $cookingTime Cooking time in minutes
     * @param int $servings Number of servings
     * @param array $ingredients Array of ingredients
     * @return bool Success status
     */
    public function create($title, $imageUrl, $sourceUrl, $publisher, $cookingTime, $servings, $ingredients) {
        try {
            // Start transaction
            $this->conn->begin_transaction();
            
            // Insert recipe
            $sql = "INSERT INTO recipes (title, image_url, source_url, publisher, cooking_time, servings, user_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssiii", $title, $imageUrl, $sourceUrl, $publisher, $cookingTime, $servings, getCurrentUserId());
            $stmt->execute();
            
            // Get the recipe ID
            $recipeId = $this->conn->insert_id;
            
            // Insert ingredients
            if (!empty($ingredients)) {
                $sql = "INSERT INTO ingredients (recipe_id, quantity, unit, description) VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                
                foreach ($ingredients as $ingredient) {
                    $stmt->bind_param("idss", $recipeId, $ingredient['quantity'], $ingredient['unit'], $ingredient['description']);
                    $stmt->execute();
                }
            }
            
            // Commit transaction
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            error_log("Error creating recipe: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update a recipe
     * 
     * @param int $recipeId Recipe ID
     * @param string $title Recipe title
     * @param string $imageUrl Recipe image URL
     * @param string $sourceUrl Recipe source URL
     * @param string $publisher Recipe publisher
     * @param int $cookingTime Cooking time in minutes
     * @param int $servings Number of servings
     * @param array $ingredients Array of ingredients
     * @return bool Success status
     */
    public function update($recipeId, $title, $imageUrl, $sourceUrl, $publisher, $cookingTime, $servings, $ingredients) {
        try {
            // Start transaction
            $this->conn->begin_transaction();
            
            // Update recipe
            $sql = "UPDATE recipes SET title = ?, image_url = ?, source_url = ?, publisher = ?, 
                    cooking_time = ?, servings = ? WHERE id = ? AND user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssiiii", $title, $imageUrl, $sourceUrl, $publisher, $cookingTime, $servings, $recipeId, getCurrentUserId());
            $stmt->execute();
            
            // Delete existing ingredients
            $sql = "DELETE FROM ingredients WHERE recipe_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $recipeId);
            $stmt->execute();
            
            // Insert new ingredients
            if (!empty($ingredients)) {
                $sql = "INSERT INTO ingredients (recipe_id, quantity, unit, description) VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                
                foreach ($ingredients as $ingredient) {
                    $stmt->bind_param("idss", $recipeId, $ingredient['quantity'], $ingredient['unit'], $ingredient['description']);
                    $stmt->execute();
                }
            }
            
            // Commit transaction
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            error_log("Error updating recipe: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a recipe
     * 
     * @param int $id Recipe ID
     * @param int $userId User ID
     * @return array Result with status and message
     */
    public function delete($id, $userId) {
        try {
            // Start transaction
            $this->conn->begin_transaction();
            
            // Delete recipe
            $sql = "DELETE FROM recipes WHERE id = ? AND user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $id, $userId);
            $stmt->execute();
            
            // Delete ingredients
            $sql = "DELETE FROM ingredients WHERE recipe_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            // Delete bookmarks
            $sql = "DELETE FROM bookmarks WHERE recipe_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            // Commit transaction
            $this->conn->commit();
            
            return [
                'status' => true,
                'message' => 'Recipe deleted successfully'
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            error_log("Error deleting recipe: " . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Failed to delete recipe'
            ];
        }
    }
    
    /**
     * Get user recipes
     * 
     * @param int $userId User ID
     * @return array User recipes
     */
    public function getUserRecipes($userId) {
        $sql = "SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $recipes = [];
        while ($row = $result->fetch_assoc()) {
            $recipes[] = $this->formatRecipe($row);
        }
        
        return $recipes;
    }
    
    /**
     * Get popular recipes
     * 
     * @param int $limit Limit results
     * @return array Popular recipes
     */
    public function getPopular($limit = 6) {
        // Get recipes with most bookmarks
        $sql = "SELECT r.*, COUNT(b.id) as bookmark_count 
                FROM recipes r 
                LEFT JOIN bookmarks b ON r.id = b.recipe_id 
                GROUP BY r.id 
                ORDER BY bookmark_count DESC, r.created_at DESC 
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $recipes = [];
        while ($row = $result->fetch_assoc()) {
            $recipes[] = $this->formatRecipe($row);
        }
        
        return $recipes;
    }
    
    /**
     * Get recent recipes
     * 
     * @param int $limit Limit results
     * @return array Recent recipes
     */
    public function getRecent($limit = 5) {
        // Get most recently added recipes
        $sql = "SELECT * FROM recipes ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $recipes = [];
        while ($row = $result->fetch_assoc()) {
            $recipes[] = $this->formatRecipe($row);
        }
        
        return $recipes;
    }
    
    /**
     * Get all recipes with pagination
     * 
     * @param int $limit Limit results
     * @param int $offset Offset for pagination
     * @return array Recipes
     */
    public function getAllRecipes($limit = 10, $offset = 0) {
        $sql = "SELECT * FROM recipes ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $recipes = [];
        while ($row = $result->fetch_assoc()) {
            $recipes[] = $this->formatRecipe($row);
        }
        
        return $recipes;
    }
    
    /**
     * Get total recipe count
     * 
     * @return int Total number of recipes
     */
    public function getRecipeCount() {
        $sql = "SELECT COUNT(*) as count FROM recipes";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        
        return (int)$row['count'];
    }
    
    /**
     * Format recipe data
     * 
     * @param array $recipe Raw recipe data
     * @return array Formatted recipe data
     */
    private function formatRecipe($recipe) {
        // Format cooking time
        if (!empty($recipe['cooking_time'])) {
            $hours = floor($recipe['cooking_time'] / 60);
            $minutes = $recipe['cooking_time'] % 60;
            $recipe['formatted_time'] = '';
            if ($hours > 0) {
                $recipe['formatted_time'] .= $hours . 'h';
            }
            if ($minutes > 0) {
                if ($hours > 0) {
        }
    }

    // Format created_at timestamp
    if (!empty($recipe['created_at'])) {
        $recipe['created_at_formatted'] = date('F j, Y', strtotime($recipe['created_at']));
    }

    // Get ingredients
    $recipe['ingredients'] = $this->getRecipeIngredients($recipe['id'], $this->conn);

    // Check if bookmarked
    $recipe['bookmarked'] = isBookmarked($recipe['id'], $this->conn);
    
    // Format created_at timestamp
        if (!empty($recipe['created_at'])) {
            $recipe['created_at_formatted'] = date('F j, Y', strtotime($recipe['created_at']));
        }

    return $recipe;
            }
    }


        
    }

