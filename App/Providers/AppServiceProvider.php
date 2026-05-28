<?php

// FILE: app/Providers/AppServiceProvider.php
// ============================================================
namespace App\Providers;

use App\Repositories\Contracts\{
    BookingRepositoryInterface,
    FieldSlotRepositoryInterface,
    MatchRepositoryInterface,
    PostRepositoryInterface,
    TransactionRepositoryInterface,
    UserRepositoryInterface
};
use App\Repositories\Eloquent\{
    BookingRepository,
    FieldSlotRepository,
    MatchRepository,
    PostRepository,
    TransactionRepository,
    UserRepository
};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(UserRepositoryInterface::class,        UserRepository::class);
        $this->app->bind(FieldSlotRepositoryInterface::class,   FieldSlotRepository::class);
        $this->app->bind(BookingRepositoryInterface::class,     BookingRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(MatchRepositoryInterface::class,       MatchRepository::class);
        $this->app->bind(PostRepositoryInterface::class,        PostRepository::class);
    }

    public function boot(): void
    {
        //
    }
}


// ============================================================