# realstate2-final

A PHP‑based real‑estate platform that connects buyers with sellers, provides an admin dashboard for managing listings, users, and reports, and integrates Stripe for secure payments.

---

## Overview

`realstate2-final` is a full‑stack web application designed for real‑estate agencies. It offers:

- **Admin panel** – manage properties, users, and generate reports.
- **Buyer portal** – browse listings, contact sellers, save favourites, and complete purchases via Stripe.
- **Responsive UI** – clean, mobile‑friendly design using custom CSS.

The project is structured with separate directories for admin and buyer functionalities, a shared configuration file, and a SQL dump for the initial database schema.

---

## Features

| Area | Key Capabilities |
|------|------------------|
| **Admin** | Login/logout, property CRUD, user management, status updates, report generation, reply to buyer messages |
| **Buyer** | Dashboard, property search & details, favourite listings, messaging, report a property, profile editing, Stripe payment integration |
| **Common** | Centralized configuration, reusable navigation bars, CSS styling, secure session handling |
| **Database** | MySQL schema (`Database/realstate_db.sql`) with tables for users, properties, messages, payments, and reports |

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 7.x+ |
| **Database** | MySQL |
| **Payments** | Stripe (replace with `YOUR_OWN_API_KEY`) |
| **Frontend** | HTML5, CSS3 (custom `style.css`) |
| **Server** | Apache / Nginx (LAMP stack) |

---

## Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/yourusername/realstate2-final.git
   cd realstate2-final
   ```

2. **Set up the database**

   - Create a new MySQL database (e.g., `realstate_db`).
   - Import the schema:

     ```bash
     mysql -u your_user -p your_password realstate_db < Database/realstate_db.sql
     ```

3. **Configure the application**

   - Copy `config.php.example` to `config.php` (if provided) or edit the existing `config.php` and `admin/config.php` / `buyer/config.php` files.
   - Update the following placeholders:

     ```php
     // Example in config.php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'realstate_db');
     define('DB_USER', 'YOUR_DB_USER');
     define('DB_PASS', 'YOUR_DB_PASSWORD');

     // Stripe
     define('STRIPE_SECRET_KEY', 'YOUR_OWN_API_KEY');
     define('STRIPE_PUBLISHABLE_KEY', 'YOUR_OWN_API_KEY');
     ```

4. **Set up the web server**

   - Point your virtual host document root to the project folder.
   - Ensure PHP is enabled and the `mysqli` extension is installed.

5. **Adjust file permissions** (if needed)

   ```bash
   sudo chown -R www-data:www-data .
   sudo chmod -R 755 .
   ```

6. **Visit the site**

   - Admin login: `http://your-domain.com/admin/admin_login.php`
   - Buyer portal (home): `http://your-domain.com/index.php`

---

## Usage

### Admin Workflow

1. **Login** – `admin/admin_login.php` (default credentials can be set in the DB).
2. **Dashboard** – `admin/admin_home.php` provides quick stats.
3. **Manage Properties** – `admin/view_properties.php` → add, edit, delete listings.
4. **User Management** – `admin/view_users.php` to view or deactivate accounts.
5. **Reports** – `admin/view_reports.php` for generated analytics.
6. **Messaging** – `admin/admin_reply.php` to