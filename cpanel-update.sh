#!/bin/bash
# Script to safely update from remote repository in cPanel
# This handles diverging branches automatically

set -e  # Exit on error (but we'll handle errors manually)

echo "=========================================="
echo "cPanel Git Update Script"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Fetch latest changes
echo -e "${YELLOW}📥 Fetching latest changes from remote...${NC}"
git fetch origin

# Step 2: Check current status
echo -e "\n${YELLOW}📊 Checking current branch status...${NC}"
git status

# Step 3: Check if branches have diverged
LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/main)
BASE=$(git merge-base HEAD origin/main)

if [ "$LOCAL" = "$REMOTE" ]; then
    echo -e "\n${GREEN}✓ Already up to date with origin/main${NC}"
    exit 0
fi

if [ "$LOCAL" = "$BASE" ]; then
    echo -e "\n${GREEN}✓ Local branch is behind, fast-forwarding...${NC}"
    git merge --ff-only origin/main
    echo -e "${GREEN}✓ Update completed successfully!${NC}"
    exit 0
fi

if [ "$REMOTE" = "$BASE" ]; then
    echo -e "\n${YELLOW}⚠️  Local branch is ahead of remote${NC}"
    echo -e "${YELLOW}   You may want to push your local changes first${NC}"
    read -p "Continue with update anyway? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${RED}Update cancelled${NC}"
        exit 1
    fi
fi

# Branches have diverged - try different strategies
echo -e "\n${YELLOW}⚠️  Branches have diverged. Attempting to merge...${NC}"

# Strategy 1: Try merge with no-ff
if git pull --no-ff origin main 2>/dev/null; then
    echo -e "${GREEN}✓ Successfully merged changes${NC}"
    echo -e "${GREEN}✓ Update completed successfully!${NC}"
    exit 0
fi

# Strategy 2: Try rebase (cleaner history)
echo -e "\n${YELLOW}🔄 Merge failed, trying rebase...${NC}"
if git pull --rebase origin main 2>/dev/null; then
    echo -e "${GREEN}✓ Successfully rebased changes${NC}"
    echo -e "${GREEN}✓ Update completed successfully!${NC}"
    exit 0
fi

# Strategy 3: Show what's different and provide options
echo -e "\n${RED}❌ Automatic merge/rebase failed. Manual intervention required.${NC}"
echo ""
echo "=========================================="
echo "Diverging Branches Detected"
echo "=========================================="
echo ""
echo "Commits on remote (origin/main) not in local:"
git log HEAD..origin/main --oneline || true
echo ""
echo "Commits in local not on remote:"
git log origin/main..HEAD --oneline || true
echo ""
echo "=========================================="
echo "Resolution Options:"
echo "=========================================="
echo ""
echo "Option 1: Rebase (recommended for cleaner history)"
echo "  git pull --rebase origin main"
echo ""
echo "Option 2: Merge (creates merge commit)"
echo "  git pull --no-ff origin main"
echo ""
echo "Option 3: Reset to match remote (WARNING: Discards local changes)"
echo "  git reset --hard origin/main"
echo ""
echo "Option 4: Create a backup branch first, then reset"
echo "  git branch backup-$(date +%Y%m%d-%H%M%S)"
echo "  git reset --hard origin/main"
echo ""
exit 1

