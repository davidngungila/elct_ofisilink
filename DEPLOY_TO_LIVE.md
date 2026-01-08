# Complete Deployment Commands for cPanel Live Server

## Step 1: Fix Git Diverging Branches (if needed)

```bash
cd /home/your-username/public_html
git fetch origin
git merge --no-ff origin/main -m "Merge remote changes"
```

## Step 2: Run Database Migrations

```bash
# Navigate to your project directory
cd /home/your-username/public_html

# Run migrations
php artisan migrate

# If you get "migrate:fresh" warnings, use force:
php artisan migrate --force
```

## Step 3: Clear All Caches

```bash
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Optimize (optional, for production)
php artisan optimize
```

## Step 4: Set Permissions (if needed)

```bash
# Set storage permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Set ownership (adjust user:group as needed)
chown -R your-username:your-username storage
chown -R your-username:your-username bootstrap/cache
```

## Step 5: Create Storage Link (if needed)

```bash
php artisan storage:link
```

## Complete One-Line Deployment Script

```bash
cd /home/your-username/public_html && git fetch origin && git merge --no-ff origin/main -m "Deploy" && php artisan migrate --force && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan optimize
```

## Troubleshooting

### If migrations fail:
```bash
# Check migration status
php artisan migrate:status

# Rollback last migration (if needed)
php artisan migrate:rollback

# Then run migrations again
php artisan migrate --force
```

### If you get permission errors:
```bash
# Check current user
whoami

# Check file ownership
ls -la storage/
ls -la bootstrap/cache/
```

