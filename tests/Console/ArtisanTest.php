<?php

declare(strict_types=1);

namespace Orchid\Tests\Console;

use Orchid\Tests\TestConsoleCase;
use Illuminate\Support\Facades\File;

class ArtisanTest extends TestConsoleCase
{
    /**
     * debug: php vendor/bin/phpunit  --filter= ArtisanTest tests\\Feature\\ArtisanTest --debug.
     *
     * @var
     */
    public function testArtisanOrchidChart()
    {
        $this->artisan('orchid:chart', ['name' => $this->generateNameFromMethod()])
            ->expectsOutput('Chart created successfully.')
            ->assertExitCode(0);
    }

    public function testArtisanOrchidTable()
    {
        $this->artisan('orchid:table', ['name' => $this->generateNameFromMethod()])
            ->expectsOutput('Table created successfully.')
            ->assertExitCode(0);
    }

    public function testArtisanOrchidScreen()
    {
        $this->artisan('orchid:screen', ['name' => $this->generateNameFromMethod()])
            ->expectsOutput('Screen created successfully.')
            ->assertExitCode(0);
    }

    public function testArtisanOrchidRows()
    {
        $this->artisan('orchid:rows', ['name' => $this->generateNameFromMethod()])
            ->expectsOutput('Rows created successfully.')
            ->assertExitCode(0);
    }

    public function testArtisanOrchidFilter()
    {
        $this->artisan('orchid:filter', ['name' => $this->generateNameFromMethod()])
            ->expectsOutput('Filter created successfully.')
            ->assertExitCode(0);
    }

    public function testArtisanOrchidSelection()
    {
        $this->artisan('orchid:selection', ['name' => $this->generateNameFromMethod()])
            ->expectsOutput('Selection created successfully.')
            ->assertExitCode(0);
    }

    public function testArtisanOrchidMetrics()
    {
        $this->artisan('orchid:metrics', ['name' => $this->generateNameFromMethod()])
            ->expectsOutput('Metric created successfully.')
            ->assertExitCode(0);
    }

    public function testArtisanOrchidAdmin()
    {
        $this->artisan('orchid:admin')
            ->expectsQuestion('What is your name?', 'testConsoleCreateUser')
            ->expectsQuestion('What is your email?', 'testConsoleCreateUser@console.loc')
            ->expectsQuestion('What is the password?', 'testConsoleCreateUser')
            ->expectsOutput('User created successfully.');

        $this->artisan('orchid:admin')
            ->expectsQuestion('What is your name?', 'testConsoleCreateUser')
            ->expectsQuestion('What is your email?', 'testConsoleCreateUser@console.loc')
            ->expectsQuestion('What is the password?', 'testConsoleCreateUser')
            ->expectsOutput('User exist');
    }

    public function testArtisanOrchidInstall()
    {
        $this->artisan('orchid:install')
            ->expectsQuestion('The platform has already been installed, do you really want to repeat?', 'yes')
            ->expectsOutput("To start the embedded server, run 'artisan serve'");
    }

    public function testArtisanOrchidLink()
    {
        $resources = public_path('resources');

        File::deleteDirectory($resources);

        $this->artisan('orchid:link')
            ->expectsOutput("The [$resources] directory has been linked.");

        $this->artisan('orchid:link')
            ->expectsOutput("The [$resources] directory already exists.");

        $this->artisan('orchid:link --force')
            ->expectsOutput("The [$resources] directory has been linked.");
    }

    public function testArtisanPresetOrchidSource()
    {
        $this->artisan('preset', ['type' => 'orchid-source'])
            ->expectsOutput('Please run "npm install && npm run dev" to compile your fresh scaffolding.')
            ->expectsOutput('Orchid scaffolding installed successfully.');
    }

    public function testArtisanPresetOrchid()
    {
        $this->artisan('preset', ['type' => 'orchid'])
            ->expectsOutput('Please run "npm install && npm run dev" to compile your fresh scaffolding.')
            ->expectsOutput("After that, You need to add this line to AppServiceProvider's register method:")
            ->expectsOutput("app(\Orchid\Platform\Dashboard::class)->registerResource('scripts','/js/dashboard.js');")
            ->expectsOutput('Orchid scaffolding installed successfully.');
    }
}
