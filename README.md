# SunuNews - Senegalese News Application

A bilingual (French/English) news web application built with Laravel 11 and Next.js-style Vite + TailwindCSS. It scans the web for Senegalese news, summarizes stories, and allows editors to create local articles.

## Features

- **Bilingual Support**: Full French/English localization with language switcher
- **News Scanning**: Scheduled job that scans web sources and imports articles with AI summarization
- **Three-Column Layout**: Professional news portal layout with featured stories
- **Article Management**: CRUD operations with role-based access control
- **Categories**: POLITIQUE, ÉCONOMIE, SOCIÉTÉ, SPORT, AFRIQUE, MONDE, PEOPLE
- **Ad Management**: Header, sidebar, inline, and footer ad zones
- **Search**: Full-text search across titles, summaries, and content
- **Admin Dashboard**: Overview cards, user management, article management
- **Responsive Design**: Mobile-friendly with modern TailwindCSS styling

## Technology Stack

- **Framework**: Laravel 11
- **Frontend**: Blade templates + Vite + TailwindCSS v4
- **Database**: MySQL 8.4
- **Language**: PHP 8.3
- **JavaScript**: Vite + Alpine.js ready

## Installation

### Prerequisites
- WampServer installed (PHP 8.3, MySQL 8.4)
- Node.js v24+
- Composer

### Quick Start

1. **Start the server**:
   ```bash
   cd C:\sununews
   php artisan serve --port=3002
   ```

2. **Open your browser**: http://localhost:3002

3. **Run migrations** (if needed):
   ```bash
   php artisan migrate --force
   ```

4. **Seed the database**:
   ```bash
   php artisan db:seed --force
   ```

### Scheduled Jobs

To run the daily news scanner:
```bash
php artisan schedule:run
```

Or use the queue worker:
```bash
php artisan queue:work
```

## Admin Credentials

- **Email**: admin@admin.com
- **Password**: password123
- **Role**: Administrator

Access admin at: http://localhost:3002/admin

## Roles

| Role | Permissions |
|------|-------------|
| Administrator | Full access (users, articles, categories, ads, settings) |
| Editor | Create/edit articles, edit categories |
| Viewer | Read articles, search, switch language |

## Project Structure

```
C:\sununews\
├── app\
│   ├── Http\
│   │   ├── Controllers\
│   │   │   ├── PublicController.php      # Frontend controllers
│   │   │   ├── Admin\                    # Admin controllers
│   │   │   │   ├── AdminController.php
│   │   │   │   ├── ArticleController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── AdController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── SettingController.php
│   │   │   │   └── SearchController.php
│   │   │   └── Auth\                     # Authentication controllers
│   │   ├── Middleware\
│   │   │   └── RoleMiddleware.php
│   │   └── Requests\                     # Form requests
│   ├── Models\                           # Eloquent models
│   ├── Providers\                        # Service providers
│   ├── Services\                         # Business services
│   │   ├── SummarizerService.php
│   │   └── WebScannerService.php
│   └── Console\Commands\
│       └── DailyNewsScan.php             # Scheduled command
├── database\
│   ├── migrations\                       # Database migrations
│   └── seeders\                          # Database seeders
├── resources\
│   ├── views\                            # Blade templates
│   │   ├── layouts\                      # Layout files
│   │   ├── auth\                         # Auth views
│   │   ├── admin\                        # Admin views
│   │   ├── articles\                     # Article views
│   │   └── search\                       # Search views
│   ├── css\app.css                       # TailwindCSS styles
│   └── js\app.js                         # JavaScript
├── routes\
│   ├── web.php                           # Public & admin routes
│   └── console.php                       # Console routes
├── lang\
│   ├── fr\                               # French translations
│   └── en\                               # English translations
├── storage\
│   └── logs\                             # Application logs
├── public\
└── .env                                  # Environment configuration
```

## Configuration

The `.env` file is configured for MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=sununews
DB_USERNAME=root
DB_PASSWORD=
APP_URL=http://localhost:3002
APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
```

## Routes

### Public Routes
- `/` - Home page with featured stories and three-column layout
- `/articles/{article}` - Article detail page
- `/category/{category}` - Category page
- `/search` - Search results
- `/lang/{locale}` - Language switcher (fr/en)
- `/login` - Login page
- `/register` - Registration page

### Admin Routes (requires admin role)
- `/admin` - Dashboard overview
- `/admin/articles` - Article management
- `/admin/categories` - Category management
- `/admin/ads` - Ad management
- `/admin/users` - User management
- `/admin/settings` - Site settings

## Running the Scheduler

The application uses Laravel Scheduler for daily news scanning. To run:

```bash
# Run the scheduler
php artisan schedule:run

# Or add to crontab (Linux/Mac)
* * * * * cd /path/to/sununews && php artisan schedule:run >> /dev/null 2>&1
```

## License

MIT License

## Author

SunuNews Team
