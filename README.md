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

## Ongoing maintenance

### Reset planting season
```sql
UPDATE seeds SET seeding_start=DATE_ADD(seeding_start, INTERVAL 1 YEAR), seeding_end=DATE_ADD(seeding_end, INTERVAL 1 YEAR), planting_start=DATE_ADD(planting_start, INTERVAL 1 YEAR), planting_end=DATE_ADD(planting_end, INTERVAL 1 YEAR), harvest_start=DATE_ADD(harvest_start, INTERVAL 1 YEAR), harvest_end=DATE_ADD(harvest_end, INTERVAL 1 YEAR);
```