# EATLY API Documentation

![EATLY Logo](../assets/images/logo.png)

This document provides information about the EATLY API endpoints, request formats, and response structures.

## Base URL

```
http://localhost:8000/api
```

## Authentication

Most API endpoints require authentication. To authenticate, you need to:

1. Login using the `/api/users/login` endpoint to get a token
2. Include the token in subsequent requests using one of these methods:
   - As a cookie (automatically handled if using the API from the browser)
   - As a query parameter: `?token=your_token_here`
   - In the Authorization header: `Authorization: Bearer your_token_here`

## API Endpoints

### Payments

#### Get User Payments

```
GET /api/payments
```

**Authentication:** Required

**Response:**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "payment_method": "credit_card",
    "amount": 19.99,
    "transaction_id": "txn_123456789",
    "status": "completed",
    "created_at": "2025-05-15 10:00:00"
  },
  // More payments...
]
```

#### Get User Payment Methods

```
GET /api/payments/methods
```

**Authentication:** Required

**Response:**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "type": "credit_card",
    "card_number": "xxxx-xxxx-xxxx-1234",
    "expiry_month": "12",
    "expiry_year": "2027",
    "name_on_card": "John Doe",
    "is_default": true,
    "created_at": "2025-05-15 10:00:00"
  },
  // More payment methods...
]
```

#### Process a Payment

```
POST /api/payments/process
```

**Authentication:** Required

**Request Body:**
```json
{
  "payment_method": "credit_card",
  "amount": 19.99,
  "payment_details": {
    "card_number": "4111111111111111",
    "expiry_month": "12",
    "expiry_year": "2027",
    "cvv": "123",
    "name_on_card": "John Doe"
  }
}
```

**Response:**
```json
{
  "message": "Payment processed successfully",
  "payment_id": 1,
  "transaction_id": "txn_123456789"
}
```

#### Add a Payment Method

```
POST /api/payments/methods
```

**Authentication:** Required

**Request Body (Credit Card):**
```json
{
  "type": "credit_card",
  "details": {
    "card_number": "4111111111111111",
    "expiry_month": "12",
    "expiry_year": "2027",
    "cvv": "123",
    "name_on_card": "John Doe"
  }
}
```

**Request Body (PayPal):**
```json
{
  "type": "paypal",
  "details": {
    "email": "john.doe@example.com"
  }
}
```

**Response:**
```json
{
  "message": "Payment method added successfully",
  "method_id": 1
}
```

#### Remove a Payment Method

```
DELETE /api/payments/methods/{method_id}
```

**Authentication:** Required

**Response:**
```json
{
  "message": "Payment method removed successfully"
}
```

### Recipes

#### Get All Recipes

```
GET /api/recipes
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `limit` (optional): Number of recipes per page (default: 10)
- `user_id` (optional): Filter recipes by user ID

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Pasta Carbonara",
      "image_url": "https://example.com/pasta.jpg",
      "publisher": "Italian Chef",
      "cooking_time": 30,
      "servings": 4,
      "user_id": 1,
      "created_at": "2025-05-15 10:00:00"
    },
    // More recipes...
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 50,
    "total_pages": 5
  },
  "links": {
    "first": "/api/recipes?page=1&limit=10",
    "last": "/api/recipes?page=5&limit=10",
    "next": "/api/recipes?page=2&limit=10"
  }
}
```

#### Get a Specific Recipe

```
GET /api/recipes/{id}
```

**Response:**
```json
{
  "id": 1,
  "title": "Pasta Carbonara",
  "image_url": "https://example.com/pasta.jpg",
  "publisher": "Italian Chef",
  "cooking_time": 30,
  "servings": 4,
  "source_url": "https://example.com/recipe/pasta-carbonara",
  "user_id": 1,
  "created_at": "2025-05-15 10:00:00",
  "ingredients": [
    {
      "id": 1,
      "recipe_id": 1,
      "quantity": 200,
      "unit": "g",
      "description": "spaghetti"
    },
    // More ingredients...
  ]
}
```

#### Search Recipes

```
GET /api/recipes/search?q={query}
```

**Query Parameters:**
- `q` (required): Search query
- `page` (optional): Page number (default: 1)
- `limit` (optional): Number of recipes per page (default: 10)

**Response:** Same as Get All Recipes

#### Get Popular Recipes

```
GET /api/recipes/popular
```

**Query Parameters:**
- `limit` (optional): Number of recipes to return (default: 5)

**Response:** Array of recipes

#### Get Recent Recipes

```
GET /api/recipes/recent
```

**Query Parameters:**
- `limit` (optional): Number of recipes to return (default: 5)

**Response:** Array of recipes

#### Create a Recipe

```
POST /api/recipes
```

**Authentication:** Required

**Request Body:**
```json
{
  "title": "Pasta Carbonara",
  "image_url": "https://example.com/pasta.jpg",
  "publisher": "Italian Chef",
  "cooking_time": 30,
  "servings": 4,
  "source_url": "https://example.com/recipe/pasta-carbonara",
  "ingredients": [
    {
      "quantity": 200,
      "unit": "g",
      "description": "spaghetti"
    },
    {
      "quantity": 100,
      "unit": "g",
      "description": "pancetta"
    }
    // More ingredients...
  ]
}
```

**Response:** The created recipe

#### Update a Recipe

```
PUT /api/recipes/{id}
```

**Authentication:** Required (must be the owner of the recipe)

**Request Body:** Same as Create a Recipe

**Response:** The updated recipe

#### Delete a Recipe

```
DELETE /api/recipes/{id}
```

**Authentication:** Required (must be the owner of the recipe)

**Response:**
```json
{
  "message": "Recipe deleted successfully"
}
```

### Users

#### Login

```
POST /api/users/login
```

**Request Body:**
```json
{
  "username": "john_doe",
  "password": "your_password"
}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "username": "john_doe",
    "email": "john@example.com",
    "created_at": "2025-05-15 10:00:00"
  },
  "token": "your_api_token",
  "expires_at": "2025-06-14 10:00:00"
}
```

#### Register

```
POST /api/users/register
```

**Request Body:**
```json
{
  "username": "new_user",
  "email": "user@example.com",
  "password": "your_password",
  "confirm_password": "your_password"
}
```

**Response:** The created user (without password)

#### Get Current User Profile

```
GET /api/users/profile
```

**Authentication:** Required

**Response:**
```json
{
  "id": 1,
  "username": "john_doe",
  "email": "john@example.com",
  "created_at": "2025-05-15 10:00:00"
}
```

#### Update User Profile

```
PUT /api/users/profile
```

**Authentication:** Required

**Request Body:**
```json
{
  "username": "john_doe_updated",
  "email": "john_updated@example.com"
}
```

**Response:** The updated user (without password)

#### Change Password

```
PUT /api/users/password
```

**Authentication:** Required

**Request Body:**
```json
{
  "current_password": "your_current_password",
  "new_password": "your_new_password",
  "confirm_password": "your_new_password"
}
```

**Response:**
```json
{
  "message": "Password changed successfully"
}
```

#### Logout

```
POST /api/users/logout
```

**Authentication:** Required

**Response:**
```json
{
  "message": "Logged out successfully"
}
```

### Bookmarks

#### Get User Bookmarks

```
GET /api/bookmarks
```

**Authentication:** Required

**Response:**
```json
[
  {
    "id": 1,
    "title": "Pasta Carbonara",
    "image_url": "https://example.com/pasta.jpg",
    "publisher": "Italian Chef",
    "cooking_time": 30,
    "servings": 4,
    "user_id": 2,
    "created_at": "2025-05-15 10:00:00"
  },
  // More bookmarked recipes...
]
```

#### Add Bookmark

```
POST /api/bookmarks
```

**Authentication:** Required

**Request Body:**
```json
{
  "recipe_id": 1
}
```

**Response:**
```json
{
  "message": "Recipe bookmarked successfully"
}
```

#### Remove Bookmark

```
DELETE /api/bookmarks/{recipe_id}
```

**Authentication:** Required

**Response:**
```json
{
  "message": "Bookmark removed successfully"
}
```

## Error Responses

All API endpoints return appropriate HTTP status codes and error messages in case of failure:

```json
{
  "error": "Error message here"
}
```

Common error status codes:
- `400`: Bad Request (invalid input)
- `401`: Unauthorized (authentication required)
- `403`: Forbidden (insufficient permissions)
- `404`: Not Found
- `405`: Method Not Allowed
- `500`: Internal Server Error

## Rate Limiting

The API has rate limiting to prevent abuse. If you exceed the rate limit, you'll receive a `429 Too Many Requests` response.

## CORS

The API supports Cross-Origin Resource Sharing (CORS), allowing it to be used from different domains.
