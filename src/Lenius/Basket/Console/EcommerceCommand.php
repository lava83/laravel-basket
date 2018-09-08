<?php
/**
 * Created by PhpStorm.
 * User: firma
 * Date: 9/8/18
 * Time: 12:23 AM.
 */

namespace Lenius\Basket\Console;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;

class EcommerceCommand extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'ecommerce/basket.stub' => 'ecommerce/basket.blade.php',
    ];

    /**
     * Languages supported
     *
     * @var array
     */
    protected $languages = [
        'da' => 'da/ecommerce.php',
        'en' => 'en/ecommerce.php',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:ecommerce
                    {--views : Only scaffold the ecommerce views}
                    {--force : Overwrite existing views by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ecommerce scaffold basic login and registration views and routes';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->createDirectories();

        $this->exportViews();

        $this->exportLanguages();

        if (! $this->option('views')) {
            file_put_contents(
                app_path('Http/Controllers/BasketController.php'),
                $this->compileControllerStub()
            );

            file_put_contents(
                base_path('routes/web.php'),
                file_get_contents(__DIR__.'/stubs/make/routes.stub'),
                FILE_APPEND
            );
        }

        $this->info('Ecommerce installed');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        if (!is_dir($directory = resource_path('views/layouts'))) {
            mkdir($directory, 0755, true);
        }

        if (!is_dir($directory = resource_path('views/ecommerce'))) {
            mkdir($directory, 0755, true);
        }

        foreach ($this->languages as $key => $value) {
            if (!is_dir($directory = resource_path('lang/'.$key))) {
                mkdir($directory, 0755, true);
            }
        }
    }

    /**
     * Export the ecommerce views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $key => $value) {
            if (file_exists($view = resource_path('views/'.$value)) && !$this->option('force')) {
                if (!$this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__.'/stubs/make/views/'.$key,
                $view
            );
        }
    }

    /**
     * Export the ecommerce languages.
     *
     * @return void
     */
    protected function exportLanguages()
    {
        foreach ($this->languages as $key => $value) {
            if (file_exists($lang = resource_path('lang/'.$value)) && !$this->option('force')) {
                if (!$this->confirm("The [{$value}] language already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            $this->info(__DIR__.'/stubs/make/lang/'.$value);

            copy(
                __DIR__.'/stubs/make/lang/'.$value,
                $lang
            );
        }
    }

    /**
     * Compiles the BasketController stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents(__DIR__.'/stubs/make/controllers/BasketController.stub')
        );
    }
}
