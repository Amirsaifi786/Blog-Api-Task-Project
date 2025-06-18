
# 📝 Laravel Blog API with Sanctum Authentication

This project is a robust Blog API built with Laravel, featuring user authentication powered by Laravel Sanctum. It provides endpoints for user registration, login, creating, reading, updating posts, and commenting on posts.

---

## ✨ Features

* **User Authentication:** Secure user registration and login using Laravel Sanctum.
* **Token-Based API:** All authenticated endpoints utilize Bearer tokens for authorization.
* **Post Management:** Create, retrieve, and update blog posts.
* **Commenting System:** Users can add comments to specific posts.
* **Structured Responses:** API responses are formatted in JSON for easy consumption.
* **Pagination:** Post listings include pagination metadata.

---

## 🚀 Getting Started

Follow these steps to set up and run the Blog API on your local machine.

### 1. Project Setup

First, create a new Laravel project and install Laravel Sanctum:

```bash
# Create a new Laravel project
composer create-project laravel/laravel blog-api

# Navigate into the project directory
cd blog-api

# Install Laravel Sanctum
composer require laravel/sanctum

# Publish Sanctum's configuration and migration files
php artisan vendor:publish --tag="sanctum-config"
php artisan vendor:publish --tag="sanctum-migrations"

# Run migrations to create necessary tables (including users and personal_access_tokens)
php artisan migrate
```

**Note:** Ensure your `.env` file is properly configured with your database credentials.

### 2. Database Migrations and Models (Conceptual)

While not explicitly provided, ensure you have appropriate migrations and models for `Post` and `Comment`, with their `fillable` properties correctly defined, and relationships set up.

**Example Models (Conceptual):**

* **`app/Models/User.php`** (Laravel's default, ensure it uses `HasApiTokens` trait)
    ```php
    use Laravel\Sanctum\HasApiTokens;
    // ...
    class User extends Authenticatable
    {
        use HasApiTokens, Notifiable;
        // ...
    }
    ```
* **`app/Models/Post.php`**
    ```php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Post extends Model
    {
        use HasFactory;

        protected $fillable = ['title', 'body', 'user_id'];

        public function user()
        {
            return $this->belongsTo(User::class);
        }

        public function comments()
        {
            return $this->hasMany(Comment::class);
        }
    }
    ```
* **`app/Models/Comment.php`**
    ```php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Comment extends Model
    {
        use HasFactory;

        protected $fillable = ['body', 'user_id', 'post_id'];

        public function user()
        {
            return $this->belongsTo(User::class);
        }

        public function post()
        {
            return $this->belongsTo(Post::class);
        }
    }
    ```
* **Corresponding Migrations:** Ensure migrations for `posts` and `comments` tables exist with foreign keys linking `user_id` and `post_id`.

### 3. Configure Sanctum API Guard

In `config/auth.php`, ensure `sanctum` is listed under the `guards` section and `api` is using the `sanctum` driver:

```php
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'sanctum', // Ensure this is 'sanctum'
            'provider' => 'users',
            'hash' => false,
        ],
    ],
```

And ensure the `routes/api.php` file routes are wrapped in the `auth:sanctum` middleware where required.

---

## 💻 API Endpoints

All API endpoints are prefixed with `http://localhost/blog-api/public/api/`. Make sure your Laravel development server is running (e.g., `php artisan serve` or using XAMPP/WAMP).

### 1. User Registration

* **URL:** `http://localhost/blog-api/public/api/register`
* **Method:** `POST`
* **Headers:** `Content-Type: application/json`
* **Body (JSON - raw):**
    ```json
    {
      "name": "Amir",
      "email": "amir@gmail.com",
      "password": "password",
      "password_confirmation": "password"
    }
    ```
* **Success Response:**
    ```json
    {
      "user": {
        "id": 1,
        "name": "Amir",
        "email": "amir@example.com",
        // ... other user details
      },
      "token": "1|eB2x9g3Zr...your_sanctum_token..."
    }
    ```

### 2. User Login

* **URL:** `http://localhost/blog-api/public/api/login`
* **Method:** `POST`
* **Headers:** `Content-Type: application/json`
* **Body (JSON - raw):**
    ```json
    {
      "email": "amir@gmail.com",
      "password": "password"
    }
    ```
* **Success Response:** (Similar to registration, returns user details and a new token)
    ```json
    {
      "user": {
        "id": 1,
        "name": "Amir",
        "email": "amir@gmail.com",
        // ...
      },
      "token": "2|ABCDE123...another_sanctum_token..."
    }
    ```
    **Note:** This `token` will be used for authenticated requests.

### 3. Create a Post (Authenticated)

* **URL:** `http://localhost/blog-api/public/api/posts`
* **Method:** `POST`
* **Headers:**
    * `Content-Type: application/json`
    * `Authorization: Bearer <your_sanctum_token>` (Replace `<your_sanctum_token>` with the token obtained from login/registration)
* **Body (JSON - raw):**
    ```json
    {
      "title": "This is my first blog post",
      "body": "This is the content of my first blog post."
    }
    ```
* **Success Response:** (Example)
    ```json
    {
        "post": {
            "title": "This is my first blog post",
            "body": "This is the content of my first blog post.",
            "user_id": 1,
            "updated_at": "2025-06-13T09:49:23.000000Z",
            "created_at": "2025-06-13T09:49:23.000000Z",
            "id": 1
        }
    }
    ```

### 4. Get All Posts

* **URL:** `http://localhost/blog-api/public/api/posts`
* **Method:** `GET`
* **Headers:** (No authentication required for public access, but can be added if your route is protected)
* **Success Response (with Pagination and eager loaded user/comments):**
    ```json
    {
        "data": [
            {
                "id": 1,
                "title": "this is title",
                "body": "this is body of the post",
                "user": {
                    "id": 1,
                    "name": "Amir"
                },
                "comments": [],
                "created_at": "2025-06-13 09:49:23"
            },
            {
                "id": 2,
                "title": "this is title1",
                "body": "this is body of the post1",
                "user": {
                    "id": 1,
                    "name": "Amir"
                },
                "comments": [],
                "created_at": "2025-06-13 09:56:08"
            }
        ],
        "links": {
            "first": "http://localhost/blog-api/public/api/posts?page=1",
            "last": "http://localhost/blog-api/public/api/posts?page=1",
            "prev": null,
            "next": null
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "last_page": 1,
            "links": [
                {
                    "url": null,
                    "label": "&laquo; Previous",
                    "active": false
                },
                {
                    "url": "http://localhost/blog-api/public/api/posts?page=1",
                    "label": "1",
                    "active": true
                },
                {
                    "url": null,
                    "label": "Next &raquo;",
                    "active": false
                }
            ],
            "path": "http://localhost/blog-api/public/api/posts",
            "per_page": 10,
            "to": 2,
            "total": 2
        }
    }
    ```

### 5. Update a Post (Authenticated)

* **URL:** `http://localhost/blog-api/public/api/posts/{post_id}` (e.g., `http://localhost/blog-api/public/api/posts/2`)
* **Method:** `PUT` (or `PATCH`)
* **Headers:**
    * `Content-Type: application/json`
    * `Authorization: Bearer <your_sanctum_token>`
* **Body (JSON - raw):**
    ```json
    {
      "title": "Updated Title for Post 2",
      "body": "This is the updated body content for Post 2."
    }
    ```
* **Success Response:** (Example, updated post details)
    ```json
    {
        "post": {
            "id": 2,
            "title": "Updated Title for Post 2",
            "body": "This is the updated body content for Post 2.",
            "user_id": 1,
            "created_at": "2025-06-13T09:56:08.000000Z",
            "updated_at": "2025-06-18T07:00:00.000000Z"
        }
    }
    ```

### 6. Add a Comment to a Post (Authenticated)

* **URL:** `http://localhost/blog-api/public/api/posts/{post_id}/comments` (e.g., `http://localhost/blog-api/public/api/posts/1/comments`)
* **Method:** `POST`
* **Headers:**
    * `Content-Type: application/json`
    * `Authorization: Bearer <your_sanctum_token>`
* **Body (JSON - raw):**
    ```json
    {
      "body": "This is my first comment by Amir!"
    }
    ```
* **Success Response:**
    ```json
    {
        "comment": {
            "body": "This is my first comment by Amir!",
            "user_id": 1, // The authenticated user's ID
            "post_id": 1, // The post ID the comment was added to
            "updated_at": "2025-06-18T07:05:00.000000Z",
            "created_at": "2025-06-18T07:05:00.000000Z",
            "id": 1
        }
    }
    ```

    **Example `Get All Posts` response with comments populated:**
    ```json
    {
        "data": [
            {
                "id": 1,
                "title": "this is title",
                "body": "this is body of the post",
                "user": { "id": 1, "name": "Amir" },
                "comments": [
                    { "id": 1, "body": "this is my first comment by amir", "user": "Amir" },
                    { "id": 2, "body": "this is another comment by amir", "user": "Amir" },
                    { "id": 3, "body": "This is knowledgable post", "user": "Amir" }
                ],
                "created_at": "2025-06-13 09:49:23"
            },
            {
                "id": 2,
                "title": "Updated Title",
                "body": "Updated body",
                "user": { "id": 1, "name": "Amir" },
                "comments": [],
                "created_at": "2025-06-13 09:56:08"
            }
        ],
        "links": { /* ... */ },
        "meta": { /* ... */ }
    }
    ```

### 7. Logout User (Authenticated)

* **URL:** `http://localhost/blog-api/public/api/logout`
* **Method:** `POST`
* **Headers:**
    * `Accept: application/json`
    * `Authorization: Bearer <your_sanctum_token>`
* **Success Response:**
    ```json
    {
        "message": "Logged out."
    }
    ```
    After logout, the token used will be invalidated, and you will receive an "Unauthenticated." error for subsequent requests requiring authentication with that token.

---

## 🔒 Authentication Reminder

For any endpoint requiring authentication (e.g., creating posts, updating posts, adding comments, logging out), you **must** include the `Authorization` header with a valid Sanctum Bearer Token obtained from the `/register` or `/login` endpoints.

**Example Header:**

```
Authorization: Bearer 1|eB2x9g3Zr...your_sanctum_token...
```

If you try to access an authenticated endpoint without a valid token, you will receive:

```json
{
    "message": "Unauthenticated."
}
```

---

## 🤝 Contributing

Feel free to fork the repository, make improvements, and submit pull requests.

---

## 📜 License

This project is open-source and free to use.

---

## 👤 Author

Amir Saifi
