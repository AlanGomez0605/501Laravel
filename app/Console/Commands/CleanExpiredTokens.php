<?php

namespace App\Console\Commands;

use App\Models\EmailVerificationToken;
use Illuminate\Console\Command;

class CleanExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:clean-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eliminar tokens de verificación de email expirados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deletedCount = EmailVerificationToken::where('expires_at', '<', now())->delete();
        
        $this->info("Se eliminaron {$deletedCount} tokens expirados.");
        
        return 0;
    }
}
