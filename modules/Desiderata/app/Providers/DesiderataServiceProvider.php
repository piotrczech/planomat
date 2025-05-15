<?php

declare(strict_types=1);

namespace Modules\Desiderata\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Desiderata\Application\UseCases\ScientificWorker\UpdateOrCreateDesideratumUseCase;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Modules\Desiderata\Infrastructure\Repositories\EloquentDesideratumRepository;
use Modules\Desiderata\Presentation\Livewire\Dashboard\DesiderataCard;
use Modules\Desiderata\Presentation\Livewire\Desideratum\ScientificWorker\DesiderataFormAvailabilityStepComponent;
use Modules\Desiderata\Presentation\Livewire\Desideratum\ScientificWorker\DesiderataFormPreferencesStepComponent;
use Modules\Desiderata\Presentation\Livewire\Desideratum\ScientificWorker\DesiderataFormWizardComponent;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DesiderataServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Desiderata';

    protected string $nameLower = 'desiderata';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerComponents();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        // Rejestracja repozytorium
        $this->app->bind(
            DesideratumRepositoryInterface::class,
            EloquentDesideratumRepository::class,
        );

        // Rejestracja przypadku użycia
        $this->app->bind(UpdateOrCreateDesideratumUseCase::class, function ($app) {
            return new UpdateOrCreateDesideratumUseCase(
                $app->make(DesideratumRepositoryInterface::class),
            );
        });
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey = $this->nameLower . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Register components.
     */
    protected function registerComponents(): void
    {
        $components = [
            'dashboard.desiderata-card' => DesiderataCard::class,
            'desideratum.scientific-worker.desiderata-form-wizard' => DesiderataFormWizardComponent::class,
            'desideratum.scientific-worker.desiderata-form-availability-step' => DesiderataFormAvailabilityStepComponent::class,
            'desideratum.scientific-worker.desiderata-form-preferences-step' => DesiderataFormPreferencesStepComponent::class,
        ];

        foreach ($components as $alias => $component) {
            Livewire::component("{$this->nameLower}::{$alias}", $component);
        }
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];

        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
