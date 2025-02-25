<?php

namespace Incentads\Disposable\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\BindingResolutionException;
use Incentads\Disposable\DisposableDomains;
use Incentads\Disposable\Fetcher\Fetcher;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\Console\Command\Command as CommandAlias;

class UpdateDisposableDomainsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disposable:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates to the latest disposable email domains list';

    /**
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function handle(Config $config, DisposableDomains $disposable)
    {
        $this->line('Fetching from source...');

        $fetcher = $this->laravel->make(
            $fetcherClass = $config->get('disposable.email.fetcher'),
        );

        if ( ! $fetcher instanceof Fetcher) {
            $this->error($fetcherClass . ' should implement ' . Fetcher::class);

            return CommandAlias::FAILURE;
        }

        $sources = $config->get('disposable.email.sources');

        if ( ! $sources && $config->get('disposable.email.source')) {
            $sources = [$config->get('disposable.email.source')];
        }

        if ( ! is_array($sources)) {
            $this->error('Source URLs should be defined in an array');

            return CommandAlias::FAILURE;
        }

        $data = [];
        foreach ($sources as $source) {
            $data = array_merge($data, $this->laravel->call([$fetcher, 'handle'], [
                'url' => $source,
            ]));
        }

        $this->line('Saving response to storage...');

        if ( ! $disposable->saveToStorage($data)) {
            $this->error('Could not write to storage (' . $disposable->getStoragePath() . ')!');

            return CommandAlias::FAILURE;
        }

        $this->info('Disposable domains list updated successfully.');

        $disposable->bootstrap();

        return CommandAlias::SUCCESS;
    }
}
