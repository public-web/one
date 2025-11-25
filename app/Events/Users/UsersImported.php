<?php

namespace App\Events\Users;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UsersImported
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param int $successCount Number of users successfully imported
     * @param int $errorCount Number of users that failed to import
     * @param array $failures Details of failed imports
     */
    public function __construct(
        public int $successCount,
        public int $errorCount,
        public array $failures = []
    ) {}
}
