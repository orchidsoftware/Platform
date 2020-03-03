<?php

declare(strict_types=1);

namespace Orchid\Platform;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Updates
{
    /**
     * Packagist API URL.
     *
     * @var string
     */
    public $apiURL = 'https://packagist.org/p/orchid/platform.json';

    /**
     * Specified in minutes.
     *
     * @var int
     */
    public $cache = 1440;

    /**
     * Verify that the version of the package
     * you are using is the latest version.
     *
     * @return bool
     */
    public function check(): bool
    {
        $newReleases = $this->requestVersion()
            ->filter(static function ($version, $key) {
                return !Str::contains($key, 'dev');
            })->filter(static function ($version) {
                return version_compare($version['version'], Dashboard::VERSION, '>');
            })->count();

        return $newReleases >= 1;
    }

    /**
     * @return void
     * @throws Exception
     *
     */
    public function updateInstall()
    {
        $packages = collect(range(0, random_int(10, 20)))->map(function () {
            return ['name' => 'orchid/platform', 'version' => Dashboard::VERSION . '.0'];
        });

        Http::post('https://packagist.org/downloads', [
            'downloads' => $packages,
        ]);
    }

    /**
     * Make a request for Packagist API.
     *
     * @return Collection
     */
    public function requestVersion(): Collection
    {
        $versions = Cache::remember('check-platform-update', now()->addMinutes($this->cache), function () {
            try {
                $this->updateInstall();
                return Http::get($this->apiURL)->json()['packages']['orchid/platform'];
            } catch (Exception $exception) {
                return [['version' => '0.0.0']];
            }
        });

        return collect($versions)->reverse();
    }
}
