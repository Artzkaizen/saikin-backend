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

            "index_broadcast_template",
            "filter_index_broadcast_template",
            "search_index_broadcast_template",
            "show_broadcast_template",
            "update_broadcast_template",
            "delete_broadcast_template",

            "index_cache",
            "clear_cache",

            "index_contact",
            "filter_index_contact",
            "search_index_contact",
            "show_contact",
            "update_contact",
            "delete_contact",

            "index_group",
            "filter_index_group",
            "search_index_group",
            "show_group",
            "store_group",
            "update_group",
            "delete_group",

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

            "index_profile",
            "filter_index_profile",
            "search_index_profile",
            "show_profile",
            "update_profile",
            "delete_profile",

            "index_user",
            "filter_index_user",
            "search_index_user",
            "show_user",
            "show_role_permission",
            "relation_user",
            "update_user",
            "block_user",
            "unblock_user",
            "delete_user",
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
