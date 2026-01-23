<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StandardizeEmployeeNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:standardize-numbers 
                            {--dry-run : Run without making changes}
                            {--format=sequential : Format to use (sequential, date-based)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Standardize all employee numbers to a consistent format (EMP001, EMP002, etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $format = $this->option('format');

        $this->info('Starting employee number standardization...');
        $this->newLine();

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Get all users with employee_id
        $users = User::whereNotNull('employee_id')
            ->orderBy('id')
            ->get();

        if ($users->isEmpty()) {
            $this->warn('No users with employee_id found.');
            return 0;
        }

        $this->info("Found {$users->count()} users with employee numbers to standardize.");
        $this->newLine();

        // Track changes
        $changes = [];
        $skipped = [];
        $errors = [];

        DB::beginTransaction();
        try {
            $counter = 1;

            foreach ($users as $user) {
                $oldEmployeeId = $user->employee_id;
                
                // Generate new employee ID based on format
                if ($format === 'date-based') {
                    $newEmployeeId = $this->generateDateBasedId($user, $counter);
                } else {
                    // Sequential format: EMP001, EMP002, etc.
                    $newEmployeeId = 'EMP' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                }

                // Check if new ID already exists (shouldn't happen, but safety check)
                $exists = User::where('employee_id', $newEmployeeId)
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($exists) {
                    // If exists, try with higher counter
                    $tempCounter = $counter;
                    do {
                        $tempCounter++;
                        $newEmployeeId = 'EMP' . str_pad($tempCounter, 3, '0', STR_PAD_LEFT);
                        $exists = User::where('employee_id', $newEmployeeId)
                            ->where('id', '!=', $user->id)
                            ->exists();
                    } while ($exists && $tempCounter < 10000);
                    
                    if ($exists) {
                        $errors[] = [
                            'user' => $user->name,
                            'old_id' => $oldEmployeeId,
                            'error' => 'Could not generate unique ID'
                        ];
                        continue;
                    }
                }

                // Skip if already in correct format
                if ($oldEmployeeId === $newEmployeeId) {
                    $skipped[] = [
                        'user' => $user->name,
                        'employee_id' => $oldEmployeeId
                    ];
                    $counter++;
                    continue;
                }

                if (!$dryRun) {
                    $user->employee_id = $newEmployeeId;
                    $user->save();
                }

                $changes[] = [
                    'user' => $user->name,
                    'old_id' => $oldEmployeeId,
                    'new_id' => $newEmployeeId
                ];

                $counter++;
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }

            // Display results
            $this->displayResults($changes, $skipped, $errors, $dryRun);

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error standardizing employee numbers: ' . $e->getMessage());
            Log::error('Employee number standardization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Generate date-based employee ID
     */
    private function generateDateBasedId($user, $counter)
    {
        $date = $user->hire_date 
            ? date('Ymd', strtotime($user->hire_date)) 
            : date('Ymd');

        $deptCode = 'XX';
        if ($user->primary_department_id) {
            $department = $user->primaryDepartment;
            if ($department && $department->code) {
                $code = strtoupper($department->code);
                $deptCode = substr($code, 0, 2);
                if (strlen($deptCode) < 2) {
                    $deptCode = str_pad($deptCode, 2, 'X', STR_PAD_RIGHT);
                }
            } elseif ($department && $department->name) {
                $name = strtoupper(preg_replace('/[^A-Z]/', '', $department->name));
                $deptCode = substr($name, 0, 2);
                if (strlen($deptCode) < 2) {
                    $deptCode = 'XX';
                }
            }
        }

        return 'EMP' . $date . $deptCode;
    }

    /**
     * Display results table
     */
    private function displayResults($changes, $skipped, $errors, $dryRun)
    {
        $this->newLine();
        $this->info('=== STANDARDIZATION RESULTS ===');
        $this->newLine();

        if (!empty($changes)) {
            $this->info("Changes " . ($dryRun ? "(would be) " : "") . "made: " . count($changes));
            $this->newLine();

            $headers = ['User Name', 'Old Employee ID', 'New Employee ID'];
            $rows = array_map(function($change) {
                return [
                    $change['user'],
                    $change['old_id'],
                    $change['new_id']
                ];
            }, $changes);

            $this->table($headers, $rows);
            $this->newLine();
        }

        if (!empty($skipped)) {
            $this->comment("Skipped (already in correct format): " . count($skipped));
            if ($this->option('verbose')) {
                foreach ($skipped as $skip) {
                    $this->line("  - {$skip['user']}: {$skip['employee_id']}");
                }
            }
            $this->newLine();
        }

        if (!empty($errors)) {
            $this->error("Errors: " . count($errors));
            foreach ($errors as $error) {
                $this->error("  - {$error['user']}: {$error['error']}");
            }
            $this->newLine();
        }

        $this->info("Total processed: " . (count($changes) + count($skipped) + count($errors)));
    }
}

