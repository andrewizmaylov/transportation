<?php

declare(strict_types=1);

namespace Src;

use App\Http\Middleware\JsonApiSpecificationMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Src\Cargo\DomainLayer\Repository\CargoRepositoryInterface;
use Src\Cargo\DomainLayer\Storage\CargoStorageInterface;
use Src\Cargo\InfrastructureLayer\Repository\CargoRepository;
use Src\Cargo\InfrastructureLayer\Storage\CargoStorage;
use Src\SharedKernel\DomainLayer\Repository\CityRepositoryInterface;
use Src\SharedKernel\DomainLayer\Repository\CountryRepositoryInterface;
use Src\SharedKernel\DomainLayer\Repository\CurrencyRepositoryInterface;
use Src\SharedKernel\DomainLayer\Repository\TransportationAddressRepositoryInterface;
use Src\SharedKernel\DomainLayer\Repository\UserRepositoryInterface;
use Src\SharedKernel\DomainLayer\Storage\CityStorageInterface;
use Src\SharedKernel\DomainLayer\Storage\TransportationAddressStorageInterface;
use Src\SharedKernel\InfrastructureLayer\Repository\CityRepository;
use Src\SharedKernel\InfrastructureLayer\Repository\CountryRepository;
use Src\SharedKernel\InfrastructureLayer\Repository\CurrencyRepository;
use Src\SharedKernel\InfrastructureLayer\Repository\TransportationAddressRepository;
use Src\SharedKernel\InfrastructureLayer\Repository\UserRepository;
use Src\SharedKernel\InfrastructureLayer\Storage\CityStorage;
use Src\SharedKernel\InfrastructureLayer\Storage\TransportationAddressStorage;
use Src\Transportation\DomainLayer\Repository\TransportationRepositoryInterface;
use Src\Transportation\DomainLayer\Storage\TransportationStorageInterface;
use Src\Transportation\InfrastructureLayer\Repository\TransportationRepository;
use Src\Transportation\InfrastructureLayer\Storage\TransportationStorage;

class ServiceProvider extends IlluminateServiceProvider
{
    protected array $middlewareGroups = [
        JsonApiSpecificationMiddleware::class,
        'api-web', // Use api-web instead of web to exclude CSRF
        'auth:sanctum',
    ];

    protected array $openMiddleware = [
        JsonApiSpecificationMiddleware::class,
    ];

    /**
     * Register domain service providers.
     */
    public function register(): void
    {
        $this->registerContracts();
    }

    /**
     * Load routes after registering all services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
    }

    public function registerRoutes(): void
    {
        Route::middleware($this->openMiddleware)->group(__DIR__ . '/SharedKernel/PresentationLayer/HTTP/V1/routes.php');
        Route::middleware($this->middlewareGroups)->group(__DIR__ . '/Cargo/PresentationLayer/HTTP/V1/routes.php');
        Route::middleware($this->middlewareGroups)->group(__DIR__ . '/Carrier/PresentationLayer/HTTP/V1/routes.php');
        Route::middleware($this->middlewareGroups)->group(__DIR__ . '/FinancialInformation/PresentationLayer/HTTP/V1/routes.php');
        Route::middleware($this->middlewareGroups)->group(__DIR__ . '/Transportation/PresentationLayer/HTTP/V1/routes.php');
        Route::middleware($this->middlewareGroups)->group(__DIR__ . '/Shipper/PresentationLayer/HTTP/V1/routes.php');
    }

    public function registerContracts(): void
    {
        $this->app->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        $this->app->bind(TransportationRepositoryInterface::class, TransportationRepository::class);
        $this->app->bind(TransportationStorageInterface::class, TransportationStorage::class);

        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
        $this->app->bind(CityStorageInterface::class, CityStorage::class);

        $this->app->bind(TransportationAddressStorageInterface::class, TransportationAddressStorage::class);
        $this->app->bind(TransportationAddressRepositoryInterface::class, TransportationAddressRepository::class);

        $this->app->bind(CargoStorageInterface::class, CargoStorage::class);
        $this->app->bind(CargoRepositoryInterface::class, CargoRepository::class);
    }
}
