<?php

namespace App\Providers;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Enrollement;
use App\Models\Grade;
use App\Models\Level;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Policies\GeneralPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // registrando una politica general para cada modelo
        Gate::policy(AcademicYear::class, GeneralPolicy::class);
        Gate::policy(Level::class, GeneralPolicy::class);
        Gate::policy(Grade::class, GeneralPolicy::class);
        Gate::policy(Course::class, GeneralPolicy::class);
        Gate::policy(Schedule::class, GeneralPolicy::class);
        Gate::policy(Teacher::class, GeneralPolicy::class);
        Gate::policy(Enrollement::class, GeneralPolicy::class);
    }
}
