# Velora Backend Deployment Guide

## System Requirements

- PHP 8.1 or higher
- MySQL 8.0 or higher
- Composer
- Web server (Apache/Nginx)

## Local Development Setup

### 1. Clone and Install Dependencies
```bash
git clone <repository-url>
cd velora-backend
composer install
```

### 2. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration
Update `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=velora_db
DB_USERNAME=velora_user
DB_PASSWORD=velora_password
```

### 4. Create Database and User
```sql
CREATE DATABASE velora_db;
CREATE USER 'velora_user'@'localhost' IDENTIFIED BY 'velora_password';
GRANT ALL PRIVILEGES ON velora_db.* TO 'velora_user'@'localhost';
FLUSH PRIVILEGES;
```

### 5. Run Migrations and Seeders
```bash
php artisan migrate
php artisan db:seed --class=SiteSeeder
```

### 6. Start Development Server
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Production Deployment

### 1. Server Setup (Ubuntu/Debian)
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install -y php php-cli php-fpm php-json php-common php-mysql php-zip php-gd php-mbstring php-curl php-xml php-pear php-bcmath

# Install MySQL
sudo apt install -y mysql-server mysql-client

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install -y nginx
```

### 2. Application Deployment
```bash
# Clone repository
cd /var/www
sudo git clone <repository-url> velora-backend
cd velora-backend

# Install dependencies
sudo composer install --optimize-autoloader --no-dev

# Set permissions
sudo chown -R www-data:www-data /var/www/velora-backend
sudo chmod -R 755 /var/www/velora-backend
sudo chmod -R 775 /var/www/velora-backend/storage
sudo chmod -R 775 /var/www/velora-backend/bootstrap/cache
```

### 3. Environment Configuration
```bash
# Copy environment file
sudo cp .env.example .env

# Generate application key
sudo php artisan key:generate

# Configure database in .env
sudo nano .env
```

### 4. Database Setup
```bash
# Run migrations
sudo php artisan migrate --force

# Seed database
sudo php artisan db:seed --class=SiteSeeder --force

# Optimize application
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

### 5. Nginx Configuration
Create `/etc/nginx/sites-available/velora-backend`:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/velora-backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/velora-backend /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 6. SSL Configuration (Optional)
```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d your-domain.com
```

## API Endpoints

### Base URL
- Development: `http://localhost:8000/api`
- Production: `https://your-domain.com/api`

### Admin Panel
- Development: `http://localhost:8000/admin`
- Production: `https://your-domain.com/admin`

## Security Considerations

### 1. Environment Variables
Ensure sensitive information is properly configured in `.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-generated-key
DB_PASSWORD=strong-password
```

### 2. File Permissions
```bash
sudo chmod 644 .env
sudo chmod -R 755 /var/www/velora-backend
sudo chmod -R 775 storage bootstrap/cache
```

### 3. Firewall Configuration
```bash
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

## Monitoring and Maintenance

### 1. Log Files
- Laravel logs: `storage/logs/laravel.log`
- Nginx logs: `/var/log/nginx/access.log` and `/var/log/nginx/error.log`

### 2. Database Backup
```bash
# Create backup
mysqldump -u velora_user -p velora_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore backup
mysql -u velora_user -p velora_db < backup_file.sql
```

### 3. Application Updates
```bash
cd /var/www/velora-backend
sudo git pull origin main
sudo composer install --optimize-autoloader --no-dev
sudo php artisan migrate --force
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R www-data:www-data /var/www/velora-backend
   sudo chmod -R 775 storage bootstrap/cache
   ```

2. **Database Connection Issues**
   - Check MySQL service: `sudo systemctl status mysql`
   - Verify credentials in `.env`
   - Test connection: `php artisan tinker` then `DB::connection()->getPdo()`

3. **500 Internal Server Error**
   - Check Laravel logs: `tail -f storage/logs/laravel.log`
   - Check Nginx logs: `sudo tail -f /var/log/nginx/error.log`
   - Ensure proper file permissions

4. **CORS Issues**
   - CORS is already configured in `config/cors.php`
   - Verify `paths` and `allowed_origins` settings

## Performance Optimization

### 1. Caching
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Database Optimization
- Add indexes for frequently queried columns
- Use database connection pooling
- Consider read replicas for high traffic

### 3. Server Optimization
- Enable Gzip compression in Nginx
- Use Redis for session storage
- Implement CDN for static assets

## Support

For technical support or questions:
- Check Laravel documentation: https://laravel.com/docs
- Review API documentation: `API_DOCUMENTATION.md`
- Check application logs for error details

