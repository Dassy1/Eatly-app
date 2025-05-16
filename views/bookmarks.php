<div class="mb-4">
    <h1>My Bookmarks</h1>
    <p class="text-muted">Your saved recipes</p>
</div>

<?php if (empty($bookmarks)): ?>
    <div class="alert alert-info">
        <p class="mb-0">You haven't bookmarked any recipes yet. <a href="index.php" class="alert-link">Find recipes</a> to bookmark!</p>
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($bookmarks as $recipe): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($recipe['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($recipe['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light text-center py-5">
                            <i class="fas fa-utensils fa-4x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                        <p class="card-text text-muted">
                            <?php if (!empty($recipe['publisher'])): ?>
                                <small><i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($recipe['publisher']); ?></small>
                            <?php endif; ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <?php if (!empty($recipe['cooking_time'])): ?>
                                    <span class="badge bg-light text-dark me-2">
                                        <i class="far fa-clock me-1"></i> <?php echo $recipe['formatted_time']; ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($recipe['servings'])): ?>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-users me-1"></i> <?php echo $recipe['servings']; ?> servings
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <a href="index.php?page=recipe&id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-outline-success">View Recipe</a>
                        <a href="index.php?page=bookmark&action=remove&recipe_id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-bookmark"></i> Remove
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
