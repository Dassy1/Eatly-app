<?php
// Get the root directory
$root = dirname(dirname(__FILE__));

// Include Recipe model
require_once $root . '/models/Recipe.php';

// Create Recipe model instance
$recipeModel = new Recipe($conn);

// Check if we're editing an existing recipe
$isEditing = isset($_GET['id']);
$recipeId = $isEditing ? (int)$_GET['id'] : null;

// If editing, get the recipe data
if ($isEditing) {
    try {
        $recipe = $recipeModel->getById($recipeId);
        
        // Check if recipe exists and belongs to the current user
        if (!$recipe || $recipe['user_id'] !== getCurrentUserId()) {
            redirect('profile', ['error' => 'Recipe not found or you do not have permission to edit it']);
        }
    } catch (Exception $e) {
        error_log("Error getting recipe: " . $e->getMessage());
        redirect('profile', ['error' => 'Failed to load recipe']);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
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

        // Create or update recipe
        if ($isEditing) {
            $result = $recipeModel->update($recipeId, $title, $imageUrl, $sourceUrl, $publisher, $cookingTime, $servings, $ingredients);
        } else {
            $result = $recipeModel->create($title, $imageUrl, $sourceUrl, $publisher, $cookingTime, $servings, $ingredients);
        }

        if ($result) {
            redirect('profile', ['success' => 'Recipe saved successfully']);
        } else {
            throw new Exception('Failed to save recipe');
        }
    } catch (Exception $e) {
        error_log("Error saving recipe: " . $e->getMessage());
        $error = "Failed to save recipe: " . $e->getMessage();
    }
}
?>

<div class="mb-4">
    <h1><?php echo $isEditing ? 'Edit Recipe' : 'Add New Recipe'; ?></h1>
    <p class="text-muted"><?php echo $isEditing ? 'Update your recipe details' : 'Share your recipe with the EATLY community'; ?></p>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="index.php?page=<?php echo $isEditing ? 'recipe&id=' . $recipeId : 'add-recipe'; ?>" method="POST" id="recipeForm">
            <?php if ($isEditing): ?>
                <input type="hidden" name="recipe_id" value="<?php echo $recipeId; ?>">
                <input type="hidden" name="update_recipe" value="1">
            <?php else: ?>
                <input type="hidden" name="create_recipe" value="1">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label">Recipe Title *</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $isEditing ? htmlspecialchars($recipe['title']) : ''; ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="publisher" class="form-label">Publisher/Author</label>
                    <input type="text" class="form-control" id="publisher" name="publisher" value="<?php echo $isEditing && isset($recipe['publisher']) ? htmlspecialchars($recipe['publisher']) : ''; ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cooking_time" class="form-label">Cooking Time (minutes) *</label>
                    <input type="number" class="form-control" id="cooking_time" name="cooking_time" min="1" value="<?php echo $isEditing ? $recipe['cooking_time'] : ''; ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="servings" class="form-label">Servings *</label>
                    <input type="number" class="form-control" id="servings" name="servings" min="1" value="<?php echo $isEditing ? $recipe['servings'] : ''; ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="image_url" class="form-label">Image URL</label>
                    <input type="url" class="form-control" id="image_url" name="image_url" value="<?php echo $isEditing && isset($recipe['image_url']) ? htmlspecialchars($recipe['image_url']) : ''; ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="source_url" class="form-label">Source URL</label>
                    <input type="url" class="form-control" id="source_url" name="source_url" value="<?php echo $isEditing && isset($recipe['source_url']) ? htmlspecialchars($recipe['source_url']) : ''; ?>">
                </div>
            </div>
            
            <h4 class="mt-4 mb-3">Ingredients *</h4>
            <div id="ingredients-container">
                <?php if ($isEditing && !empty($recipe['ingredients'])): ?>
                    <?php foreach ($recipe['ingredients'] as $index => $ingredient): ?>
                        <div class="row ingredient-row mb-2">
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="ingredient_quantity[]" placeholder="Qty" step="0.01" value="<?php echo isset($ingredient['quantity']) ? $ingredient['quantity'] : ''; ?>">
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="ingredient_unit[]" placeholder="Unit" value="<?php echo isset($ingredient['unit']) ? htmlspecialchars($ingredient['unit']) : ''; ?>">
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="ingredient_description[]" placeholder="Description" required value="<?php echo htmlspecialchars($ingredient['description']); ?>">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger remove-ingredient">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="row ingredient-row mb-2">
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="ingredient_quantity[]" placeholder="Qty" step="0.01">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="ingredient_unit[]" placeholder="Unit">
                        </div>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ingredient_description[]" placeholder="Description" required>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger remove-ingredient">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <button type="button" id="add-ingredient" class="btn btn-outline-success">
                    <i class="fas fa-plus me-1"></i> Add Ingredient
                </button>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save me-1"></i> <?php echo $isEditing ? 'Update Recipe' : 'Save Recipe'; ?>
                </button>
                <a href="<?php echo $isEditing ? 'index.php?page=recipe&id=' . $recipeId : 'index.php?page=profile'; ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add ingredient button
    document.getElementById('add-ingredient').addEventListener('click', function() {
        const container = document.getElementById('ingredients-container');
        const newRow = document.createElement('div');
        newRow.className = 'row ingredient-row mb-2';
        newRow.innerHTML = `
            <div class="col-md-2">
                <input type="number" class="form-control" name="ingredient_quantity[]" placeholder="Qty" step="0.01">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="ingredient_unit[]" placeholder="Unit">
            </div>
            <div class="col-md-7">
                <input type="text" class="form-control" name="ingredient_description[]" placeholder="Description" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger remove-ingredient">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
        
        // Add event listener to the new remove button
        newRow.querySelector('.remove-ingredient').addEventListener('click', removeIngredient);
    });
    
    // Remove ingredient button
    document.querySelectorAll('.remove-ingredient').forEach(button => {
        button.addEventListener('click', removeIngredient);
    });
    
    function removeIngredient() {
        const row = this.closest('.ingredient-row');
        const container = document.getElementById('ingredients-container');
        
        // Only remove if there's more than one ingredient row
        if (container.querySelectorAll('.ingredient-row').length > 1) {
            row.remove();
        } else {
            // Clear the inputs instead of removing the last row
            row.querySelectorAll('input').forEach(input => {
                input.value = '';
            });
        }
    }
    
    // Form validation
    document.getElementById('recipeForm').addEventListener('submit', function(e) {
        const ingredientDescriptions = document.querySelectorAll('input[name="ingredient_description[]"]');
        let hasIngredient = false;
        
        ingredientDescriptions.forEach(input => {
            if (input.value.trim() !== '') {
                hasIngredient = true;
            }
        });
        
        if (!hasIngredient) {
            e.preventDefault();
            alert('Please add at least one ingredient');
        }
    });
});
</script>
