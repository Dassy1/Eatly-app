# Contributing to EATLY

Thank you for considering contributing to EATLY! This document outlines the process for contributing to the project.

## Code of Conduct

By participating in this project, you agree to abide by our Code of Conduct. Please be respectful and considerate when interacting with other contributors.

## How Can I Contribute?

### Reporting Bugs

If you find a bug, please create an issue in the repository with the following information:
- A clear and descriptive title
- Steps to reproduce the issue
- Expected behavior
- Actual behavior
- Screenshots (if applicable)
- Your environment (OS, PHP version, MySQL version, browser)

### Suggesting Enhancements

If you have an idea for an enhancement, please create an issue with:
- A clear and descriptive title
- A detailed description of the proposed enhancement
- Any relevant examples or mockups

### Pull Requests

1. Fork the repository
2. Create a new branch for your feature or bugfix
3. Make your changes
4. Test your changes thoroughly
5. Submit a pull request with a clear description of the changes

## Development Guidelines

### Coding Standards

- Follow PSR-12 coding standards for PHP code
- Use meaningful variable and function names
- Add comments for complex logic
- Keep functions small and focused on a single task

### Database Changes

If your contribution includes database changes:
1. Update the database schema in `database/eatly_db.sql`
2. Document the changes in your pull request

### Testing

Before submitting a pull request, please test your changes:
- Ensure all features work as expected
- Check for any regressions
- Test on different browsers if making frontend changes

### Documentation

If you're adding a new feature, please update the relevant documentation:
- README.md for general features
- INSTALL.md for installation-related changes
- Code comments for technical details

## Project Structure

Understanding the project structure will help you contribute effectively:

- `index.php` - Main entry point and router
- `assets/` - Contains CSS, JS, and images
- `includes/` - PHP components and helper functions
- `config/` - Configuration files
- `database/` - Database schema and migrations
- `models/` - Database models for data handling
- `views/` - Frontend templates
- `controllers/` - Application logic

## Getting Help

If you need help with your contribution, feel free to:
- Ask questions in the issue you're working on
- Reach out to the maintainers

Thank you for contributing to EATLY!
