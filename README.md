# EATLY - Recipe Application

EATLY is a recipe search and bookmarking application built with PHP, inspired by Forkify. It allows users to search for recipes, view recipe details, bookmark favorite recipes, and create their own recipes.

## Features
- Recipe search functionality
- Detailed recipe view with ingredients and instructions
- User authentication (register, login, logout)
- Bookmark favorite recipes
- Create and upload custom recipes
- User profile management
- Responsive design for all devices

## Technologies Used
- PHP 7.4+ (No frameworks, pure PHP)
- MySQL/MariaDB
- HTML5/CSS3
- JavaScript (ES6+)
- Bootstrap 5 for styling
- Font Awesome for icons

## Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.3 or higher
- Web server (Apache, Nginx, or PHP's built-in server)

## Installation

### Using XAMPP/WAMP
1. Clone or download this repository to your `htdocs` (XAMPP) or `www` (WAMP) directory:
   ```
   git clone https://github.com/yourusername/eatly.git
   ```

2. Start your Apache and MySQL services from the XAMPP/WAMP control panel

3. Create a new database named `eatly_db` in phpMyAdmin

4. Import the database schema from `database/eatly_db.sql`

5. Configure your database connection in `config/database.php` if needed (default settings should work with XAMPP/WAMP)

6. Access the application through your web browser:
   ```
   http://localhost/eatly
   ```

### Using PHP's Built-in Server
1. Clone or download this repository:
   ```
   git clone https://github.com/yourusername/eatly.git
   ```

2. Navigate to the project directory:
   ```
   cd eatly
   ```

3. Create a new MySQL database named `eatly_db`

4. Import the database schema:
   ```
   mysql -u username -p eatly_db < database/eatly_db.sql
   ```

5. Configure your database connection in `config/database.php`

6. Start the PHP built-in server:
   ```
   php -S localhost:8000
   ```

7. Access the application through your web browser:
   ```
   http://localhost:8000
   ```

## Default User Accounts
The application comes with two pre-configured user accounts for testing:

1. Username: `john_doe`
   Email: `john@example.com`
   Password: `password123`

2. Username: `jane_smith`
   Email: `jane@example.com`
   Password: `password123`

## Project Structure
- `index.php` - Main entry point and router
- `assets/` - Contains CSS, JS, and images
  - `css/` - Stylesheet files
  - `js/` - JavaScript files
  - `images/` - Image files
- `includes/` - PHP components and helper functions
- `config/` - Configuration files
- `database/` - Database schema and migrations
- `models/` - Database models for data handling
- `views/` - Frontend templates
  - `partials/` - Reusable view components
- `controllers/` - Application logic

## Usage
1. Register a new account or log in with an existing one
2. Search for recipes using the search bar
3. View recipe details by clicking on a recipe card
4. Bookmark recipes by clicking the bookmark button
5. Add your own recipes by clicking the "Add Recipe" button
6. Manage your profile and view your recipes in the profile section

## License
This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements
- Inspired by [Forkify](https://forkify-v2.netlify.app/)
- Recipe data structure based on common recipe APIs
- Icons from [Font Awesome](https://fontawesome.com/)
- UI components from [Bootstrap](https://getbootstrap.com/)
