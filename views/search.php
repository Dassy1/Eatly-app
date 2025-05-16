<div class="mb-4">
    <h1>Search Results: "<?php echo htmlspecialchars($query); ?>"</h1>
    <p class="text-muted">Found <?php echo $totalResults; ?> recipe(s)</p>
</div>
   
<?php if (empty($recipes)): ?>
    <div class="alert alert-info">
        <p class="mb-0">No recipes found for "<?php echo htmlspecialchars($query); ?>". Try a different search term or <a href="index.php?page=add-recipe" class="alert-link">add a new recipe</a>.</p>
    </div>
<?php else: ?>
     <strong>NOTHING FOUND</strong>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
        <?php foreach ($recipes as $recipe): ?>
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
                    <div class="card-footer bg-white">
                        <a href="index.php?page=recipe&id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-outline-success w-100">View Recipe</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav aria-label="Search results pagination">
            <ul class="pagination justify-content-center">
                <?php if ($prevPage): ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=search&q=<?php echo urlencode($query); ?>&p=<?php echo $prevPage; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">&laquo;</span>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="index.php?page=search&q=<?php echo urlencode($query); ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($nextPage): ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=search&q=<?php echo urlencode($query); ?>&p=<?php echo $nextPage; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">&raquo;</span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>
