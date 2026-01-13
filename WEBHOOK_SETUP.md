# GitHub Webhook Setup for Automatic Deployment

This guide explains how to set up automatic deployment when code is pushed to GitHub.

## Features

- ✅ Automatic deployment on `git push`
- ✅ Secure webhook signature verification
- ✅ Handles diverging branches automatically
- ✅ Background deployment (non-blocking)
- ✅ Deployment logs tracking
- ✅ Only deploys from `main` branch

## Setup Instructions

### 1. Generate Webhook Secret

Generate a secure random string for webhook verification:

```bash
# Generate a random secret (32 characters)
openssl rand -hex 16
```

Or use an online generator: https://www.random.org/strings/

### 2. Add Secret to .env File

Add the secret to your `.env` file on the server:

```env
GITHUB_WEBHOOK_SECRET=your_generated_secret_here
```

### 3. Configure GitHub Webhook

1. Go to your GitHub repository
2. Navigate to **Settings** > **Webhooks** > **Add webhook**
3. Configure the webhook:
   - **Payload URL**: `https://yourdomain.com/webhook/github`
   - **Content type**: `application/json`
   - **Secret**: (paste the secret from step 1)
   - **Events**: Select "Just the push event"
   - **Active**: ✓ (checked)

4. Click **Add webhook**

### 4. Make Deployment Script Executable

On your server (cPanel Terminal or SSH):

```bash
cd /path/to/your/project
chmod +x webhook-deploy.sh
```

### 5. Test the Webhook

1. Make a small change and push to GitHub:
   ```bash
   git commit --allow-empty -m "Test webhook deployment"
   git push origin main
   ```

2. Check the deployment status:
   - Visit: `https://yourdomain.com/webhook/status`
   - Or check logs: `storage/logs/webhook-deploy.log`

## How It Works

1. **Push to GitHub**: When you push code to the `main` branch
2. **GitHub sends webhook**: POST request to `/webhook/github`
3. **Verification**: Server verifies the webhook signature
4. **Deployment**: Executes `webhook-deploy.sh` in background
5. **Updates**: Pulls code, updates dependencies, clears caches

## Deployment Process

The webhook automatically:
- ✅ Fetches latest code from GitHub
- ✅ Pulls changes (handles diverging branches)
- ✅ Updates Composer dependencies
- ✅ Clears Laravel caches
- ✅ Rebuilds optimized caches
- ✅ Sets proper file permissions

**Note**: Database migrations are NOT run automatically for safety. Uncomment in `webhook-deploy.sh` if needed.

## Security

- **Signature Verification**: Webhook secret prevents unauthorized deployments
- **Branch Filtering**: Only deploys from `main` branch
- **Event Filtering**: Only responds to `push` events
- **IP Logging**: All webhook attempts are logged

## Troubleshooting

### Webhook Not Working?

1. **Check webhook status in GitHub**:
   - Go to Settings > Webhooks
   - Click on your webhook
   - Check "Recent Deliveries" for errors

2. **Check server logs**:
   ```bash
   tail -f storage/logs/laravel.log
   tail -f storage/logs/webhook-deploy.log
   ```

3. **Verify secret matches**:
   - GitHub webhook secret must match `.env` `GITHUB_WEBHOOK_SECRET`

4. **Check file permissions**:
   ```bash
   chmod +x webhook-deploy.sh
   chmod -R 755 storage bootstrap/cache
   ```

5. **Test manually**:
   ```bash
   ./webhook-deploy.sh
   ```

### Deployment Fails?

- Check if `git pull` has conflicts (resolve manually)
- Verify Composer is installed and accessible
- Check PHP version and Laravel requirements
- Review `storage/logs/webhook-deploy.log` for errors

### cPanel Git Version Control Error: "Diverging branches can't be fast-forwarded"

This error occurs when the server's local branch and the remote branch have diverged. Here's how to fix it:

#### Option 1: Use the Updated Script (Recommended)

1. **Via cPanel Terminal/SSH:**
   ```bash
   cd /path/to/your/project
   chmod +x cpanel-update.sh
   ./cpanel-update.sh
   ```
   
   The script will automatically:
   - Try to merge changes
   - If merge fails, try rebase
   - Show you what's different if both fail

#### Option 2: Manual Resolution via SSH/Terminal

1. **SSH into your server** or use cPanel Terminal

2. **Navigate to your project:**
   ```bash
   cd /path/to/your/project
   ```

3. **Fetch latest changes:**
   ```bash
   git fetch origin
   ```

4. **Check what's different:**
   ```bash
   # See commits on remote not in local
   git log HEAD..origin/main --oneline
   
   # See commits in local not on remote
   git log origin/main..HEAD --oneline
   ```

5. **Choose a resolution method:**

   **A. Rebase (cleaner history, recommended):**
   ```bash
   git pull --rebase origin main
   ```

   **B. Merge (creates merge commit):**
   ```bash
   git pull --no-ff origin main
   ```

   **C. Reset to match remote (WARNING: Discards local changes):**
   ```bash
   # Create backup first
   git branch backup-$(date +%Y%m%d-%H%M%S)
   
   # Reset to match remote
   git reset --hard origin/main
   ```

#### Option 3: Use Reset Script (Discards Local Changes)

If you want to match the remote exactly and don't need local changes:

```bash
cd /path/to/your/project
chmod +x cpanel-update-reset.sh
./cpanel-update-reset.sh
```

#### Option 4: Fix via cPanel Git Version Control

1. Go to **cPanel > Git Version Control**
2. Click on your repository
3. If you see the error, click **"Pull or Deploy"**
4. In the command box, use one of these:

   **For rebase:**
   ```bash
   git fetch origin && git pull --rebase origin main
   ```

   **For merge:**
   ```bash
   git fetch origin && git pull --no-ff origin main
   ```

   **To reset (discards local changes):**
   ```bash
   git fetch origin && git reset --hard origin/main
   ```

#### Preventing Future Divergence

To prevent this issue:
- Always pull before making changes on the server
- Use the webhook deployment system instead of manual updates
- Avoid making commits directly on the server

## Manual Deployment

If webhook fails, you can deploy manually:

```bash
cd /path/to/your/project
./webhook-deploy.sh
```

Or use the existing script:

```bash
./deploy-update.sh
```

## Disable Auto-Deployment

To temporarily disable automatic deployment:

1. Go to GitHub > Settings > Webhooks
2. Uncheck "Active" for your webhook
3. Or remove `GITHUB_WEBHOOK_SECRET` from `.env`

## Advanced Configuration

### Run Migrations Automatically

Edit `webhook-deploy.sh` and uncomment:

```bash
# Step 6: Run migrations
php artisan migrate --force
```

### Restart Queue Workers

Edit `webhook-deploy.sh` and uncomment:

```bash
# Step 8: Restart queue workers
php artisan queue:restart
```

## Support

For issues or questions, check:
- Deployment logs: `storage/logs/webhook-deploy.log`
- Laravel logs: `storage/logs/laravel.log`
- GitHub webhook delivery logs in repository settings

