<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class WebhookController extends Controller
{
    /**
     * Handle GitHub webhook for automatic deployment
     * 
     * Configure in GitHub: Settings > Webhooks > Add webhook
     * Payload URL: https://yourdomain.com/webhook/github
     * Content type: application/json
     * Secret: (set in .env as GITHUB_WEBHOOK_SECRET)
     * Events: Just the push event
     */
    public function github(Request $request)
    {
        // Verify webhook secret
        $secret = env('GITHUB_WEBHOOK_SECRET');
        if ($secret) {
            $signature = $request->header('X-Hub-Signature-256');
            $payload = $request->getContent();
            $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
            
            if (!hash_equals($expectedSignature, $signature)) {
                Log::warning('GitHub webhook: Invalid signature', [
                    'ip' => $request->ip(),
                    'expected' => $expectedSignature,
                    'received' => $signature
                ]);
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        // Verify it's a push event
        $event = $request->header('X-GitHub-Event');
        if ($event !== 'push') {
            return response()->json(['message' => 'Event ignored'], 200);
        }

        // Verify it's from the main branch
        $payload = $request->all();
        $ref = $payload['ref'] ?? '';
        if ($ref !== 'refs/heads/main' && $ref !== 'refs/heads/master') {
            Log::info('GitHub webhook: Ignoring non-main branch', ['ref' => $ref]);
            return response()->json(['message' => 'Non-main branch ignored'], 200);
        }

        // Log the webhook
        Log::info('GitHub webhook received', [
            'ref' => $ref,
            'commits' => count($payload['commits'] ?? []),
            'pusher' => $payload['pusher']['name'] ?? 'unknown',
            'repository' => $payload['repository']['full_name'] ?? 'unknown'
        ]);

        // Execute deployment in background
        try {
            $this->deploy();
            return response()->json([
                'success' => true,
                'message' => 'Deployment initiated successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('GitHub webhook deployment failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Deployment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Execute deployment process
     */
    protected function deploy()
    {
        $basePath = base_path();
        $logFile = storage_path('logs/webhook-deploy.log');
        $scriptPath = $basePath . '/webhook-deploy.sh';
        
        // Check if deployment script exists
        if (!file_exists($scriptPath)) {
            throw new \Exception('Deployment script not found: ' . $scriptPath);
        }

        // Make sure script is executable
        chmod($scriptPath, 0755);

        // Execute deployment script in background and log output
        $command = "bash {$scriptPath} >> {$logFile} 2>&1 &";
        exec($command);

        // Log that deployment started
        Log::info('Deployment process started', [
            'script' => $scriptPath,
            'log_file' => $logFile,
            'command' => $command
        ]);
    }

    /**
     * Get deployment status/logs
     */
    public function status()
    {
        $logFile = storage_path('logs/webhook-deploy.log');
        
        if (!file_exists($logFile)) {
            return response()->json([
                'status' => 'no_deployment',
                'message' => 'No deployment logs found'
            ]);
        }

        $logs = file_get_contents($logFile);
        $lastModified = filemtime($logFile);
        
        return response()->json([
            'status' => 'completed',
            'last_deployment' => date('Y-m-d H:i:s', $lastModified),
            'logs' => $logs
        ]);
    }
}

