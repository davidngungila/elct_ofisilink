#!/bin/bash
# Fix git merge conflict on server
# Run this script on your cPanel server

cd /path/to/your/project || exit 1

# Check git status
echo "Checking git status..."
git status

# Stash any local changes
echo "Stashing local changes..."
git stash

# Pull latest changes
echo "Pulling latest changes..."
git pull origin main

# If there were stashed changes, try to apply them
if [ -n "$(git stash list)" ]; then
    echo "Attempting to apply stashed changes..."
    git stash pop || echo "Warning: Could not apply stashed changes automatically. Please review manually."
fi

echo "Deployment fix completed!"

