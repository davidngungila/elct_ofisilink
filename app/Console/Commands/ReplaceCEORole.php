<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReplaceCEORole extends Command
{
    protected $signature = 'role:replace-ceo-to-general-manager';
    protected $description = 'Replace CEO role with General Manager role in database';

    public function handle()
    {
        $this->info('Starting CEO to General Manager role replacement...');

        DB::beginTransaction();
        try {
            // Find CEO role
            $ceoRole = Role::where('name', 'CEO')->first();
            
            if ($ceoRole) {
                // Check if General Manager role exists
                $gmRole = Role::where('name', 'General Manager')->first();
                
                if (!$gmRole) {
                    // Create General Manager role from CEO role
                    $gmRole = Role::create([
                        'name' => 'General Manager',
                        'display_name' => $ceoRole->display_name ?? 'General Manager',
                        'description' => $ceoRole->description ?? 'Executive level access',
                    ]);
                    
                    // Copy permissions
                    $gmRole->permissions()->sync($ceoRole->permissions()->pluck('permissions.id'));
                    
                    $this->info('Created General Manager role with CEO permissions.');
                }
                
                // Update all users with CEO role to General Manager
                $usersWithCEO = User::whereHas('roles', function($q) {
                    $q->where('name', 'CEO');
                })->get();
                
                foreach ($usersWithCEO as $user) {
                    $user->roles()->detach($ceoRole->id);
                    $user->roles()->attach($gmRole->id);
                    $this->info("Updated user: {$user->name} from CEO to General Manager");
                }
                
                // Delete CEO role
                $ceoRole->delete();
                $this->info('Deleted CEO role.');
            } else {
                $this->warn('CEO role not found. It may have already been replaced.');
            }
            
            DB::commit();
            $this->info('Successfully replaced CEO role with General Manager!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error replacing role: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
