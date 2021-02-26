<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{

    public function run(): void
    {
        /*
        Eloquent::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        Permission::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        */

        $crudPermissions = ['index', 'create', 'edit', 'delete'];
        $trashPermissions = ['trash', 'restore', 'force-delete'];

        $sections = [
            'admin-panel' => [
                'name' => 'Админ-панель',
                'permissions' => ['access', 'telescope'],
            ],
            'pages' => [
                'name' => 'Страницы приложения',
                'permissions' => [...$crudPermissions],
            ],
            'faqs' => [
                'name' => 'Часто задаваемые вопросы',
                'permissions' => [...$crudPermissions],
            ],
            'users' => [
                'name' => 'Пользователи',
                'permissions' => [...$crudPermissions, ...$trashPermissions],
            ]
        ];

        foreach ($sections as $key => $section) {
            foreach ($section['permissions'] as $permission) {
                $name = "$key.$permission";
                if (Permission::where('name', $name)->first() === null) {
                    Permission::create(['name' => $name]);
                }
            }
        }

        $roles = [
            'admin' => [
                'label' => 'Админ',
                'permissions' => Permission::all()->pluck('name')
            ],
            'guest' => [
                'label' => 'Гость',
                'permissions' => ['admin-panel.access', 'users.index']
            ]
        ];

        foreach ($roles as $key => $data) {
            if (!($role = Role::where('name', '=', $key)->first())) {
                $role = new Role(['name' => $key, 'label' => $data['label']]);
                $role->save();
            }
            if (isset($data['permissions'])) {
                $permissions = $data['permissions'];
                $role->syncPermissions($permissions);
            }
        }
    }
}
