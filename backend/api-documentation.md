# 🏔️ Matterhorn.co — API Documentation

> **Base URL**: `http://localhost:8000/api`  
> **Auth**: Laravel Sanctum (Bearer Token)  
> **Format**: JSON (`Content-Type: application/json`, `Accept: application/json`)

---

## Table of Contents

- [Authentication](#authentication)
- [Products](#products)
- [Cart](#cart)
- [Profile](#profile)
- [Addresses](#addresses)
- [Store Status](#store-status)
- [Admin — Rentals](#admin--rentals)
- [Admin — Products](#admin--products)
- [Admin — Payments](#admin--payments)
- [Admin — Users](#admin--users)
- [Standard Response Format](#standard-response-format)
- [Error Handling](#error-handling)

---

## Standard Response Format

Semua endpoint menggunakan format response yang konsisten:

```json
{
  "status": "success" | "error",
  "message": "Optional message string",
  "data": { ... },
  "meta": { "current_page": 1, "last_page": 5, "per_page": 15, "total": 72 }
}
```

## Error Handling

| HTTP Code | Meaning |
|-----------|---------|
| `200` | OK |
| `201` | Created |
| `401` | Unauthenticated — token missing/invalid |
| `403` | Forbidden — insufficient permissions |
| `404` | Resource not found |
| `422` | Validation error |
| `500` | Server error |

Validation errors return:
```json
{
  "status": "error",
  "message": "Validation failed message",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

---

## Authentication

### `POST /auth/register`

Create a new user account.

**Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone_number": "08123456789"
}
```

**Response (201):**
```json
{
  "status": "success",
  "message": "Pendaftaran berhasil.",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "customer",
      "phone_number": "08123456789",
      "address": null,
      "created_at": "2026-04-14T12:00:00.000000Z"
    },
    "token": "1|abc123..."
  }
}
```

---

### `POST /auth/login`

Authenticate and receive a Bearer token.

**Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Login berhasil.",
  "data": {
    "user": { ... },
    "token": "2|xyz789..."
  }
}
```

**Error (401):**
```json
{
  "status": "error",
  "message": "Email atau password salah."
}
```

---

### `GET /auth/me` 🔒

Get the currently authenticated user.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "customer",
      "phone_number": "08123456789",
      "address": null,
      "created_at": "2026-04-14T12:00:00.000000Z"
    }
  }
}
```

---

### `POST /auth/logout` 🔒

Revoke the current access token.

**Response (200):**
```json
{
  "status": "success",
  "message": "Logout berhasil."
}
```

---

## Products

### `GET /products`

List all products with optional filters.

**Query Parameters:**

| Param | Type | Description |
|-------|------|-------------|
| `search` | string | Search by product name |
| `category` | string | Filter by category |
| `gender` | string | `unisex`, `pria`, `wanita`, `anak` |
| `min_price` | number | Minimum price filter |
| `max_price` | number | Maximum price filter |
| `sort` | string | `newest`, `price_asc`, `price_desc`, `name_asc` |
| `per_page` | number | Items per page (default: 15, max: 50) |
| `page` | number | Page number |

**Response (200):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Carrier Eiger 60L",
      "category": "Carrier & Daypack",
      "gender": "unisex",
      "description": "Carrier kapasitas 60 liter...",
      "image_url": "http://localhost:8000/images/carrier.jpg",
      "price_24h": 50000,
      "stock_total": 10,
      "stock_available": 7,
      "created_at": "2026-01-15T00:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 42
  }
}
```

---

### `GET /products/categories`

Get all unique product categories.

**Response (200):**
```json
{
  "status": "success",
  "data": ["Carrier & Daypack", "Tenda", "Sleeping Bag", "Kompor & Memasak"]
}
```

---

### `GET /products/{id}`

Get a single product with sizes.

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Carrier Eiger 60L",
    "category": "Carrier & Daypack",
    "gender": "unisex",
    "description": "...",
    "image_url": "http://localhost:8000/images/carrier.jpg",
    "price_24h": 50000,
    "stock_total": 10,
    "stock_available": 7,
    "sizes": [
      { "size": "M", "stock": 3 },
      { "size": "L", "stock": 4 }
    ],
    "created_at": "2026-01-15T00:00:00.000000Z"
  }
}
```

---

## Cart 🔒

All cart endpoints require `Authorization: Bearer {token}`.

### `GET /cart`

Get the authenticated user's cart.

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "items": [
      {
        "id": "cart_6621a...",
        "product_id": 1,
        "quantity": 2,
        "product": {
          "id": 1,
          "name": "Carrier Eiger 60L",
          "price_24h": 50000,
          "image_url": "..."
        }
      }
    ],
    "total_items": 2,
    "total_price": 100000
  }
}
```

---

### `POST /cart`

Add an item to the cart.

**Body:**
```json
{
  "product_id": 1,
  "quantity": 2
}
```

**Response (201):**
```json
{
  "status": "success",
  "message": "Carrier Eiger 60L ditambahkan ke keranjang.",
  "data": { "total_items": 3 }
}
```

---

### `PUT /cart/{itemId}`

Update item quantity.

**Body:**
```json
{
  "quantity": 3
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Jumlah item diperbarui.",
  "data": { "total_items": 4, "total_price": 150000 }
}
```

---

### `DELETE /cart/{itemId}`

Remove a specific item from the cart.

---

### `DELETE /cart`

Clear the entire cart.

---

## Profile 🔒

### `GET /profile`

Get authenticated user's profile with addresses.

### `PUT /profile`

Update profile info.

**Body:**
```json
{
  "name": "New Name",
  "phone_number": "08123456789"
}
```

### `PUT /profile/password`

Change password.

**Body:**
```json
{
  "current_password": "old_password",
  "new_password": "new_password",
  "new_password_confirmation": "new_password"
}
```

---

## Addresses 🔒

### `GET /addresses`

List all saved addresses.

### `POST /addresses`

Create a new address.

**Body:**
```json
{
  "label": "Rumah",
  "recipient_name": "John Doe",
  "phone_number": "08123456789",
  "address_line": "Jl. Merdeka No. 123",
  "city": "Bandung",
  "province": "Jawa Barat",
  "postal_code": "40154",
  "is_default": true
}
```

### `PUT /addresses/{id}`

Update an address.

### `DELETE /addresses/{id}`

Delete an address.

### `PUT /addresses/{id}/default`

Set an address as default.

---

## Store Status

### `GET /store-status`

Get the current open/closed status of the physical store.

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "is_open": true,
    "current_time": "14:30",
    "open_time": "09:00",
    "close_time": "21:45",
    "message": "Open · Closes 9.45 pm"
  }
}
```

---

## Admin — Rentals 🔒🛡️

All admin endpoints require `Authorization: Bearer {token}` + `role: admin`.

### `GET /admin/rentals`

List all rentals with filters.

**Query Parameters:**

| Param | Type | Description |
|-------|------|-------------|
| `status` | string | `booked`, `active`, `returned`, `canceled`, `overdue` |
| `search` | string | Search by invoice_no or customer name |
| `user_id` | number | Filter by customer |
| `date_from` | date | Start date filter (YYYY-MM-DD) |
| `date_to` | date | End date filter (YYYY-MM-DD) |
| `sort` | string | `newest`, `oldest`, `price_asc`, `price_desc` |
| `per_page` | number | Items per page (default: 15) |

### `POST /admin/rentals`

Create a rental on behalf of a customer.

**Body:**
```json
{
  "user_id": 2,
  "items": [
    { "product_id": 1, "quantity": 1 },
    { "product_id": 6, "quantity": 2 }
  ],
  "start_date": "2026-04-15 08:00:00",
  "end_date": "2026-04-17 08:00:00",
  "guarantee_info": "KTP - 320115xxxx",
  "payment_method": "qris"
}
```

### `GET /admin/rentals/{id}`

Get rental details.

### `PUT /admin/rentals/{id}`

Update rental dates/guarantee info.

### `PUT /admin/rentals/{id}/status`

Update rental status with business logic:

| From → To | Action |
|-----------|--------|
| `booked → active` | Admin confirms item picked up |
| `active → returned` | Admin confirms items returned, restore stock, calculate fine if late |
| `active → overdue` | Mark as overdue, calculate fine (10% per day) |
| `booked → canceled` | Cancel booking, restore stock, remove pending payment |

**Body:**
```json
{
  "status": "active"
}
```

### `DELETE /admin/rentals/{id}`

Delete a rental (restores stock if active/booked/overdue).

---

## Admin — Products 🔒🛡️

### `GET /admin/products` | `POST /admin/products` | `GET /admin/products/{id}` | `PUT /admin/products/{id}` | `DELETE /admin/products/{id}`

Standard CRUD for product management.

---

## Admin — Payments 🔒🛡️

### `GET /admin/payments`

List all payments.

### `PUT /admin/payments/{id}/verify`

Verify a pending payment.

### `POST /admin/payments/{rentalId}/upload-proof`

Upload payment proof (FormData with `proof` file).

---

## Admin — Users 🔒🛡️

### `GET /admin/users` | `POST /admin/users` | `GET /admin/users/{id}` | `PUT /admin/users/{id}` | `DELETE /admin/users/{id}`

Standard CRUD for user management.

---

## Legend

| Symbol | Meaning |
|--------|---------|
| 🔒 | Requires `Authorization: Bearer {token}` |
| 🛡️ | Requires admin role |

---

*Last updated: 2026-04-14*
