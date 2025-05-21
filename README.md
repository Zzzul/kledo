# 🚀 Kledo API Project  
## 🏢 PT. Kledo Berhati Nyaman  
### 🔧 Back End Assessment V  

---

## 📚 Table of Contents
1. [✅ Requirements](#-requirements)  
2. [⚙️ Setup](#️-setup)  
3. [📑 Swagger Docs](#-swagger)  
4. [🧪 Testing](#-testing)  

---

## ✅ Requirements
- 🐘 PHP 8.4  
- 🐬 MySQL  

---

## ⚙️ Setup
1. 🔽 Clone or download this repository:
```bash
git clone https://github.com/Zzzul/kledo.git
```

2. 📁 Move into the project directory:
```shell 
cd kledo
```

3. 📦 Install Laravel dependencies:
```shell
composer install
```

4. 📝 Copy the example environment file:
```shell
cp .env.example .env
```

5. 🔐 Generate the application key:
```shell
php artisan key:generate
```

6. ⚙️ Configure your database settings in ```.env```:
```shell
DB_DATABASE=kledo
DB_USERNAME=root
DB_PASSWORD=
```

7.  🛠️ Run migrations and seed the database:
```shell
php artisan migrate --seed
```
> Seeder only includes status data ✅

8. ▶️ Start the development server:
```shell
php artisan serve
```

## 📑 Swagger
Access the API documentation at: `/api/documentation`

## 🧪 Testing
To run the tests:
```shell
php artisan test
```
