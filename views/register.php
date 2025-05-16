<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h3 class="mb-0">Register</h3>
            </div>
            <div class="card-body">
                <form action="index.php?page=register" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">Password must be at least 6 characters long.</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="register" class="btn btn-success">Register</button>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-white text-center">
                <p class="mb-0">Already have an account? <a href="index.php?page=login" class="text-success">Login</a></p>
            </div>
        </div>
    </div>
</div>
