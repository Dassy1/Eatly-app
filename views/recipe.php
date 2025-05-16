<?php
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">
        <p class="mb-0">' . htmlspecialchars($_GET['error']) . '</p>
    </div>';
}

if (!$recipe): ?>
    <div class="alert alert-danger">
        <p class="mb-0">Recipe not found.</p>
    </div>
<?php else: ?>
    <div class="row">
        <!-- Recipe Image -->
        <div class="col-md-6 mb-4">
            <?php if (!empty($recipe['image_url'])): ?>
                <img src="<?php echo htmlspecialchars($recipe['image_url']); ?>" class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
            <?php else: ?>
                <div class="bg-light text-center py-5 rounded shadow">
                    <i class="fas fa-utensils fa-5x text-muted"></i>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Recipe Details -->
        <div class="col-md-6 mb-4">
            <h1 class="mb-3"><?php echo htmlspecialchars($recipe['title']); ?></h1>
            
            <div class="d-flex flex-wrap mb-3">
                <?php if (!empty($recipe['cooking_time'])): ?>
                    <span class="badge bg-light text-dark me-2 mb-2">
                        <i class="far fa-clock me-1"></i> <?php echo $recipe['formatted_time']; ?>
                    </span>
                <?php endif; ?>
                
                <?php if (!empty($recipe['servings'])): ?>
                    <span class="badge bg-light text-dark me-2 mb-2">
                        <i class="fas fa-users me-1"></i> <?php echo $recipe['servings']; ?> servings
                    </span>
                <?php endif; ?>
                
                <?php if (!empty($recipe['publisher'])): ?>
                    <span class="badge bg-light text-dark mb-2">
                        <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($recipe['publisher']); ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <?php if (isLoggedIn()): ?>
                    <?php if ($recipe['bookmarked']): ?>
                        <a href="index.php?page=bookmark&action=remove&recipe_id=<?php echo $recipe['id']; ?>" class="btn btn-outline-danger me-2">
                            <i class="fas fa-bookmark me-1"></i> Remove Bookmark
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=bookmark&action=add&recipe_id=<?php echo $recipe['id']; ?>" class="btn btn-outline-success me-2">
                            <i class="far fa-bookmark me-1"></i> Bookmark
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if (!empty($recipe['source_url'])): ?>
                    <a href="<?php echo htmlspecialchars($recipe['source_url']); ?>" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i> Source
                    </a>
                <?php endif; ?>
                
                <?php if (isLoggedIn() && isset($recipe['user_id']) && $recipe['user_id'] === getCurrentUserId()): ?>
                    <div class="mt-3">
                        <a href="index.php?page=add-recipe&id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="index.php?page=recipe&action=delete&id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this recipe?')">
                            <i class="fas fa-trash-alt me-1"></i> Delete
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Ingredients -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Ingredients</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($recipe['ingredients'])): ?>
                        <p class="text-muted">No ingredients listed.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recipe['ingredients'] as $ingredient): ?>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>
                                        <?php if (!empty($ingredient['quantity'])): ?>
                                            <strong><?php echo $ingredient['quantity']; ?></strong>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($ingredient['unit'])): ?>
                                            <strong><?php echo htmlspecialchars($ingredient['unit']); ?></strong>
                                        <?php endif; ?>
                                        
                                        <?php echo htmlspecialchars($ingredient['description']); ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- How to Cook -->
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">How to Cook</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($recipe['source_url'])): ?>
                        <p>This recipe is from an external source. Please visit the <a href="<?php echo htmlspecialchars($recipe['source_url']); ?>" target="_blank">original recipe</a> for cooking instructions.</p>
                    <?php else: ?>
                        <p class="text-muted">No cooking instructions provided. If this is your recipe, consider editing it to add instructions.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
