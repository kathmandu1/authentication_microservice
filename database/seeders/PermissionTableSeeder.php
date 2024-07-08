<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();
        $permissions = [
            'view', 'create', 'edit', 'delete', 'viewany',
        ];
        $modules = [
            'order', 'deal', 'service', 'service-offer', 'permission', 'user', 'vendor', 'category', 'vendor-info',
            'cupon', 'review', 'rating', 'setting', 'rider-order', 'rider-delivery', 'rider-comission', 'rider-assign',
            'dashboard', 'revenue', 'best-selling-product', 'rider-info', 'product', 'vendor-total-earnings', 'vendor-orders', 'vendor-customer', 'vendor-month-earning', 'vendor-recent-activity', 'rider-setting',
        ];

        foreach ($modules as $module) {
            foreach ($permissions as $permission) {
                $permissionName = $permission . '-' . $module;

                Permission::firstOrCreate(
                    ['name' => $permissionName],
                    ['guard_name' => 'web']
                );
            }
        }
    }
}
