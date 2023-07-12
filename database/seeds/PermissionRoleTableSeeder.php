<?php

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // List all permissions
        $admin_permissions = [
            "index_account",
            "filter_index_account",
            "search_index_account",
            "show_account",
            "update_account",
            "delete_account",

            "index_broadcast",
            "filter_index_broadcast",
            "search_index_broadcast",
            "show_broadcast",
            "update_broadcast",
            "broadcast_placeholder_index",
            "broadcast_placeholder_update",
            "delete_broadcast",

            "index_cache",
            "clear_cache",

            "index_contact",
            "filter_index_contact",
            "search_index_contact",
            "show_contact",
            "update_contact",
            "delete_contact",

            "index_permission",
            "store_permission",
            "show_permission",
            "update_permission",
            "assign_permission",
            "retract_permission",
            "delete_permission",

            "index_role",
            "store_role",
            "show_role",
            "update_role",
            "assign_role",
            "retract_role",
            "delete_role",

        ];
        $management_permissions = [
            //
        ];
        $management_accountant_permissions = [
            //
        ];
        $management_supervisor_permissions = [
           //
        ];
        $management_employee_permissions = [
           //
        ];

        // Assign all permissions to the Admin role
        $admin_role = Role::where('name', 'admin')->first()->attachPermissions(
            Permission::whereIn('name',
                array_merge($admin_permissions)
            )->get()
        );

        // Assign some permissions to the management role
        $management_role = Role::where('name', 'management')->first()->attachPermissions(
            Permission::whereIn('name',
                array_merge($management_permissions,$management_accountant_permissions,$management_supervisor_permissions,$management_employee_permissions)
            )->get()
        );

        // Assign some permissions to the management accountant role
        $management_accountant_role = Role::where('name', 'management_accountant')->first()->attachPermissions(
            Permission::whereIn('name',
                array_merge($management_accountant_permissions,$management_supervisor_permissions,$management_employee_permissions)
            )->get()
        );

        // Assign some permissions to the management supervisor role
        $management_supervisor_role = Role::where('name', 'management_supervisor')->first()->attachPermissions(
            Permission::whereIn('name',
                array_merge($management_supervisor_permissions,$management_employee_permissions)
            )->get()
        );

        // Assign some permissions to the management employee role
        $management_employee_role = Role::where('name', 'management_employee')->first()->attachPermissions(
            Permission::whereIn('name',
                array_merge($management_employee_permissions)
            )->get()
        );
    }
}
