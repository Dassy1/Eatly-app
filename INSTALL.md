# EATLY Installation Guide

This guide will help you set up and run the EATLY recipe application on your local machine.

## Prerequisites

Before you begin, make sure you have the following installed:
- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.3 or higher
- Web server (Apache, Nginx, or PHP's built-in server)

## Installation Steps

### 1. Database Setup

1. Create a new database named `eatly_db`:
   ```sql
   CREATE DATABASE eatly_db;
   ```

2. Import the database schema:
   ```
   mysql -u username -p eatly_db < database/eatly_db.sql
   ```
   
   Alternatively, you can use phpMyAdmin to import the SQL file.

### 2. Configuration

1. Open `config/database.php` and update the database connection settings if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');  // Change to your MySQL username
   define('DB_PASS', '');      // Change to your MySQL password
   define('DB_NAME', 'eatly_db');
   ```

### 3. Web Server Setup

#### Using Apache (XAMPP/WAMP)

1. Place the EATLY folder in your `htdocs` (XAMPP) or `www` (WAMP) directory
2. Start Apache and MySQL services
3. Access the application at `http://localhost/eatly`

#### Using PHP's Built-in Server

1. Navigate to the EATLY directory in your terminal
2. Run the following command:
   ```
   php -S localhost:8000
   ```
3. Access the application at `http://localhost:8000`

### 4. Testing the Installation

1. Visit the application URL in your browser
2. You should see the EATLY homepage with a search bar and recipe cards
3. Try logging in with one of the default test accounts:
   - Username: `john_doe`, Password: `password123`
   - Username: `jane_smith`, Password: `password123`

## Troubleshooting

### Database Connection Issues
- Verify your database credentials in `config/database.php`
- Make sure MySQL service is running
- Check if the `eatly_db` database exists

### Permission Issues
- Ensure the web server has read/write permissions for the application directory
- On Linux/macOS systems, you may need to set appropriate permissions:
  ```
  chmod -R 755 /path/to/eatly
  ```

### Missing PHP Extensions
- The application requires the following PHP extensions:
  - PDO and PDO_MySQL
  - GD (for image processing)
  - JSON
  - Session

## Additional Configuration

### Enabling Error Reporting (Development Only)
For development purposes, you can enable error reporting by modifying the `.htaccess` file:
```
php_flag display_errors on
php_value error_reporting E_ALL
```

### Customizing File Upload Limits
If you need to allow larger file uploads, modify the following in `.htaccess`:
```
php_value upload_max_filesize 20M
php_value post_max_size 20M
```

## Support

If you encounter any issues during installation, please create an issue in the GitHub repository or contact the development team.
