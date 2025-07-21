<?php

declare(strict_types=1);

namespace Modules\Consultation\Infrastructure\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker\MyPartTimeConsultationCalendarComponent;
use Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker\MySemesterConsultationCalendarComponent;
use Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker\MySessionConsultationCalendarComponent;
use Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker\NewPartTimeConsultationComponent;
use Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker\NewSemesterConsultationComponent;
use Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker\NewSessionConsultationComponent;
use Modules\Consultation\Presentation\Livewire\Dashboard\ConsultationsCard;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Modules\Consultation\Infrastructure\Repositories\ConsultationRepository;
use Modules\Consultation\Domain\Interfaces\Services\PdfGeneratorInterface;
use Modules\Consultation\Infrastructure\Services\DomPdfGenerator;
use Illuminate\Console\Scheduling\Schedule;
use Modules\Consultation\Presentation\Console\SendSemesterConsultationRemindersCommand;
use Modules\Consultation\Presentation\Console\SendSessionConsultationRemindersCommand;

class ConsultationServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Consultation';

    protected string $nameLower = 'consultation';

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

        $this->app->bind(ConsultationRepositoryInterface::class, ConsultationRepository::class);
        $this->app->bind(PdfGeneratorInterface::class, DomPdfGenerator::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            SendSemesterConsultationRemindersCommand::class,
            SendSessionConsultationRemindersCommand::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function (): void {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command(SendSemesterConsultationRemindersCommand::class)->mondays()->at('09:00');
            $schedule->command(SendSessionConsultationRemindersCommand::class)->mondays()->at('09:15');
        });
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
            'dashboard.consultations-card' => ConsultationsCard::class,
            'consultations.scientific-worker.new-semester-consultation' => NewSemesterConsultationComponent::class,
            'consultations.scientific-worker.new-session-consultation' => NewSessionConsultationComponent::class,
            'consultations.scientific-worker.new-part-time-consultation' => NewPartTimeConsultationComponent::class,
            'consultations.scientific-worker.my-semester-consultation-calendar' => MySemesterConsultationCalendarComponent::class,
            'consultations.scientific-worker.my-session-consultation-calendar' => MySessionConsultationCalendarComponent::class,
            'consultations.scientific-worker.my-part-time-consultation-calendar' => MyPartTimeConsultationCalendarComponent::class,
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
