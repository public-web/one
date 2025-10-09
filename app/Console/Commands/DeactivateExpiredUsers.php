<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeactivateExpiredUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate users whose accounts have expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired users...');

        // Find users who are active but have expired - using new scopes and indexed columns
        $expiredUsers = User::active()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        if ($expiredUsers->isEmpty()) {
            $this->info('No expired users found.');

            return Command::SUCCESS;
        }

        $count = $expiredUsers->count();
        $this->info("Found {$count} expired user(s):");

        /** @var \App\Models\User $user */
        foreach ($expiredUsers as $user) {
            $this->line("- {$user->name} ({$user->email}) - Expired: {$user->expires_at->format('Y-m-d H:i:s')}");

            // Deactivate the user
            $user->update(['active' => false]);
        }

        $this->info("{$count} user(s) have been deactivated.");

        return Command::SUCCESS;
    }
}
