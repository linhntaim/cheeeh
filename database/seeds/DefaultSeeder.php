<?php

use App\V1\Models\Permission;
use App\V1\Models\Role;
use App\V1\Models\User;
use App\V1\Models\UserEmail;
use App\V1\Utils\ConfigHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultSeeder extends Seeder
{
    public function run()
    {
        $beSystem = Permission::create([
            'name' => 'be-system',
            'display_name' => 'Be System',
            'description' => 'Be system'
        ]);
        $systemRole = Role::create(array(
            'name' => 'system',
            'display_name' => 'System',
            'description' => 'Representation of the system',
        ));
        $systemRole->permissions()->attach([
            $beSystem->id,
        ]);

        $beOwner = Permission::create([
            'name' => 'be-owner',
            'display_name' => 'Be Owner',
            'description' => 'Be owner'
        ]);
        $ownerRole = Role::create(array(
            'name' => 'owner',
            'display_name' => 'Owner',
            'description' => 'Owner of the system',
        ));
        $ownerRole->permissions()->attach([
            $beOwner->id,
        ]);

        $system = User::create(array(
            'display_name' => 'System',
            'name' => 'system',
            'password' => Hash::make(')^KM$bB-W7:Z@8eG'),
            'url_avatar' => ConfigHelper::defaultAvatarUrl(),
        ));
        $system->emails()->create([
            'email' => 'system@cheeeh.com',
            'is_alias' => UserEmail::IS_ALIAS_NO,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);
        $system->localizations()->create();
        $system->roles()->attach([
            $systemRole->id,
        ]);

        $owner = User::create(array(
            'display_name' => 'Owner',
            'name' => 'owner',
            'password' => Hash::make('3sQUJ8yXc@m#3bx3'),
            'url_avatar' => ConfigHelper::defaultAvatarUrl(),
        ));
        $owner->emails()->create([
            'email' => 'owner@cheeeh.com',
            'is_alias' => UserEmail::IS_ALIAS_NO,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);
        $owner->localizations()->create();
        $owner->roles()->attach([
            $ownerRole->id,
        ]);

        $superAdministrator = User::create(array(
            'display_name' => 'Super Administrator',
            'name' => 'superadmin',
            'password' => Hash::make('6cWZzv!j?53pfR+7'),
            'url_avatar' => ConfigHelper::defaultAvatarUrl(),
        ));
        $superAdministrator->emails()->create([
            'email' => 'superadmin@cheeeh.com',
            'is_alias' => UserEmail::IS_ALIAS_NO,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);
        $superAdministrator->localizations()->create();

        $admin = User::create(array(
            'display_name' => 'Administrator',
            'name' => 'admin',
            'password' => Hash::make('12345678'),
            'url_avatar' => ConfigHelper::defaultAvatarUrl(),
        ));
        $admin->emails()->create([
            'email' => 'admin@cheeeh.com',
            'is_alias' => UserEmail::IS_ALIAS_NO,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);
        $admin->localizations()->create();

        $tester = User::create(array(
            'display_name' => 'Tester',
            'name' => 'tester',
            'password' => Hash::make('12345678'),
            'url_avatar' => ConfigHelper::defaultAvatarUrl(),
        ));
        $tester->emails()->create([
            'email' => 'tester@cheeeh.com',
            'is_alias' => UserEmail::IS_ALIAS_NO,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);
        $tester->localizations()->create();
    }
}
