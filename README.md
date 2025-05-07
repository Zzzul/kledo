# Kledo
## PT. Kledo Berhati Nyaman
### Back End Assessment V

## Table of Contents
1. [Requirements](#requirements)
2. [Setup](#setup)
3. [Swagger](#swagger)
4. [Testing](#testing)

## Requirements
- PHP 8.4
- MySQL

## Setup
1. Clone or download
```bash
git clone https://github.com/Zzzul/kledo.git
```

2. CD into `/kledo`
```shell 
cd kledo
```

3. Install Laravel dependency
```shell
composer install
```

4. Create copy of ```.env```
```shell
cp .env.example .env
```

5. Generate laravel key
```shell
php artisan key:generate
```

6. Set database name and account in ```.env```
```shell
DB_DATABASE=kledo
DB_USERNAME=root
DB_PASSWORD=
```

7.  Run Laravel migrate and seeder
```shell
php artisan migrate --seed
```
> Seeder only for status

8. Start development server
```shell
php artisan serve
```

## Swagger
Go to: `/api/documentation`

## Testing
```shell
php artisan test
```
