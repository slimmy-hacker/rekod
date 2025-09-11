<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
       
    }

    
    public function boot(): void
    {
        
        Schema::defaultStringLength(191);
    }
    public function redirectTo()
{
    $user = auth()->user();

    if ($user->isStudent()) {
        return '/student/portal';
    } elseif ($user->isSupervisor()) {
        return '/supervisor/portal';
    } elseif ($user->isIndustry()) {
        return '/industry/portal';
    }

    return '/';
}
}
