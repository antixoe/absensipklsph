# Quick Start Guide

## Running the Application

### 1. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Setup Database
```bash
# Create .env file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations and seed database
php artisan migrate --seed
```

### 3. Start Development Servers
Open two terminals:

**Terminal 1 - Backend (Laravel)**
```bash
php artisan serve
```
Server will run on `http://localhost:8000`

**Terminal 2 - Frontend (Vue.js)**
```bash
npm run dev
```
Frontend will run on `http://localhost:5173`

### 4. Access the Application
Open your browser and go to: `http://localhost:5173`

## Default Test Accounts

After running migrations and seeders, the following roles are available:

### Student Account
- Email: (Register as student through the app)
- Role: Student

### Instructor Account  
- Email: (Register as instructor through the app)
- Role: Instructor

## First Steps

1. **Register as Student**
   - Go to Registration page
   - Select "Student" role
   - Fill in student details (NIM, School, Major)
   - Create account

2. **Dashboard**
   - View quick stats
   - Access attendance, logbook, activities, documents

3. **Check In**
   - Click "Check In" on Attendance page
   - Allow location access
   - Take a photo
   - Submit

4. **Create Logbook Entry**
   - Go to Logbook page
   - Click "+ New Entry"
   - Fill in daily activities and learning
   - Save as draft or submit for review

5. **Upload Documents**
   - Go to Documents page
   - Upload internship-related documents
   - Wait for instructor approval

## Common Commands

```bash
# Database commands
php artisan migrate              # Run migrations
php artisan migrate:rollback     # Rollback last migration
php artisan db:seed              # Run seeders
php artisan tinker              # Laravel CLI

# Cache management
php artisan cache:clear
php artisan view:clear

# Build frontend
npm run build                    # Production build
npm run dev                      # Development build with hot reload
```

## API Testing

Use Postman or similar tool to test API:

1. **Login**
   ```
   POST http://localhost:8000/api/v1/auth/login
   {
     "email": "student@example.com",
     "password": "password123"
   }
   ```

2. **Get Current User**
   ```
   GET http://localhost:8000/api/v1/auth/me
   Headers: Authorization: Bearer {token}
   ```

3. **Check In**
   ```
   POST http://localhost:8000/api/v1/attendance/check-in
   Headers: Authorization: Bearer {token}
   {
     "latitude": -6.2088,
     "longitude": 106.8456
   }
   ```

## Project Structure Quick Reference

- **Backend Logic**: `app/Http/Controllers/Api/`
- **Database Models**: `app/Models/`
- **Database Schemas**: `database/migrations/`
- **Frontend Pages**: `resources/js/views/`
- **State Management**: `resources/js/stores/`
- **Routes (API)**: `routes/api.php`

## Environment Variables (.env)

Key variables to configure:

```
APP_NAME=AbsensiPKL
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_pkl
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
```

## Troubleshooting

### "npm: command not found"
- Install Node.js from nodejs.org
- Restart terminal

### Database connection error
- Check DB credentials in .env
- Ensure MySQL is running
- Run `php artisan migrate`

### Port already in use
- Change port: `php artisan serve --port=8001`
- Change frontend: `npm run dev -- --port 5174`

### CORS errors
- Make sure backend is running on port 8000
- Check `config/cors.php` settings

## Next Steps

1. Customize email notifications
2. Set up QR code scanning
3. Add SMS notifications
4. Create mobile app
5. Set up automated reports
6. Configure backup system
7. Deploy to production

## Support Files

- **README_APP.md** - Full application documentation
- **Database Diagram** - See migrations for schema
- **.env.example** - Environment variables template

For more details, see [README_APP.md](README_APP.md)
