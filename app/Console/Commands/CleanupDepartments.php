<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class CleanupDepartments extends Command
{
    protected $signature = 'departments:cleanup';
    protected $description = 'Clean up departments - keep only LOAN, ICT, ACCOUNANC, MARKETING, and OTHERS';

    public function handle()
    {
        $this->info('Starting department cleanup...');

        // Define the departments to keep
        $departmentsToKeep = [
            [
                'name' => 'LOAN',
                'code' => 'LOAN',
                'description' => 'Loan Department'
            ],
            [
                'name' => 'ICT',
                'code' => 'ICT',
                'description' => 'Information and Communication Technology'
            ],
            [
                'name' => 'ACCOUNANC',
                'code' => 'ACCOUNANC',
                'description' => 'Accountancy Department'
            ],
            [
                'name' => 'MARKETING',
                'code' => 'MARKETING',
                'description' => 'Marketing Department'
            ],
            [
                'name' => 'OTHERS',
                'code' => 'OTHERS',
                'description' => 'Other Departments'
            ],
        ];

        DB::beginTransaction();
        try {
            // Get all existing departments
            $allDepartments = Department::all();
            $codesToKeep = array_column($departmentsToKeep, 'code');
            
            $this->info('Found ' . $allDepartments->count() . ' existing departments.');
            
            // Deactivate departments that are not in the keep list (safer than deleting due to foreign keys)
            $departmentsToDeactivate = $allDepartments->filter(function($dept) use ($codesToKeep) {
                return !in_array($dept->code, $codesToKeep);
            });
            
            $deactivatedCount = 0;
            foreach ($departmentsToDeactivate as $dept) {
                $this->info("Deactivating department: {$dept->name} ({$dept->code})");
                $dept->update(['is_active' => false]);
                $deactivatedCount++;
            }
            
            // Create or update departments to keep
            $createdCount = 0;
            $updatedCount = 0;
            
            foreach ($departmentsToKeep as $deptData) {
                $existing = Department::where('code', $deptData['code'])->first();
                
                if ($existing) {
                    $existing->update([
                        'name' => $deptData['name'],
                        'description' => $deptData['description'],
                        'is_active' => true
                    ]);
                    $this->info("Updated department: {$deptData['name']} ({$deptData['code']})");
                    $updatedCount++;
                } else {
                    Department::create([
                        'name' => $deptData['name'],
                        'code' => $deptData['code'],
                        'description' => $deptData['description'],
                        'is_active' => true
                    ]);
                    $this->info("Created department: {$deptData['name']} ({$deptData['code']})");
                    $createdCount++;
                }
            }
            
            DB::commit();
            
            $this->info("\n=== Cleanup Summary ===");
            $this->info("Deactivated: {$deactivatedCount} departments");
            $this->info("Created: {$createdCount} departments");
            $this->info("Updated: {$updatedCount} departments");
            $this->info("\n✅ Department cleanup completed successfully!");
            $this->info("Note: Old departments were deactivated (not deleted) to preserve data integrity.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error during cleanup: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
