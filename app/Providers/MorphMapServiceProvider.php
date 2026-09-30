<?php

namespace App\Providers;

use App\Models\{
    Attachment, Country, EntityRelationship, LogEntityStatus,
    OtpToken, Permission, Role, State, Status, User,
};
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class MorphMapServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register individual model mappers for optimal performance
        Relation::morphMap([
            'Attachment' => Attachment::class,
            'Country' => Country::class,
            'OtpToken' => OtpToken::class,
            'Permission' => Permission::class,
            'Role' => Role::class,
            'State' => State::class,
            'Status' => Status::class,
            'User' => User::class,
            "LogEntityStatus"  => LogEntityStatus::class,
            "EntityRelationship"  => EntityRelationship::class,
        ]);
    }
}