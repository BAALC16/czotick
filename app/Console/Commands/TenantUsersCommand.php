<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TenantDatabaseService;
use App\Models\Organization;

class TenantUsersCommand extends Command
{
    protected $signature = 'tenant:users';
    protected $description = 'Lister tous les utilisateurs tenant MySQL';

    protected $tenantService;

    public function __construct(TenantDatabaseService $tenantService)
    {
        parent::__construct();
        $this->tenantService = $tenantService;
    }

    public function handle()
    {
        $this->info('👥 Utilisateurs tenant MySQL');
        $this->newLine();
        
        $users = $this->tenantService->listTenantUsers();
        
        if ($users->isEmpty()) {
            $this->warn('Aucun utilisateur tenant trouvé');
            return;
        }
        
        $this->table(['Utilisateur', 'Host', 'Base de Données'], 
            $users->map(fn($user) => [
                $user['username'],
                $user['host'], 
                $user['database']
            ])
        );
        
        $this->newLine();
        $this->info("Total: {$users->count()} utilisateurs tenant");
        $this->info("Mot de passe commun: Une@Vie@2route");
    }
}