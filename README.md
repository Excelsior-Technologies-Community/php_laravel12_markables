# php_laravel12_markables

## Project Introduction

php_laravel12_markables is a Laravel 12 demonstration project that showcases how to implement a flexible marking system—such as Likes, Bookmarks, Favorites, and Reactions—using the maize-tech/laravel-markable package.

It demonstrates a modern, scalable approach to track user-based interactions on posts, allowing developers to:

- Apply marks (like, favorite, bookmark, reaction) on posts per user.

- Retrieve and display marked records dynamically.

- Use polymorphic relationships in a clean and reusable way.

- Build an interactive front-end for post engagement.

This project is ideal for learning how to implement user interaction systems and reusable marking functionality in Laravel 12.

---

## Project Overview

The project provides a complete workflow for managing posts and user interactions:

- Post Management: Simple Post model with title and content.

- Marking System: Single marks table to store Likes, Bookmarks, Favorites, and Reactions per user.

- User Interaction: Users can like, favorite, bookmark, or react to posts.

- Frontend UI: Clean and modern Blade views to display posts and mark counts dynamically.

- Authentication: Implemented using Laravel Breeze to ensure only logged-in users can interact.

- Database Structure: Includes posts table and marks table with constraints for unique user-post-type marks.

- Scalable Architecture: Designed to easily add more mark types or integrate with other models in the future.

---

## Requirements

- PHP 8.2+

- Composer

- MySQL

- Node.js (for Breeze authentication)

---

## Step 1: Create Laravel 12 Project

Create Project

```bash
composer create-project laravel/laravel php_laravel12_markables "12.*"
```

Move inside project:

```bash
cd php_laravel12_markables
```

Check version:

```bash
php artisan --version
```

---

## Step 2: Configure Database

Update .env file:

```.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=markable_db
DB_USERNAME=root
DB_PASSWORD=
```

Create database manually:

```bash
CREATE DATABASE markable_db;
```

Or

Run Migration Command:

```bash
php artisan migrate
```

---

## Step 3: Install Laravel Markable

Install package:

```bash
composer require maize-tech/laravel-markable
```

---

## Step 4: Publish Config

```bash
php artisan vendor:publish --tag="markable-config"
```

Edit config/markable.php:

```bash
return [
    'user_model' => App\Models\User::class,
    'table_prefix' => '',
    'allowed_values' => [
        'reaction' => ['like', 'love', 'haha', 'wow', 'sad', 'angry'],
    ],
];
```

---

## Step 5: Publish  Migrations 

```bash
php artisan vendor:publish --tag="markable-migration-like"
php artisan vendor:publish --tag="markable-migration-favorite"
php artisan vendor:publish --tag="markable-migration-bookmark"
php artisan vendor:publish --tag="markable-migration-reaction"
```

Run migrations:

```bash
php artisan migrate
```

---

## Step 6: Install Authentication (Laravel Breeze)

```bash
composer require laravel/breeze --dev
php artisan breeze:install
npm install
npm run build
php artisan migrate
```

Authentication system is ready.

---

## Step 7: Migrations Table

### Post Table

```bash
php artisan make:model Post -m
```
Edit migration:

```bash
database/migrations/xxxx_create_posts_table.php
```

```php
public function up(): void
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('content');
        $table->timestamps();
    });
}
```

### Marks Table

Run a migration:

```bash
php artisan make:migration create_marks_table --create=marks
```

Update it:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // like, favorite, bookmark, love, haha, etc.
            $table->timestamps();

            $table->unique(['user_id','post_id','type']); // one mark per user per type
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
```


Then migrate:

```bash
php artisan migrate
```

---

## Step 8: Models

### Post.php

```bash
app/Models/Post.php
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'body'];

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function marksByType($type)
    {
        return $this->marks()->where('type', $type);
    }

    public function mark($type, $user)
    {
        return $this->marks()->updateOrCreate(
            ['user_id' => $user->id, 'type' => $type],
            ['post_id' => $this->id]
        );
    }
}
```

### Mark.php

Create a Mark model

```bash
php artisan make:model Mark
```

```bash
app/Models/Mark.php
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $fillable = ['user_id', 'post_id', 'type'];
}
```

### User.php (default)

```bash
app/Models/User.php
```

```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Maize\Markable\Markable;    // add this


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Markable;  // add this

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

---

## Step 9: Create Controller

```bash
php artisan make:controller PostController
```

```bash
app/Http/Controllers/PostController.php
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\User;

class PostController extends Controller
{
    public function index()
    {
        return view('posts.index', ['posts' => Post::all()]);
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function bookmark(Post $post)
    {
        $post->mark('bookmark', auth()->user());
        return redirect()->back();
    }

    public function like(Post $post)
    {
        $post->mark('like', auth()->user());
        return redirect()->back();
    }

    public function favorite(Post $post)
    {
        $post->mark('favorite', auth()->user());
        return redirect()->back();
    }

    public function react(Request $request, Post $post)
    {
        $post->mark($request->type, auth()->user());
        return redirect()->back();
    }
}
```

---

## Step 10: Add Routes

```bash
routes/web.php
```

```php
use App\Http\Controllers\PostController;


Route::middleware(['auth'])->group(function () {
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
    Route::post('/posts/{post}/favorite', [PostController::class, 'favorite'])->name('posts.favorite');
    Route::post('/posts/{post}/bookmark', [PostController::class, 'bookmark'])->name('posts.bookmark');
    Route::post('/posts/{post}/react', [PostController::class, 'react'])->name('posts.react');
});
```

---

## Step 11: Create View

Create:

```bash
resources/views/posts/index.blade.php
```

```html
<x-app-layout>

    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('All Posts') }}
        </h2>
    </x-slot>

    <br>

    <div class="py-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 space-y-8">

            @foreach($posts as $post)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg rounded-2xl p-6 transition duration-300 hover:shadow-2xl">

                    <!-- Post Title -->
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $post->title }}
                    </h3>

                    <!-- Post Body -->
                    <p class="mt-3 text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ $post->body }}
                    </p>

                    <!-- Divider -->
                    <div class="mt-5 border-t border-gray-200 dark:border-gray-700"></div>

                    <!-- Action Buttons -->
                    <div class="mt-5 flex flex-wrap gap-4">

                        <!-- Like -->
                        <form action="{{ route('posts.like', $post) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-4 py-2 rounded-full 
                                       bg-blue-600 hover:bg-blue-700 
                                       text-white font-medium transition duration-200">

                                👍 Like
                                <span class="bg-blue-100 text-blue-700 
                                             dark:bg-blue-900 dark:text-blue-300
                                             px-2 py-0.5 rounded-full text-sm font-semibold">
                                    {{ $post->marks->where('type', 'like')->count() }}
                                </span>
                            </button>
                        </form>

                        <!-- Favorite -->
                        <form action="{{ route('posts.favorite', $post) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-4 py-2 rounded-full 
                                       bg-yellow-500 hover:bg-yellow-600 
                                       text-black font-medium transition duration-200">

                                ⭐ Favorite
                                <span class="bg-yellow-100 text-yellow-700 
                                             dark:bg-yellow-900 dark:text-yellow-300
                                             px-2 py-0.5 rounded-full text-sm font-semibold">
                                    {{ $post->marks->where('type', 'favorite')->count() }}
                                </span>
                            </button>
                        </form>

                        <!-- Bookmark -->
                        <form action="{{ route('posts.bookmark', $post) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-4 py-2 rounded-full 
                                       bg-gray-700 hover:bg-gray-800 
                                       text-white font-medium transition duration-200">

                                🔖 Bookmark
                                <span class="bg-gray-200 text-gray-800 
                                             dark:bg-gray-900 dark:text-gray-300
                                             px-2 py-0.5 rounded-full text-sm font-semibold">
                                    {{ $post->marks->where('type', 'bookmark')->count() }}
                                </span>
                            </button>
                        </form>

                        <!-- Reaction Dropdown -->
                        <form action="{{ route('posts.react', $post) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <select name="type"
                                class="px-3 py-2 rounded-lg border 
                                       bg-white dark:bg-gray-700 
                                       text-gray-800 dark:text-gray-200
                                       border-gray-300 dark:border-gray-600
                                       focus:outline-none focus:ring-2 focus:ring-pink-500">

                                @foreach(['like','love','haha','wow','sad','angry'] as $reaction)
                                    <option value="{{ $reaction }}">{{ ucfirst($reaction) }}</option>
                                @endforeach
                            </select>

                            <button type="submit"
                                class="px-4 py-2 rounded-lg 
                                       bg-pink-600 hover:bg-pink-700 
                                       text-white font-medium transition duration-200">
                                React
                            </button>
                        </form>

                    </div>

                    <!-- Summary Counts -->
                    <div class="mt-5 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <p>Total Likes: {{ $post->marks->where('type', 'like')->count() }}</p>
                        <p>Total Favorites: {{ $post->marks->where('type', 'favorite')->count() }}</p>
                        <p>Total Bookmarks: {{ $post->marks->where('type', 'bookmark')->count() }}</p>
                    </div>

                </div>
            @endforeach

        </div>
    </div>

</x-app-layout>
```

---

## Step 12: Testing

Create demo post:

```bash
php artisan tinker
```

```
\App\Models\Post::create([
    'title' => 'First Post',
    'content' => 'This is Laravel 12 Markable demo'
]);
```

Run project:

```bash
php artisan serve
```

Visit:

```bash
http://127.0.0.1:8000/posts
```
---

## Output

<img width="1902" height="1023" alt="Screenshot 2026-02-26 172129" src="https://github.com/user-attachments/assets/e555d3f6-9b39-4919-92b1-e400b8649773" />

---

## Project Structure

```
php_laravel12_markables/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── PostController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Post.php
│   │   └── Mark.php
│
├── database/
│   ├── migrations/
│   │   ├── 2026_02_26_create_posts_table.php
│   │   └── 2026_02_26_create_marks_table.php
│   │       └── // Marks table stores likes, bookmarks, favorites, reactions
│   └── seeders/
│       
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   └── posts/
│   │       └── index.blade.php
│   └── css/
│       └── app.css
│
├── routes/
│   └── web.php
│
├── public/
│   └── assets/          
│
├── .env                  <-- Add database connection & APP_KEY
│
├── composer.json
├── package.json          <-- if using Tailwind or frontend assets
└── tailwind.config.js
```

---

Your php_laravel12_markables Project is now ready!
