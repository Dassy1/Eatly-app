<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h3 class="mb-0">Profile</h3>
            </div>
            <div class="card-body">
                <form action="index.php?page=profile" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <div class="form-text">Leave blank to keep current password.</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">My Recipes</h3>
                <a href="index.php?page=add-recipe" class="btn btn-sm btn-success">
                    <i class="fas fa-plus me-1"></i> Add Recipe
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($userRecipes)): ?>
                    <div class="alert alert-info">
                        <p class="mb-0">You haven't created any recipes yet. <a href="index.php?page=add-recipe" class="alert-link">Add your first recipe</a>!</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userRecipes as $recipe): ?>
                                    <tr>
                                        <td>
                                            <a href="index.php?page=recipe&id=<?php echo $recipe['id']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($recipe['title']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($recipe['created_at'])); ?></td>
                                        <td>
                                            <a href="index.php?page=add-recipe&id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-outline-secondary me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="index.php?page=recipe&action=delete&id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this recipe?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
