#!/bin/bash
# Script to safely update from remote repository in cPanel
# This handles diverging branches

echo "Fetching latest changes from remote..."
git fetch origin

echo "Checking current branch status..."
git status

echo "Attempting to pull with merge (no fast-forward)..."
git pull --no-ff origin main

# If merge fails, show options
if [ $? -ne 0 ]; then
    echo ""
    echo "Merge failed. You have two options:"
    echo ""
    echo "Option 1: Rebase (cleaner history)"
    echo "  git pull --rebase origin main"
    echo ""
    echo "Option 2: Reset to match remote (WARNING: This will discard local changes)"
    echo "  git reset --hard origin/main"
    echo ""
    echo "Option 3: See what's different"
    echo "  git log HEAD..origin/main  # See commits on remote not in local"
    echo "  git log origin/main..HEAD  # See commits in local not on remote"
    exit 1
fi

echo ""
echo "Update completed successfully!"
echo "If there were merge conflicts, please resolve them manually."

