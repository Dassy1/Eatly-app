<?php
// Start session
session_start();

// Include configuration
require_once 'config/database.php';

// Get database connection
$conn = getDbConnection();

// Delete existing data
$sql = "DELETE FROM bookmarks";
$conn->query($sql);

$sql = "DELETE FROM ingredients";
$conn->query($sql);

$sql = "DELETE FROM recipes";
$conn->query($sql);

$sql = "DELETE FROM users";
$conn->query($sql);

// Insert sample users
$users = [
    ['dassy01', 'dassy@gmail.com', password_hash('password123', PASSWORD_DEFAULT)],
    ['jane_smith', 'jane@outlook.com', password_hash('password123', PASSWORD_DEFAULT)]
];

foreach ($users as $user) {
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $user[0], $user[1], $user[2]);
    $stmt->execute();
}

// Get user IDs
$sql = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);

$userIds = [];
foreach (["dassy01", "jane_smith"] as $username) {
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $userIds[] = $row['id'];
}

// Insert sample recipes
$recipes = [
    [
        'title' => 'Spaghetti Carbonara',
        'image_url' => 'https://static01.nyt.com/images/2021/02/14/dining/carbonara-horizontal/carbonara-horizontal-master768-v2.jpg?quality=75&auto=webp',
        'source_url' => 'https://cooking.nytimes.com/recipes/12965-spaghetti-carbonara',
        'publisher' => 'Italian Cuisine',
        'cooking_time' => 30,
        'servings' => 4,
        'user_id' => $userIds[0]
    ],
    [
        'title' => 'Chicken Tikka Masala',
        'image_url' => 'https://www.jocooks.com/wp-content/uploads/2024/01/chicken-tikka-masala-1-26-730x913.jpg',
        'source_url' => 'https://www.jocooks.com/recipes/chicken-tikka-masala/',
        'publisher' => 'Indian Delights',
        'cooking_time' => 45,
        'servings' => 6,
        'user_id' => $userIds[0]
    ],
    [
        'title' => 'Vegetable Stir Fry',
        'image_url' => 'https://i0.wp.com/kristineskitchenblog.com/wp-content/uploads/2024/01/vegetable-stir-fry-22-2.jpg?resize=700%2C1050&ssl=1',
        'source_url' => 'https://kristineskitchenblog.com/vegetable-stir-fry/',
        'publisher' => 'Healthy Eats',
        'cooking_time' => 20,
        'servings' => 2,
        'user_id' => $userIds[1]
    ]
];

foreach ($recipes as $recipe) {
    $sql = "INSERT INTO recipes (title, image_url, source_url, publisher, cooking_time, servings, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiii", 
        $recipe['title'],
        $recipe['image_url'],
        $recipe['source_url'],
        $recipe['publisher'],
        $recipe['cooking_time'],
        $recipe['servings'],
        $recipe['user_id']
    );
    $stmt->execute();
    $recipeId = $conn->insert_id;

    // Insert ingredients for this recipe
    $ingredients = [
        [400, 'g', 'spaghetti'],
        [200, 'g', 'pancetta']
    ];

    foreach ($ingredients as $ingredient) {
        $sql = "INSERT INTO ingredients (recipe_id, quantity, unit, description) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idss", $recipeId, $ingredient[0], $ingredient[1], $ingredient[2]);
        $stmt->execute();
    }
}

// Add bookmarks
$sql = "INSERT INTO bookmarks (user_id, recipe_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

// First bookmark
$userId1 = $userIds[0];
$recipeId1 = 2;
$stmt->bind_param("ii", $userId1, $recipeId1);
$stmt->execute();

// Second bookmark
$userId2 = $userIds[1];
$recipeId2 = 1;
$stmt->bind_param("ii", $userId2, $recipeId2);
$stmt->execute();

// Redirect to home page
header("Location: index.php");
exit;
