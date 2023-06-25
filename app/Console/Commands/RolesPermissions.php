<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;

class RolesPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:permissions {--detach} {--attach} {--refresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manages application roles and permissions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('detach') || $this->option('refresh')){

            $roles = Role::all();
            $roles->map(function($role){
                $role->detachPermissions($role->permissions()->get()->pluck('name')->toArray());
            });
        }

        if ($this->option('attach') || $this->option('refresh')) {

            $PermissionRoleTableSeeder = new \PermissionRoleTableSeeder;
            $PermissionRoleTableSeeder->run();
        }

        return;
    }
}
