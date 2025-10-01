# Stripe API Integration

Comprehensive Stripe API client for the Loppservice application, providing full integration with Stripe's payment processing, product management, and transaction tracking.

## Overview

The Stripe integration provides a complete set of API endpoints for managing payments, products, prices, and transactions within the Loppservice application.

## Features

- **Product Management**: Create, update, archive, and restore products
- **Price Management**: Create and manage product pricing
- **Transaction Tracking**: Get transaction counts and recent transactions
- **Account Information**: Retrieve account balance and status
- **Payment Processing**: Full Stripe payment integration

## Authentication

All Stripe API endpoints require:

- **API Key**: `apikey: testkey` (for development)
- **JWT Token**: Valid JWT token from `/api/login`

## Base URL

```
http://localhost:8082/loppservice/api/integration/stripe
```

## Endpoints

### 1. Account & Status

#### Get Stripe Status
**GET** `/status`

Returns the current Stripe configuration and status.

```bash
curl -X GET \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  "http://localhost:8082/loppservice/api/integration/stripe/status"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "api_key_configured": true,
    "test_mode": true,
    "currency": "eur"
  },
  "test_mode": true
}
```

#### Get Account Balance
**GET** `/balance`

Retrieves the current Stripe account balance.

```bash
curl -X GET \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  "http://localhost:8082/loppservice/api/integration/stripe/balance"
```

### 2. Product Management

#### Get All Products
**GET** `/products`

Retrieves all products from Stripe.

```bash
curl -X GET \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  "http://localhost:8082/loppservice/api/integration/stripe/products"
```

#### Get Single Product
**GET** `/products/{productId}`

Retrieves a specific product by ID.

```bash
curl -X GET \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  "http://localhost:8082/loppservice/api/integration/stripe/products/prod_1234567890"
```

#### Create Product
**POST** `/products`

Creates a new product. Optionally create a price in the same call.

**Basic Product:**
```bash
curl -X POST \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Event Registration",
    "description": "Registration for cycling event",
    "metadata": {"category": "registration"},
    "active": true
  }' \
  "http://localhost:8082/loppservice/api/integration/stripe/products"
```

**Product with Price:**
```bash
curl -X POST \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Event Registration",
    "description": "Registration for cycling event",
    "metadata": {"category": "registration"},
    "active": true,
    "price": {
      "unit_amount": 2500,
      "currency": "eur",
      "type": "one_time"
    }
  }' \
  "http://localhost:8082/loppservice/api/integration/stripe/products"
```

**Recurring Product:**
```bash
curl -X POST \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Monthly Subscription",
    "description": "Monthly membership",
    "price": {
      "unit_amount": 1000,
      "currency": "eur",
      "type": "recurring",
      "recurring": {
        "interval": "month"
      }
    }
  }' \
  "http://localhost:8082/loppservice/api/integration/stripe/products"
```

#### Update Product
**PUT** `/products/{productId}`

Updates an existing product.

```bash
curl -X PUT \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Product Name",
    "description": "Updated description",
    "metadata": {"category": "updated"}
  }' \
  "http://localhost:8082/loppservice/api/integration/stripe/products/prod_1234567890"
```

#### Archive Product
**PUT** `/products/{productId}/archive`

Archives a product (soft delete).

```bash
curl -X PUT \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  "http://localhost:8082/loppservice/api/integration/stripe/products/prod_1234567890/archive"
```

#### Restore Product
**PUT** `/products/{productId}/restore`

Restores an archived product.

```bash
curl -X PUT \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  "http://localhost:8082/loppservice/api/integration/stripe/products/prod_1234567890/restore"
```

#### Delete Product
**DELETE** `/products/{productId}`

Permanently deletes a product (only if no prices exist).

```bash
curl -X DELETE \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  "http://localhost:8082/loppservice/api/integration/stripe/products/prod_1234567890"
```

### 3. Price Management

#### Get Product Prices
**GET** `/products/{productId}/prices`

Retrieves all prices for a specific product.

```bash
curl -X GET \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  "http://localhost:8082/loppservice/api/integration/stripe/products/prod_1234567890/prices"
```

#### Create Price
**POST** `/products/{productId}/prices`

Creates a new price for a product.

```bash
curl -X POST \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  -H "Content-Type: application/json" \
  -d '{
    "unit_amount": 2500,
    "currency": "eur",
    "type": "one_time"
  }' \
  "http://localhost:8082/loppservice/api/integration/stripe/products/prod_1234567890/prices"
```

#### Set Default Price
**PUT** `/products/{productId}/prices/default`

Sets a price as the default for a product.

```bash
curl -X PUT \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  -H "Content-Type: application/json" \
  -d '{
    "price_id": "price_1234567890"
  }' \
  "http://localhost:8082/loppservice/api/integration/stripe/products/prod_1234567890/prices/default"
```

#### Create and Set Default Price
**POST** `/products/{productId}/prices/create-and-set-default`

Creates a new price and sets it as default in one call.

```bash
curl -X POST \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  -H "Content-Type: application/json" \
  -d '{
    "unit_amount": 2500,
    "currency": "eur",
    "type": "one_time"
  }' \
  "http://localhost:8082/loppservice/api/integration/stripe/products/prod_1234567890/prices/create-and-set-default"
```

### 4. Transaction Management

#### Get Transaction Counts
**GET** `/transactions/counts`

Retrieves transaction statistics (succeeded, refunded, failed, etc.).

```bash
curl -X GET \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  "http://localhost:8082/loppservice/api/integration/stripe/transactions/counts"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "all": 89,
    "succeeded": 89,
    "refunded": 0,
    "failed": 0,
    "disputed": 0,
    "uncaptured": 0,
    "filters_applied": []
  },
  "test_mode": true
}
```

**With Date Filters:**
```bash
curl -X GET \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  "http://localhost:8082/loppservice/api/integration/stripe/transactions/counts?created_after=1640995200&created_before=1672531200"
```

#### Get Recent Transactions
**GET** `/transactions/recent`

Retrieves recent transactions with detailed information.

```bash
curl -X GET \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  "http://localhost:8082/loppservice/api/integration/stripe/transactions/recent?limit=10"
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "pi_3SCalKJLy5yXc4qV1N8yrk72",
      "amount": 30000,
      "currency": "eur",
      "status": "succeeded",
      "amount_refunded": null,
      "created": 1759128042,
      "description": null,
      "metadata": []
    }
  ],
  "count": 1,
  "test_mode": true
}
```

## Error Handling

### Common Error Responses

#### 401 Unauthorized
```json
{
  "success": false,
  "error": "Unauthorized",
  "message": "JWT token is required"
}
```

#### 422 Validation Error
```json
{
  "success": false,
  "error": "Validation failed",
  "message": "Invalid input data",
  "errors": {
    "name": ["The name field is required."]
  }
}
```

#### 500 Stripe API Error
```json
{
  "success": false,
  "error": "Stripe API error",
  "message": "No such product: prod_invalid",
  "code": "resource_missing"
}
```

## Configuration

### Environment Variables

```env
# Stripe Configuration
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### Test Mode vs Production

- **Test Mode**: Uses Stripe test keys (pk_test_*, sk_test_*)
- **Production Mode**: Uses live keys (pk_live_*, sk_live_*)
- All responses include `test_mode` field indicating current mode

## Usage Examples

### Complete Product Creation Workflow

1. **Create Product with Price:**
```bash
curl -X POST \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Event Registration 2024",
    "description": "Registration for cycling event",
    "price": {
      "unit_amount": 5000,
      "currency": "eur",
      "type": "one_time"
    }
  }' \
  "http://localhost:8082/loppservice/api/integration/stripe/products"
```

2. **Check Transaction Counts:**
```bash
curl -X GET \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  "http://localhost:8082/loppservice/api/integration/stripe/transactions/counts"
```

3. **Get Recent Transactions:**
```bash
curl -X GET \
  -H "apikey: testkey" \
  -H "TOKEN: your_jwt_token" \
  "http://localhost:8082/loppservice/api/integration/stripe/transactions/recent?limit=5"
```

## Best Practices

1. **Always use HTTPS** in production
2. **Validate input data** before making API calls
3. **Handle errors gracefully** and provide meaningful messages
4. **Use test mode** during development
5. **Monitor transaction counts** for business insights
6. **Archive products** instead of deleting when possible
7. **Use webhooks** for real-time payment updates

## Support

For Stripe-specific issues, refer to:
- [Stripe API Documentation](https://stripe.com/docs/api)
- [Stripe Test Cards](https://stripe.com/docs/testing)
- [Stripe Webhooks](https://stripe.com/docs/webhooks)
