# 🚀 Laravel API: Social Authentication with Passport

A robust Laravel API backend focused on secure authentication using **Laravel Passport** for API token management and **Laravel Socialite** for seamless social login (Google). The API is documented using **L5-Swagger** for easy consumption by frontend or mobile clients.

---

## 🔗 Repository

Source code: [https://github.com/judempoyo/laravel_api.git](https://github.com/judempoyo/laravel_api.git)

---

## ✨ Key Features

- **Social Authentication:** Secure login/registration via Google (Socialite)
- **API Token Management:** Token issuance and verification with Passport
- **API Versioning:** All endpoints prefixed with `/api/v1`
- **Comprehensive Documentation:** Live, interactive docs via L5-Swagger
- **Modern Stack:** PHP ^8.2, supports SQLite, MySQL, etc.

---

## ⚙️ Getting Started

### Prerequisites

- PHP ≥8.2
- Composer
- Node.js & npm (optional, for asset compilation)

### Installation

1. **Clone the Repository:**
    ```bash
    git clone https://github.com/judempoyo/laravel_api.git
    cd laravel_api
    ```

2. **Install PHP Dependencies:**
    ```bash
    composer install
    ```

3. **Setup Environment File:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database Configuration (Example: SQLite):**
    ```bash
    touch database/database.sqlite
    # Edit .env and set:
    # DB_CONNECTION=sqlite
    ```

5. **Run Migrations:**
    ```bash
    php artisan migrate
    ```

6. **Install & Configure Passport:**
    ```bash
    php artisan passport:install
    ```

7. **Start the Local Server:**
    ```bash
    php artisan serve
    ```
    The API will run at [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## 🔐 Google Socialite Configuration

1. **Google Cloud Console:**
    - Create a Web Application OAuth Client ID.
    - Set the Authorized Redirect URI to:
      ```
      http://127.0.0.1:8000/api/v1/auth/socialite/google/callback
      ```

2. **Update `.env`:**
    ```ini
    # --- Google Socialite Credentials ---
    GOOGLE_CLIENT_ID="YOUR_GOOGLE_CLIENT_ID"
    GOOGLE_CLIENT_SECRET="YOUR_GOOGLE_CLIENT_SECRET"
    GOOGLE_REDIRECT="http://127.0.0.1:8000/api/v1/auth/socialite/google/callback"
    ```

3. **Clear Configuration Cache:**
    ```bash
    php artisan config:clear
    ```

---

## 📚 API Documentation (L5-Swagger)

- Access the interactive docs at:  
  [http://127.0.0.1:8000/api/documentation](http://127.0.0.1:8000/api/documentation)

- **Regenerate docs after annotation changes:**
    ```bash
    php artisan l5-swagger:generate
    ```

- **Common Fix:**  
  If you see `Route [login] not defined.`, edit `config/l5-swagger.php` and remove/comment the `'auth'` middleware:
    ```php
    // config/l5-swagger.php
    'middleware' => [
        'api' => [
            // ... (other middlewares)
            // 'auth',  // REMOVE OR COMMENT THIS OUT
        ],
    ],
    ```

---

## 🔑 Key Endpoints

| Method | Path                                               | Description                                         | Authentication |
|--------|----------------------------------------------------|-----------------------------------------------------|----------------|
| POST   | `/api/v1/auth/register`                            | Register a new user                                 | None           |
| POST   | `/api/v1/auth/login`                               | Login with email/password and get a token           | None           |
| GET    | `/api/v1/auth/socialite/{provider}`                | Start Socialite OAuth flow (e.g., `/google`)        | None           |
| GET    | `/api/v1/auth/socialite/{provider}/callback`       | Handle OAuth callback, issue Passport token         | None           |
| GET    | `/api/v1/auth/user`                                | Get details of authenticated user                   | Bearer Token   |
| POST   | `/api/v1/auth/logout`                              | Revoke current access token                         | Bearer Token   |

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. **Fork:** [https://github.com/judempoyo/laravel_api.git](https://github.com/judempoyo/laravel_api.git)
2. **Create branch:**  
   `git checkout -b feature/AmazingFeature`
3. **Commit:**  
   `git commit -m 'feat: Add AmazingFeature'`
4. **Push:**  
   `git push origin feature/AmazingFeature`
5. **Open a Pull Request** against the `main` branch.

### Coding Standards

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard.
- Cover all new features with Open API annotations in controllers.
- Run tests before submitting:  
  `php artisan test`

---

> Learn more about Google Socialite in Laravel:  
> [Laravel Socialite Login with Google and Github](https://www.youtube.com/results?search_query=laravel+socialite+login+with+google+and+github)