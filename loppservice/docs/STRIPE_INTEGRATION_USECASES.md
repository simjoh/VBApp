# Stripe Integration Use Cases

## 🎯 **Overview**

This document describes all possible use cases for the Stripe integration system in the loppservice. The system provides bidirectional synchronization between local products and Stripe products/prices, supporting both manual and automatic workflows.

## 🏗 **System Architecture**

- **StripeService**: API client for Stripe operations
- **StripeSyncService**: Business logic for synchronization
- **StripeSyncWebhookController**: Webhook event processing
- **Database Schema**: Enhanced products table with Stripe fields
- **Webhook Endpoints**: Real-time event processing

---

## 📋 **Complete Use Case Scenarios**

### **1. Product Creation Scenarios**

#### **1.1 Local GUI → Stripe Sync**
**Description**: Create product in your own user interface and sync to Stripe

**Flow**:
1. User creates product in your GUI
2. System saves locally with `sync_to_stripe = true`
3. Automatic or manual sync to Stripe
4. Product exists in both systems
5. Webhook skips sync (prevents duplicate)

**Use Case**: Payment products created through your application

**API Endpoint**: `POST /api/products`
**Webhook**: `product.created` (skipped due to metadata)

---

#### **1.2 Local GUI → Local Only**
**Description**: Create non-payment product in your GUI

**Flow**:
1. User creates product in your GUI
2. System saves locally with `sync_to_stripe = false`
3. No sync to Stripe
4. Product exists only locally

**Use Case**: Free products, informational items, internal products

**API Endpoint**: `POST /api/products`
**Webhook**: Not applicable

---

#### **1.3 Stripe Dashboard → Local Sync**
**Description**: Create product directly in Stripe Dashboard

**Flow**:
1. User creates product in Stripe Dashboard
2. Webhook `product.created` event triggered
3. System creates local product automatically
4. Product exists in both systems
5. Status: `sync_to_stripe = true`

**Use Case**: Products created directly in Stripe for testing or management

**Webhook**: `product.created`
**API Endpoint**: Not applicable

---

### **2. Price Management Scenarios**

#### **2.1 Stripe Dashboard → Add Price**
**Description**: Add price to existing Stripe product

**Flow**:
1. User adds price in Stripe Dashboard
2. Webhook `price.created` event triggered
3. System updates local product with price
4. Local product gets price and currency

**Database Updates**:
- `price_id`: Stripe price ID
- `price`: Amount (converted from cents)
- `currency`: Currency code (e.g., 'usd', 'sek')

**Use Case**: Adding pricing to existing products

**Webhook**: `price.created`

---

#### **2.2 Local GUI → Add Price via API**
**Description**: Add price to local product via your API

**Flow**:
1. User uses your API to add price
2. System creates price in Stripe via API
3. Webhook `price.created` event triggered
4. Price synced back to local product

**Use Case**: Programmatic price management

**API Endpoint**: `POST /api/integration/stripe/prices`
**Webhook**: `price.created`

---

#### **2.3 Stripe Dashboard → Update Price**
**Description**: Modify price in Stripe Dashboard

**Flow**:
1. User updates price in Stripe Dashboard
2. Webhook `price.updated` event triggered
3. System updates local product price
4. Price changes reflected locally

**Use Case**: Price adjustments, currency changes

**Webhook**: `price.updated`

---

#### **2.4 Local GUI → Update Price**
**Description**: Modify price in your GUI

**Flow**:
1. User updates price in your GUI
2. System updates local product
3. Sync updates Stripe price via API
4. Price changes reflected in Stripe

**Use Case**: Price management through your interface

**API Endpoint**: `PUT /api/integration/stripe/prices/{id}`

---

#### **2.5 Stripe Dashboard → Delete Price**
**Description**: Delete price in Stripe Dashboard

**Flow**:
1. User deletes price in Stripe Dashboard
2. Webhook `price.deleted` event triggered
3. System removes price from local product
4. Product has no price

**Database Updates**:
- `price_id`: null
- `price`: null
- `currency`: remains unchanged

**Use Case**: Removing pricing from products

**Webhook**: `price.deleted`

---

#### **2.6 Local GUI → Delete Price**
**Description**: Delete price in your GUI

**Flow**:
1. User deletes price in your GUI
2. System removes price from local product
3. Sync deletes Stripe price via API
4. Price removed from both systems

**Use Case**: Price removal through your interface

**API Endpoint**: `DELETE /api/integration/stripe/prices/{id}`

---

### **3. Product Update Scenarios**

#### **3.1 Stripe Dashboard → Update Product**
**Description**: Modify product in Stripe Dashboard

**Flow**:
1. User updates product in Stripe Dashboard
2. Webhook `product.updated` event triggered
3. System updates local product
4. Changes reflected in local database

**Updated Fields**:
- `productname`
- `description`
- `active`

**Use Case**: Product information updates

**Webhook**: `product.updated`

---

#### **3.2 Local GUI → Update Product**
**Description**: Modify product in your GUI

**Flow**:
1. User updates product in your GUI
2. System updates local product
3. Sync updates Stripe product via API
4. Changes reflected in Stripe

**Use Case**: Product management through your interface

**API Endpoint**: `PUT /api/products/{id}`

---

### **4. Product Deletion Scenarios**

#### **4.1 Stripe Dashboard → Delete Product**
**Description**: Delete product in Stripe Dashboard

**Flow**:
1. User deletes product in Stripe Dashboard
2. Webhook `product.deleted` event triggered
3. System marks local product as inactive or deleted
4. Product removed from both systems

**Use Case**: Product removal

**Webhook**: `product.deleted`

---

#### **4.2 Local GUI → Delete Product**
**Description**: Delete product in your GUI

**Flow**:
1. User deletes product in your GUI
2. System deletes local product
3. Sync archives/deletes Stripe product via API
4. Product removed from both systems

**Use Case**: Product removal through your interface

**API Endpoint**: `DELETE /api/products/{id}`

---

### **5. Synchronization Management Scenarios**

#### **5.1 Manual Sync All Pending**
**Description**: Sync all pending products at once

**Flow**:
1. User triggers manual sync endpoint
2. System finds all `sync_to_stripe = true` and `stripe_sync_status = 'pending'`
3. Creates all pending products in Stripe
4. All pending products synced

**Use Case**: Bulk sync, retry failed syncs

**API Endpoint**: `POST /api/syncing/manual-sync`

---

#### **5.2 Multi-Currency Products**
**Description**: Create products in different currencies

**Flow**:
1. User creates USD product in Stripe Dashboard
2. Webhook `product.created` event triggered
3. System creates local product with `currency = 'usd'`
4. When price added, `currency = 'usd'` maintained

**Supported Currencies**:
- SEK (Swedish Krona) - Default
- USD (US Dollar)
- EUR (Euro)
- Any ISO 4217 currency code

**Use Case**: International products

**Webhook**: `product.created`, `price.created`

---

#### **5.3 Failed Sync Recovery**
**Description**: Retry failed sync operations

**Flow**:
1. Previous sync failed (network, API error)
2. Status: `stripe_sync_status = 'failed'`
3. User triggers manual sync or retry
4. System attempts sync again
5. Success or remains failed

**Use Case**: Error recovery, network issues

**API Endpoint**: `POST /api/syncing/manual-sync`

---

#### **5.4 Webhook Failure Recovery**
**Description**: Webhook fails to process

**Flow**:
1. Stripe sends webhook event
2. Webhook processing fails
3. Stripe retries webhook (up to 3 times)
4. System eventually processes successfully
5. Event eventually synced

**Use Case**: Temporary system issues

**Webhook**: Any Stripe webhook event

---

### **6. Data Integrity Scenarios**

#### **6.1 Duplicate Prevention**
**Description**: Prevent duplicate products

**Flow**:
1. Product created locally with `local_product_id` metadata
2. Webhook `product.created` event received
3. System checks metadata for `local_product_id`
4. Skips webhook sync (prevents duplicate)

**Use Case**: Bidirectional sync safety

**Webhook**: `product.created`

---

#### **6.2 Category Assignment**
**Description**: Assign categories to Stripe products

**Flow**:
1. User creates product in Stripe Dashboard
2. Webhook `product.created` event triggered
3. System assigns default category
4. Product gets proper category

**Configuration**: `stripe.default_category_id`

**Use Case**: Product organization

**Webhook**: `product.created`

---

#### **6.3 Metadata Synchronization**
**Description**: Sync custom metadata

**Flow**:
1. User adds metadata in Stripe Dashboard
2. Webhook `product.updated` event triggered
3. System updates local `stripe_metadata` field
4. Custom data preserved

**Use Case**: Additional product information

**Webhook**: `product.updated`

---

## 🔧 **Technical Implementation**

### **Database Schema**
```sql
CREATE TABLE products (
    productID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    productname varchar(100) NOT NULL,
    description varchar(100) DEFAULT NULL,
    full_description varchar(400) DEFAULT NULL,
    active tinyint(1) NOT NULL,
    categoryID bigint(20) unsigned NOT NULL,
    price decimal(8,2) DEFAULT NULL,
    currency varchar(3) DEFAULT 'sek',
    price_id varchar(255) DEFAULT NULL,
    stripe_product_id varchar(255) DEFAULT NULL,
    stripe_sync_status enum('synced', 'pending', 'failed') DEFAULT 'pending',
    last_stripe_sync timestamp NULL DEFAULT NULL,
    stripe_metadata json DEFAULT NULL,
    sync_to_stripe boolean DEFAULT false,
    productable_type varchar(255) DEFAULT '',
    productable_id int(11) DEFAULT 0,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (productID)
);
```

### **API Endpoints**
- `GET /api/integration/stripe/products` - List Stripe products
- `POST /api/integration/stripe/products` - Create Stripe product
- `PUT /api/integration/stripe/products/{id}` - Update Stripe product
- `DELETE /api/integration/stripe/products/{id}` - Delete Stripe product
- `POST /api/integration/stripe/prices` - Create Stripe price
- `PUT /api/integration/stripe/prices/{id}` - Update Stripe price
- `DELETE /api/integration/stripe/prices/{id}` - Delete Stripe price
- `GET /api/integration/stripe/balance` - Get account balance
- `GET /api/integration/stripe/transactions` - Get transaction stats
- `POST /api/syncing/manual-sync` - Manual sync pending products

### **Webhook Endpoints**
- `POST /syncing/events` - Stripe webhook events
- `POST /api/syncing/events` - Alternative webhook endpoint

### **Environment Variables**
```env
STRIPE_SECRET_KEY=sk_test_...
STRIPE_CLI_WEBHOOK_SECRET=whsec_...
STRIPE_DEFAULT_CATEGORY_ID=1
```

---

## 🚀 **Getting Started**

### **1. Setup Webhook Forwarding**
```bash
stripe listen --forward-to http://localhost:8082/syncing/events
```

### **2. Test Product Creation**
```bash
# Create product in Stripe Dashboard
# Check webhook logs
docker exec -it vbapp-app-1 tail -f /var/www/html/storage/logs/laravel.log
```

### **3. Manual Sync Test**
```bash
curl -X POST http://localhost:8082/api/syncing/manual-sync \
  -H "APIKEY: notsecret_developer_key" \
  -H "TOKEN: your_jwt_token"
```

---

## 📊 **Status Tracking**

### **Sync Status Values**
- `pending`: Waiting to sync to Stripe
- `synced`: Successfully synchronized
- `failed`: Sync failed, needs retry

### **Sync Flags**
- `sync_to_stripe`: Boolean flag indicating if product should sync to Stripe
- `stripe_product_id`: Stripe product ID (null if not synced)
- `price_id`: Stripe price ID (null if no price)

---

## 🔍 **Troubleshooting**

### **Common Issues**
1. **Webhook 400 errors**: Check signature verification
2. **Sync failures**: Check API keys and network connectivity
3. **Duplicate products**: Verify metadata handling
4. **Missing prices**: Check price creation workflow

### **Debug Commands**
```bash
# Check webhook logs
docker exec -it vbapp-app-1 tail -f /var/www/html/storage/logs/laravel.log

# Check database sync status
mysql -h 192.168.1.194 -P 3309 -u root -psecret --skip-ssl -e "USE vasterbottenbrevet_se_db_2; SELECT productID, productname, stripe_sync_status, sync_to_stripe FROM products;"

# Test webhook endpoint
curl -X POST http://localhost:8082/syncing/events -H "Content-Type: application/json" -d '{"test": "data"}'
```

---

## 📝 **Best Practices**

1. **Always set `sync_to_stripe` flag** for payment products
2. **Use webhook events** for real-time synchronization
3. **Handle failed syncs** with retry mechanisms
4. **Monitor sync status** in your application
5. **Test webhook forwarding** in development
6. **Use proper error handling** for all operations
7. **Maintain data consistency** between systems
8. **Log all sync operations** for debugging

---

## 🎉 **System Status**

**All use cases are fully implemented and tested!**

- ✅ Product creation (Local ↔ Stripe)
- ✅ Price management (Local ↔ Stripe)
- ✅ Product updates (Local ↔ Stripe)
- ✅ Product deletion (Local ↔ Stripe)
- ✅ Manual synchronization
- ✅ Multi-currency support
- ✅ Error recovery
- ✅ Webhook processing
- ✅ Data integrity
- ✅ Metadata synchronization

**Ready for production use!** 🚀
