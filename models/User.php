<?php
/**
 * User Model
 */
class User {
    private $conn;
    
    // Constructor
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Register a new user
     * 
     * @param string $username Username
     * @param string $email Email
     * @param string $password Password
     * @return array Result with status and message
     */
    public function register($username, $email, $password) {
        // Validate input
        $errors = [];
        
        if (empty($username)) {
            $errors[] = "Username is required";
        }
        
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        
        if (empty($password)) {
            $errors[] = "Password is required";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters";
        }
        
        if (!empty($errors)) {
            return [
                'status' => false,
                'errors' => $errors
            ];
        }
        
        // Check if username or email already exists
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'status' => false,
                'errors' => ["Username or email already exists"]
            ];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            return [
                'status' => true,
                'message' => "Registration successful! You can now log in.",
                'user_id' => $stmt->insert_id
            ];
        } else {
            return [
                'status' => false,
                'errors' => ["Registration failed: " . $stmt->error]
            ];
        }
    }
    
    /**
     * Login a user
     * 
     * @param string $username Username or email
     * @param string $password Password
     * @return array Result with status and message
     */
    public function login($username, $password) {
        // Validate input
        $errors = [];
        
        if (empty($username)) {
            $errors[] = "Username or email is required";
        }
        
        if (empty($password)) {
            $errors[] = "Password is required";
        }
        
        if (!empty($errors)) {
            return [
                'status' => false,
                'errors' => $errors
            ];
        }
        
        // Check if user exists
        $sql = "SELECT id, username, email, password FROM users WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'status' => false,
                'errors' => ["Invalid username/email or password"]
            ];
        }
        
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, create session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            return [
                'status' => true,
                'message' => "Login successful!",
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ];
        } else {
            return [
                'status' => false,
                'errors' => ["Invalid username/email or password"]
            ];
        }
    }
    
    /**
     * Get user by ID
     * 
     * @param int $userId User ID
     * @return array|null User data or null if not found
     */
    public function getById($userId) {
        $sql = "SELECT id, username, email, created_at FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    /**
     * Update user profile
     * 
     * @param int $userId User ID
     * @param array $data User data to update
     * @return array Result with status and message
     */
    public function update($userId, $data) {
        // Validate input
        $errors = [];
        
        if (isset($data['username']) && empty($data['username'])) {
            $errors[] = "Username cannot be empty";
        }
        
        if (isset($data['email']) && empty($data['email'])) {
            $errors[] = "Email cannot be empty";
        } elseif (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        
        if (!empty($errors)) {
            return [
                'status' => false,
                'errors' => $errors
            ];
        }
        
        // Check if username or email already exists
        if (isset($data['username']) || isset($data['email'])) {
            $sql = "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?";
            $stmt = $this->conn->prepare($sql);
            $username = isset($data['username']) ? $data['username'] : '';
            $email = isset($data['email']) ? $data['email'] : '';
            $stmt->bind_param("ssi", $username, $email, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return [
                    'status' => false,
                    'errors' => ["Username or email already exists"]
                ];
            }
        }
        
        // Update user
        $updates = [];
        $types = '';
        $params = [];
        
        if (isset($data['username'])) {
            $updates[] = "username = ?";
            $types .= "s";
            $params[] = $data['username'];
        }
        
        if (isset($data['email'])) {
            $updates[] = "email = ?";
            $types .= "s";
            $params[] = $data['email'];
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            $updates[] = "password = ?";
            $types .= "s";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($updates)) {
            return [
                'status' => true,
                'message' => "No changes made"
            ];
        }
        
        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
        $types .= "i";
        $params[] = $userId;
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            // Update session if username or email changed
            if (isset($data['username'])) {
                $_SESSION['username'] = $data['username'];
            }
            
            if (isset($data['email'])) {
                $_SESSION['email'] = $data['email'];
            }
            
            return [
                'status' => true,
                'message' => "Profile updated successfully"
            ];
        } else {
            return [
                'status' => false,
                'errors' => ["Update failed: " . $stmt->error]
            ];
        }
    }
}
?>
