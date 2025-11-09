# Order Management Demo (Laravel + Vue)

This is a small order management demo app built with **Laravel 8**, **Vue 3**, **Tailwind CSS**, and **Laravel Mix**.

It’s not meant to be a full-blown production system – more of a structured example showing:

- A clean **service layer** in Laravel (`OrderService`)
- API built with **Form Requests** + **API Resources**
- A Vue frontend that talks to the API via a tiny `api/orders.js` helper
- A simple UI to browse orders, view details, and update status

---

## Features

**Orders, Customers, Products, Order Items**
  - Orders belong to customers and have line items
  - Items are linked to products with unit price + line totals

**API-first backend**
  - `/api/orders` returns paginated orders
  - Orders include customer + item + product data
  - Status updates via `/api/orders/{id}/status` (`pending`, `paid`, `shipped`, `cancelled`)

**Vue + Tailwind UI**
  - Order list with basic styling
  - Click a row to open a **modal** with full order details
  - Buttons to update the order status in-place
  - Pagination controls (Previous / Next)

**Seed data for demo**
  - Factories for customers, products, and orders
  - `php artisan db:seed` gives you a nice dataset to play with

---

## Tech Stack

- **Backend**
  - PHP 8.x
  - Laravel 8.x
  - Eloquent models + migrations + seeders
  - Service layer (`app/Services/OrderService.php`)
  - Form Requests (`StoreOrderRequest`, `UpdateOrderStatusRequest`)
  - API Resources (`OrderResource`, `OrderItemResource`, etc.)

- **Frontend**
  - Vue 3 (single-file components)
  - Tailwind CSS
  - Laravel Mix
  - Custom `resources/js/api/orders.js` for API calls

---

### Running

```bash
composer install

npm install

Copy the example env file and update your settings:

php artisan migrate

php artisan db:seed

php artisan serve

npm run dev

- **Frontend + API consumer:** `http://127.0.0.1:8000/`
- **Raw API (paginated orders):** `http://127.0.0.1:8000/api/orders`


## How It’s Structured

### Backend

- `app/Models`  
  - `Order`, `OrderItem`, `Customer`, `Product`

- `app/Services/OrderService.php`  
  - `listOrders()` – returns paginated orders with relations  
  - `getOrder($id)` – single order with relations  
  - `createOrder(array $data)` – create order & items, update total  
  - `updateStatus($id, $status)` – update order status  
  - `deleteOrder($id)` – delete an order (and cascading items)

- `app/Http/Requests`  
  - `StoreOrderRequest` – validation for creating orders  
  - `UpdateOrderStatusRequest` – validation for status updates

- `app/Http/Resources`  
  - `OrderResource` – shapes the order JSON (customer + items + products)  
  - `OrderItemResource`, `CustomerResource`, `ProductResource`

- `routes/api.php`  
  - `GET /api/orders`  
  - `GET /api/orders/{id}`  
  - `POST /api/orders`  
  - `POST /api/orders/{id}/status`  
  - `DELETE /api/orders/{id}`

### Frontend

- `resources/js/app.js`  
  - Bootstraps Vue and mounts the main component

- `resources/js/components/OrdersList.vue`  
  - Fetches paginated orders
  - Shows table + pagination
  - Controls the order details modal and status updates

- `resources/js/components/OrderDetailsCard.vue`  
  - Renders a modal with full order details + status buttons

- `resources/js/api/orders.js`  
  - `fetchOrders(page)` – wraps `/api/orders?page=...`  
  - `updateOrderStatus(id, status)` – POSTs to `/api/orders/{id}/status`

- `resources/css/app.css`  
  - Tailwind entrypoint

---
