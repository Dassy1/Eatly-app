<div class="row">
    <div class="col-md-12 text-center mb-5">
        <h1 class="display-4 fw-bold text-success">Discover Delicious Recipes</h1>
        <p class="lead">Find and save your favorite recipes with EATLY</p>
        <div class="mt-4">
            <form action="index.php" method="GET" class="d-flex justify-content-center">
                <input type="hidden" name="page" value="search">
                <div class="input-group" style="max-width: 500px;">
                    <input type="text" name="q" class="form-control form-control-lg" placeholder="Search for recipes..." required>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Get the root directory
$root = dirname(dirname(__FILE__));

// Include Recipe model
require_once $root . '/models/Recipe.php';

// Create Recipe model instance
$recipeModel = new Recipe($conn);

// Get popular recipes
$popularRecipes = $recipeModel->getPopularRecipes();
?>

<!-- Popular Recipes Section -->
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Popular Recipes</h2>
        <a href="index.php?page=search" class="btn btn-outline-success">View All</a>
    </div>
    
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if (isset($popularRecipes) && !empty($popularRecipes)): ?>
            <?php foreach ($popularRecipes as $recipe): ?>
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
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No recipes found. Be the first to <a href="index.php?page=add-recipe" class="alert-link">add a recipe</a>!
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Home Page Content -->
<div class="container mt-5">
    <!-- Search Form -->
    <div class="row mb-5">
        <div class="col-12">
            <form action="index.php?page=search" method="GET" class="row g-3 align-items-center">
                <div class="col-auto">
                    <input type="text" name="q" class="form-control" placeholder="Search recipes..." required>
                    <i class="fas fa-search fa-3x text-success mb-3"></i>
                    <h4>Find Recipes</h4>
                    <p class="text-muted">Search through our extensive collection of recipes from various cuisines.</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-3">
                    <i class="fas fa-bookmark fa-3x text-success mb-3"></i>
                    <h4>Save Favorites</h4>
                    <p class="text-muted">Bookmark your favorite recipes to easily find them later.</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-3">
                    <i class="fas fa-upload fa-3x text-success mb-3"></i>
                    <h4>Share Your Recipes</h4>
                    <p class="text-muted">Create and share your own recipes with the EATLY community.</p>
                </div>
            </div>
        </div>
    </div>
</section>
