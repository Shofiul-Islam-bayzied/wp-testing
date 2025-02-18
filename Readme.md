# 🌟 Customer Reviews API

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![API: v1](https://img.shields.io/badge/API-v1-blue.svg)]()
[![Security: API Key](https://img.shields.io/badge/Security-API%20Key-green.svg)]()

> A modern, RESTful API for managing customer reviews with secure authentication and reCAPTCHA protection.

## 📑 Table of Contents

- [Features](#-features)
- [Authentication](#-authentication)
- [API Reference](#-api-reference)
  - [List Reviews](#list-reviews)
  - [Create Review](#create-review)
- [Error Handling](#-error-handling)
- [License](#-license)

## ✨ Features

- 🔐 Secure API key authentication
- 🤖 Google reCAPTCHA integration
- 📸 Image upload support
- ⭐ Rating system
- 🎯 Comprehensive review management

## 🔑 Authentication

All API requests require an API key to be included in the request headers:

```http
X-API-Key: $2a$12$WdgH3UCEBldog4tNNuXx3uNePxow63Wa3KFEzxa5BkdMq0vFKKuWy
```

## 📚 API Reference

### List Reviews

> Retrieve a list of all customer reviews

```http
GET /api/reviews
```

#### Headers

| Header          | Value              | Required |
|-----------------|--------------------| -------- |
| `Content-Type`  | `application/json` | Yes      |
| `X-API-Key`     | `$2a$12$WdgH3UCEBldog4tNNuXx3uNePxow63Wa3KFEzxa5BkdMq0vFKKuWy`   | Yes      |
| `Recaptcha Site Key` | `6LeCGsgqAAAAAD-6ST54sCpo8-YenYq3fMStBLVb` | Yes|

#### Example Request

```bash
curl -X GET 'https://api-test.blubirdinteractive.org/api/reviews' \
     -H 'Accept: application/json' \
     -H 'X-API-KEY: $2a$12$WdgH3UCEBldog4tNNuXx3uNePxow63Wa3KFEzxa5BkdMq0vFKKuWy'
```

#### Example Response

```json
{
    "reviews": [
        {
            "id": 3,
            "name": "GSM Masum",
            "email": "masumgsm114@gmail.com",
            "rating": 5,
            "title": "The Pencil is long, slender and thin",
            "review": "The Pencil is long, slender and thin. It looks and feels a lot like a Pencil, and if the design came before the name, then 'Pencil' is the clear choice.\nPros and cons: Feels great to hold · Weighted fantastically · Able to grip it with my fingers easily",
            "image_path": "url/to/image.jpg",
            "created_at": "2025-01-30T20:11:41.000000Z",
            "updated_at": "2025-01-30T20:11:41.000000Z"
        }
    ]
}
```

### Create Review

> Submit a new customer review

```http
POST /api/reviews
```

#### Headers

| Header          | Value              | Required |
|-----------------|--------------------| -------- |
| `Content-Type`  | `application/json` | Yes      |
| `X-API-Key`     | `$2a$12$WdgH3UCEBldog4tNNuXx3uNePxow63Wa3KFEzxa5BkdMq0vFKKuWy`   | Yes      |

#### Request Body

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "rating": 5,
    "comment": "Great service!",
    "g-recaptcha-response": "recaptcha-token-here"
}
```

#### Required Fields

| Field                  | Type      | Description                           |
|-----------------------|-----------|---------------------------------------|
| `name`                 | string    | Customer's name                       |
| `email`                | string    | Valid email address                   |
| `rating`               | integer   | Rating score (1-5)                    |
| `comment`              | string    | Review comment                        |
| `g-recaptcha-response` | string    | Google reCAPTCHA response token       |

#### Success Response

```json
{
    "message": "Review created successfully",
    "review": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "rating": 5,
        "comment": "Great service!",
        "created_at": "2024-03-19T10:00:00Z"
    }
}
```

## ⚠️ Error Handling

### Error Response Format

```json
{
    "message": "An error occurred",
    "error": "Error description"
}
```

### Status Codes

| Code  | Description           |
|-------|-----------------------|
| 200   | ✅ Success            |
| 201   | ✅ Created            |
| 400   | ❌ Bad request        |
| 401   | ❌ Unauthorized       |
| 422   | ❌ Validation error   |
| 500   | ❌ Server error       |

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
