# Lab Railway

A Railway project to control professional profiles and tutorial videos. Built with Laravel backend, Vue.js frontend, and MySQL database.

## Features

### Backend (Laravel)
- **Professional Profiles Management**
  - Create, read, update, and delete professional profiles
  - Store personal information: name, email, profession, bio
  - Social links: LinkedIn, GitHub, website
  - Profile images

- **Tutorial Videos Management**
  - Create, read, update, and delete tutorial videos
  - Video metadata: title, description, duration, category
  - Video URLs and thumbnail images
  - Featured video system
  - Associate videos with profiles

- **RESTful API**
  - `/api/profiles` - Profile management endpoints
  - `/api/videos` - Video management endpoints
  - `/api/videos/featured` - Get featured videos
  - `/api/profiles/{id}/videos` - Get videos by profile
  - `/api/health` - Health check endpoint

### Frontend (Vue.js)
- **Home Page**
  - Featured videos showcase
  - Statistics overview
  - Navigation to profiles and videos

- **Profiles Page**
  - List all professional profiles
  - Create and edit profiles
  - View profile details with associated videos
  - Responsive grid layout

- **Videos Page**
  - List all tutorial videos
  - Filter by category and featured status
  - Sort by date, title, or duration
  - Create and edit videos
  - Watch videos externally

- **Responsive Design**
  - Mobile-friendly interface
  - Modern UI components
  - Interactive cards and modals

## Technology Stack

- **Backend**: Laravel 10+ (PHP 8.0+)
- **Frontend**: Vue.js 3 with Vue Router
- **Database**: MySQL
- **Build Tool**: Vite
- **Deployment**: Railway
- **Styling**: CSS3 with Flexbox/Grid

## Database Schema

### Profiles Table
- `id` (Primary Key)
- `name` (String)
- `email` (String, Unique)
- `profession` (String)
- `bio` (Text, Optional)
- `image_url` (String, Optional)
- `linkedin_url` (String, Optional)
- `github_url` (String, Optional)
- `website_url` (String, Optional)
- `created_at` / `updated_at` (Timestamps)

### Videos Table
- `id` (Primary Key)
- `profile_id` (Foreign Key to Profiles)
- `title` (String)
- `description` (Text)
- `video_url` (String)
- `thumbnail_url` (String, Optional)
- `duration` (Integer, in seconds)
- `category` (String, Optional)
- `is_featured` (Boolean)
- `created_at` / `updated_at` (Timestamps)

## Installation & Setup

### Prerequisites
- PHP 8.0 or higher
- Composer
- Node.js 16+ and npm
- MySQL database

### Development Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd lab_railway
   ```

2. **Backend Setup**
   ```bash
   # Install PHP dependencies
   composer install
   
   # Copy environment file
   cp .env.example .env
   
   # Configure your database in .env file
   # Set your MySQL connection details
   
   # Generate application key
   php artisan key:generate
   
   # Run migrations
   php artisan migrate
   ```

3. **Frontend Setup**
   ```bash
   # Install Node.js dependencies
   npm install
   
   # Start frontend development server
   npm run dev
   ```

4. **Start the application**
   ```bash
   # In one terminal, start Laravel server
   php artisan serve
   
   # In another terminal, start Vite dev server
   npm run dev
   ```

5. **Access the application**
   - Frontend: http://localhost:3000
   - API: http://localhost:8000/api

### Production Build

```bash
# Build frontend for production
npm run build

# The frontend will be built to public/dist
```

## Railway Deployment

This project is configured for deployment on Railway platform with:

- **railway.toml** configuration file
- Automatic database migrations
- Environment variables support
- Built-in MySQL service integration

### Environment Variables

Set these variables in your Railway project:

```
APP_NAME=Lab Railway
APP_ENV=production
APP_KEY=<generate-a-key>
APP_DEBUG=false
APP_URL=<your-railway-url>

# Railway automatically provides MySQL variables:
# MYSQLHOST, MYSQLPORT, MYSQLDATABASE, MYSQLUSER, MYSQLPASSWORD
```

## API Documentation

### Profiles Endpoints
- `GET /api/profiles` - Get all profiles
- `POST /api/profiles` - Create new profile
- `GET /api/profiles/{id}` - Get specific profile
- `PUT /api/profiles/{id}` - Update profile
- `DELETE /api/profiles/{id}` - Delete profile

### Videos Endpoints
- `GET /api/videos` - Get all videos
- `POST /api/videos` - Create new video
- `GET /api/videos/{id}` - Get specific video
- `PUT /api/videos/{id}` - Update video
- `DELETE /api/videos/{id}` - Delete video
- `GET /api/videos/featured` - Get featured videos
- `GET /api/profiles/{id}/videos` - Get videos by profile

### Health Check
- `GET /api/health` - API health status

## Project Structure

```
lab_railway/
├── app/                    # Laravel application files
│   ├── Http/Controllers/   # API controllers
│   ├── Models/            # Eloquent models
│   └── Providers/         # Service providers
├── config/                # Laravel configuration
├── database/              # Database migrations
│   └── migrations/        # Migration files
├── frontend/              # Vue.js frontend
│   ├── src/
│   │   ├── components/    # Vue components
│   │   ├── views/         # Page components
│   │   ├── services/      # API services
│   │   └── main.js        # Entry point
│   └── index.html         # HTML template
├── public/                # Public assets
├── routes/                # Laravel routes
│   ├── api.php           # API routes
│   └── web.php           # Web routes
├── railway.toml           # Railway deployment config
├── composer.json          # PHP dependencies
├── package.json           # Node.js dependencies
└── vite.config.js         # Vite build config
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the MIT license.
