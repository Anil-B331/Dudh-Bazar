# Dudhbazar

A full-stack e-commerce web application for milk and dairy products.

## Technologies Used
- **Frontend:** Vanilla HTML, CSS (Glassmorphism design), JavaScript
- **Backend:** PHP 8+
- **Database:** MySQL
- **Architecture:** Procedural/Session-based, PDO for database interactions

## Features
- **Product Catalog:** Browse dairy products by category.
- **Shopping Cart & Checkout:** One-off purchases using Digital Wallet.
- **Subscriptions:** Daily milk delivery subscriptions with a "Vacation Mode" toggle to pause/resume.
- **Digital Wallet:** Top-up your wallet with custom or predefined amounts using mock eSewa/Fonepay integrations.
- **Structured Logging:** Wallet transactions are logged to `wallet_transactions.log` securely.

## How to Run Locally

1. **Prerequisites:** 
   - A local web server like XAMPP, WAMP, or MAMP installed with PHP and MySQL running.
   - Alternatively, you can use PHP's built-in server.

2. **Database Configuration:**
   - The app attempts to create the database `dudhbazar` and its tables automatically. 
   - By default, it connects using `root` and an empty password `''`. 
   - You can change these credentials in `includes/db.php`.

3. **Start the Application:**
   - If using XAMPP, move the `Dudhbazar` folder to your `htdocs` directory and navigate to `http://localhost/Dudhbazar`.
   - Alternatively, open your terminal in the `Dudhbazar` directory and run:
     ```bash
     php -S localhost:8000
     ```
   - Then navigate to `http://localhost:8000` in your browser.

4. **Initialize Data:**
   - Open `http://localhost:8000/seed.php` in your browser ONCE to initialize the database tables and seed products.
   - You can then go to the homepage and start using the app.

## Integrating Actual eSewa/Fonepay Later
Currently, `api/wallet_topup.php` uses mock logic. To integrate real SDKs:
1. Replace the mock transaction logic with API calls to eSewa's `/epay/main` endpoint.
2. Set up a webhook/callback URL in a new file (e.g., `api/esewa_callback.php`) to verify the transaction using the unique `reference_id` generated.
3. Only update the `users.wallet_balance` after successful verification from the callback.
