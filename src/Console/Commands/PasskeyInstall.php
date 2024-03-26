<?php

namespace PioneerDynamics\LaravelPasskey\Console\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
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
            $this->task('Publishing migration file', fn() => $this->publishMigrations());

            if($this->option('config'))
                $this->task('Publishing config file', fn() => $this->publishConfigFile());

            if($this->option('jetstream-inertia'))
                $this->task('Publishing Jetstream-Inertia vue files', fn() => $this->publishVueComponents());
        }
    }

    private function showWarning()
    {
        $this->alert('Depending on the options provided, this command will publish the below files and modify them to suit your needs.');
        $this->comment('1. Config file, if `--config` parameter is provided');
        $this->comment('2. Migration file');
        $this->comment('3. Jetstream flavoured Inertia JS component files, if `--jetstream-inertia` is provided.');
        $this->newLine();
        $this->alert('If these files already exist, they will be replaced');
        $this->newLine();
        $this->alert('This will also replace the below Jetstream Inertia files if `--jetstream-inertia` is provided.');
        $this->comment('1. `'.resource_path('js/Components/ConfirmsPassword.vue').'`');
        $this->comment('2. `'.resource_path('js/Pages/Auth/Login.vue').'`');
    }

    private function task($message, callable $closure)
    {
        $this->output->write($message);
        
        try
        {
            $closure();
        }
        catch(\Exception $e)
        {
            $this->output->write('✘');
        }

        $this->output->write('✔');

        $this->newLine();
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
            resource_path('js/Components/ConfirmsPassword.vue'),
            resource_path('js/Pages/Auth/Login.vue')
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
