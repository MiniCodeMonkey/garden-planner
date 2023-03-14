# Garden Planner

## Stack

* **Backend:** Laravel 9
* **Frontend:** Tailwind CSS 3, Vue 3, Inertia 1.0
* **Infrastructure:** Hosted on AWS Lambda via Laravel Vapor

## Development

### Install dependencies
```
composer install
npm install
```

### Development server
```
# Run database migrations
php artisan migrate

# Start development server and frontend asset watcher
php artisan serve
npm run dev

# Go to http://localhost:8000/register and create a new account

# Seed initial data for user id 1
php artisan db:seed
```
