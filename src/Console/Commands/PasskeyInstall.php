<?php

namespace PioneerDynamics\LaravelPasskey\Console\Commands;

use RuntimeException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use PioneerDynamics\LaravelPasskey\Providers\PasskeyServiceProvider;

class PasskeyInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passkey:install 
                                {--T|table=users : table where the users are stored }
                                {--U|username=email : the name of the username field }
                                {--C|config : publish the config file}
                                {--I|jetstream-inertia : publish Jetstream inertia flavoured Vue compoenent files}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Passkey support for Laravel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->comment('Welcome to the Laravel Passky Installation File');
        
        $this->newLine();
        
        $this->showWarning();

        $this->newLine();

        if($this->confirm('This command should ONLY be run in development and ONLY ONCE. Do you want to continue?'))
        {
            $this->publishMigrations();

            if($this->option('config'))
                $this->publishConfigFile();

            if($this->option('jetstream-inertia'))
            {
                $this->publishVueComponents();
                $this->installNodeDependencies();
            }
        }
    }

    /**
     * Run the given commands.
     *
     * @param  array  $commands
     * @return void
     */
    protected function runCommands($commands)
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    '.$line);
        });
    }

    private function installNodeDependencies()
    {
        $this->updateNodePackages(function ($packages) {
            return [
                'luxon' => '^3.4.4',
                '@simplewebauthn/browser' => '^9.0.1',
            ] + $packages;
        }, false);

        if (file_exists(base_path('pnpm-lock.yaml'))) {
            $this->runCommands(['pnpm install', 'pnpm run build']);
        } elseif (file_exists(base_path('yarn.lock'))) {
            $this->runCommands(['yarn install', 'yarn run build']);
        } else {
            $this->runCommands(['npm install', 'npm run build']);
        }
    }

    /**
     * Update the "package.json" file.
     *
     * @param  callable  $callback
     * @param  bool  $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    private function showWarning()
    {
        $this->alert('Depending on the options provided, this command will publish the below files and modify them to suit your needs.');
        $this->comment('1. Config file, if `--config` parameter is provided');
        $this->comment('2. Migration file');
        $this->comment('3. Jetstream flavoured Inertia JS component files, if `--jetstream-inertia` is provided.');
        $this->newLine();
        $this->alert('If these files already exist, they will be replaced');
    }

    private function publishVueComponents()
    {
        $this->call('vendor:publish', [
            '--provider' => PasskeyServiceProvider::class,
            '--tag' => 'vue-components',
            '--quiet'
        ]);

        $this->replacePlaceholders(
            resource_path('js/Components/ConfirmsPasskey.vue'),
            resource_path('js/Components/ConfirmsPasswordOrPasskey.vue'),
            resource_path('js/Pages/Auth/LoginWithPasskey.vue')
        );
    }

    private function publishMigrations()
    {
        $this->call('vendor:publish', [
            '--provider' => PasskeyServiceProvider::class,
            '--tag' => 'migrations',
            '--quiet'
        ]);
    }

    public function publishConfigFile()
    {
        $this->call('vendor:publish', [
            '--provider' => PasskeyServiceProvider::class,
            '--tag' => 'config',
            '--quiet'
        ]);

        $this->replacePlaceholders(config_path('passkey.php'));
    }

    private function replacePlaceholders(...$files)
    {
        $search = [
            '__USERNAME__',
            '__TABLE__',
            '__USERNAME_LABEL__',
            '__USERNAME_TYPE__'
        ];
        
        $replacements = [
            $this->option('username'),
            $this->option('table'),
            Str::title($this->option('username')),
            $this->option('username') == 'email' ? 'email' : 'text'
        ];

        foreach(Arr::wrap($files) as $file)
        {
            $contents = file_get_contents($file);

            $contents = str_replace($search, $replacements, $contents);

            file_put_contents($file, $contents);
        }
    }
}
