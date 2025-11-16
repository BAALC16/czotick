<?php

namespace App\Providers;

use App\Models\User;
use GMP;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
         'App\Models\User' => 'App\Policies\UserPolicy',
         'App\Models\Article' => 'App\Policies\ArticlePolicy',
         'App\Models\Trace' => 'App\Policies\TracesPolicy',
         'App\Models\Contact' => 'App\Policies\ContactsPolicy',
         'App\Models\Rent' => 'App\Policies\RentPolicy',
         'App\Models\InquiryAgenda' => 'App\Policies\InquiryAgendasPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();


    }
}
