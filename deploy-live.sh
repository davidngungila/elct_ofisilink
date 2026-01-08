#!/bin/bash
# Complete deployment script for cPanel live server
# Run this script on your live server via SSH or cPanel Terminal

echo "=== OfisiLink Live Server Deployment ==="
echo ""

# Get current directory
CURRENT_DIR=$(pwd)
echo "Current directory: $CURRENT_DIR"
echo ""

# Check if we're in a Laravel project
if [ ! -f artisan ]; then
    echo "ERROR: Not a Laravel project. Please navigate to your project root directory."
    exit 1
fi

# Step 1: Git Operations
echo "=== Step 1: Updating from Git ==="
echo ""

# Check git status
if [ -d .git ]; then
    echo "1.1 Fetching latest changes..."
    git fetch origin
    
    # Check if there are uncommitted changes
    if [ -n "$(git status -s)" ]; then
        echo "1.2 Stashing uncommitted changes..."
        git stash save "Auto-stash before deployment - $(date '+%Y-%m-%d %H:%M:%S')"
    fi
    
    # Check if branches have diverged
    LOCAL=$(git rev-parse HEAD 2>/dev/null)
    REMOTE=$(git rev-parse origin/main 2>/dev/null)
    
    if [ "$LOCAL" != "$REMOTE" ] && [ -n "$LOCAL" ] && [ -n "$REMOTE" ]; then
        echo "1.3 Merging remote changes..."
        git merge --no-ff origin/main -m "Deploy - $(date '+%Y-%m-%d %H:%M:%S')" || {
            echo "ERROR: Merge failed. Please resolve conflicts manually."
            exit 1
        }
    else
        echo "1.3 Already up to date or no remote branch found."
    fi
    
    echo "✅ Git update completed"
    echo ""
else
    echo "⚠️  Not a git repository, skipping git operations."
    echo ""
fi

# Step 2: Database Migrations
echo "=== Step 2: Running Database Migrations ==="
echo ""

echo "2.1 Running migrations..."
php artisan migrate --force

if [ $? -eq 0 ]; then
    echo "✅ Migrations completed successfully"
else
    echo "❌ Migration failed. Please check the error above."
    exit 1
fi
echo ""

# Step 3: Clear Caches
echo "=== Step 3: Clearing Caches ==="
echo ""

echo "3.1 Clearing application cache..."
php artisan cache:clear

echo "3.2 Clearing config cache..."
php artisan config:clear

echo "3.3 Clearing route cache..."
php artisan route:clear

echo "3.4 Clearing view cache..."
php artisan view:clear

echo "✅ Caches cleared"
echo ""

# Step 4: Optimize (Production)
echo "=== Step 4: Optimizing Application ==="
echo ""

echo "4.1 Optimizing for production..."
php artisan optimize

echo "✅ Optimization completed"
echo ""

# Step 5: Storage Link
echo "=== Step 5: Creating Storage Link ==="
echo ""

if [ ! -L public/storage ]; then
    echo "5.1 Creating storage symlink..."
    php artisan storage:link
    echo "✅ Storage link created"
else
    echo "5.1 Storage link already exists"
fi
echo ""

# Step 6: Permissions (if running as root/sudo)
echo "=== Step 6: Setting Permissions ==="
echo ""

if [ -w storage ] && [ -w bootstrap/cache ]; then
    echo "6.1 Setting storage permissions..."
    chmod -R 775 storage 2>/dev/null || echo "⚠️  Could not set storage permissions (may need sudo)"
    chmod -R 775 bootstrap/cache 2>/dev/null || echo "⚠️  Could not set cache permissions (may need sudo)"
    echo "✅ Permissions set"
else
    echo "⚠️  Cannot set permissions (not running as owner or root)"
fi
echo ""

# Step 7: Apply stashed changes if any
if [ -d .git ] && [ -n "$(git stash list)" ]; then
    echo "=== Step 7: Applying Stashed Changes ==="
    echo ""
    git stash pop || echo "⚠️  Could not apply stashed changes automatically"
    echo ""
fi

# Final Status
echo "=== Deployment Summary ==="
echo ""
echo "✅ Git: Updated"
echo "✅ Database: Migrations run"
echo "✅ Cache: Cleared"
echo "✅ Optimization: Completed"
echo ""
echo "=== Deployment completed successfully! ==="
echo ""
echo "If you encounter any issues, check:"
echo "  - Laravel logs: storage/logs/laravel.log"
echo "  - PHP error logs"
echo "  - Web server error logs"
echo ""

