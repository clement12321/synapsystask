# Synapsys Task Project
# This is a Laravel + Vue Project with MySQL database

## Requirements
- PHP >= 8.x
- MySQL
- Composer
- Node.js & npm
- XAMPP

## Creating database
1. Start Apache and MySQL in XAMPP Control Panel
2. click admin to go to the admin page to create your database

## Installation
1. Clone the repository
   - git clone https://github.com/yourusername/synapsystask.git
2. Navigate to project folder
   - cd synapsystask
3. Install PHP dependencies
   - composer install
4. Install Node dependencies
   - npm install
5. Copy `.env.example` to `.env` and set your database credentials
   - cp .env.example .env
7. Generate app key:
   - php artisan key:generate
8. Run migrations and seed test data (seeding is optional)
   - php artisan migrate --seed
   - php artisan migrate (without seed)
9. Build front-end
    - npm run dev
10. Start the Laravel server (you will need to open another terminal for this if you have already npm run dev)
    - php artisan serve
11. Open browser at `http://127.0.0.1:8000`
