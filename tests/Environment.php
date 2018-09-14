<?php

declare(strict_types=1);

namespace Orchid\Tests;

use Watson\Active\Active;
use Orchid\Platform\Models\User;
use Orchid\Support\Facades\Alert;
use Intervention\Image\Facades\Image;
use Orchid\Support\Facades\Dashboard;
use Illuminate\Support\Facades\Schema;
use Orchid\Tests\Entities\DemoTestPage;
use Orchid\Tests\Entities\DemoTestPost;
use Orchid\Press\Providers\PressServiceProvider;
use Orchid\Bulldozer\Providers\BulldozerServiceProvider;
use Orchid\Platform\Providers\FoundationServiceProvider;

/**
 * Trait Environment.
 */
trait Environment
{
    /**
     * Setup the test environment.
     * Run test: php vendor/bin/phpunit --coverage-html ./logs/coverage ./tests
     * Run 1 test:  php vendor/bin/phpunit  --filter= UserTest tests\\Unit\\Platform\\UserTest --debug.
     */
    protected function setUp()
    {
        parent::setUp();

        Schema::defaultStringLength(191);

        $this->artisan('vendor:publish', [
            '--provider' => 'Orchid\Platform\Providers\FoundationServiceProvider',
        ]);

        $this->artisan('vendor:publish', [
            '--provider' => 'Orchid\Press\Providers\PressServiceProvider',
        ]);

        $this->artisan('vendor:publish', [
            '--all' => true,
            '--tag' => 'config,migrations',
        ]);

        $this->artisan('migrate:fresh', [
            '--database' => 'orchid',
        ]);

        $this->artisan('orchid:link');

        $this->withFactories(realpath(PLATFORM_PATH.'/database/factories'));

        $this->artisan('db:seed', [
            '--class' => 'Orchid\Database\Seeds\OrchidDatabaseSeeder',
        ]);

        $this->artisan('orchid:admin', [
            'name'     => 'admin',
            'email'    => 'admin@admin.com',
            'password' => 'password',
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('auth.providers.users.model', User::class);

        // set up database configuration
        $app['config']->set('database.connections.orchid', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('scout.driver', null);
        $app['config']->set('database.default', 'orchid');
        $app['config']->set('activitylog.enabled', false);

        $app['config']->set('sluggable', [
                'source'             => null,
                'maxLength'          => null,
                'maxLengthKeepWords' => true,
                'method'             => null,
                'separator'          => '-',
                'unique'             => true,
                'uniqueSuffix'       => null,
                'includeTrashed'     => false,
                'reserved'           => null,
                'onUpdate'           => false,
        ]);

        $app->make(\Orchid\Platform\Dashboard::class)->registerEntities([
            //DemoTestPost::class,
            //DemoTestPage::class,
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            FoundationServiceProvider::class,
            PressServiceProvider::class,
            BulldozerServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Alert'     => Alert::class,
            'Active'    => Active::class,
            'Dashboard' => Dashboard::class,
            'Image'     => Image::class,
        ];
    }
}
