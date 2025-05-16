<?php
/**
 * Bookmark Model
 */
class Bookmark {
    private $conn;
    
    // Constructor
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Add a bookmark
     * 
     * @param int $userId User ID
     * @param int $recipeId Recipe ID
     * @return array Result with status and message
     */
    public function add($userId, $recipeId) {
        // Check if recipe exists
        $sql = "SELECT id FROM recipes WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $recipeId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'status' => false,
                'errors' => ["Recipe not found"]
            ];
        }
        
        // Check if already bookmarked
        $sql = "SELECT id FROM bookmarks WHERE user_id = ? AND recipe_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $recipeId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'status' => true,
                'message' => "Recipe already bookmarked"
            ];
        }
        
        // Add bookmark
        $sql = "INSERT INTO bookmarks (user_id, recipe_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $recipeId);
        
        if ($stmt->execute()) {
            return [
                'status' => true,
                'message' => "Recipe bookmarked successfully"
            ];
        } else {
            return [
                'status' => false,
                'errors' => ["Failed to bookmark recipe: " . $stmt->error]
            ];
        }
    }
    
    /**
     * Remove a bookmark
     * 
     * @param int $userId User ID
     * @param int $recipeId Recipe ID
     * @return array Result with status and message
     */
    public function remove($userId, $recipeId) {
        // Check if bookmark exists
        $sql = "SELECT id FROM bookmarks WHERE user_id = ? AND recipe_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $recipeId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'status' => false,
                'errors' => ["Bookmark not found"]
            ];
        }
        
        // Remove bookmark
        $sql = "DELETE FROM bookmarks WHERE user_id = ? AND recipe_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $recipeId);
        
        if ($stmt->execute()) {
            return [
                'status' => true,
                'message' => "Bookmark removed successfully"
            ];
        } else {
            return [
                'status' => false,
                'errors' => ["Failed to remove bookmark: " . $stmt->error]
            ];
        }
    }
    
    /**
     * Get user bookmarks
     * 
     * @param int $userId User ID
     * @return array Bookmarked recipes
     */
    public function getUserBookmarks($userId) {
        $sql = "SELECT r.* FROM recipes r 
                JOIN bookmarks b ON r.id = b.recipe_id 
                WHERE b.user_id = ? 
                ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $recipes = [];
        while ($row = $result->fetch_assoc()) {
            $recipes[] = formatRecipe($row);
        }
        
        return $recipes;
    }
}
?>
