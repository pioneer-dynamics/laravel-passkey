# Laravel Passkeys

Easy Passkey integration for Laravel

> Click the thumbnail below to see a video of the package in action.:

[![](https://play.vidyard.com/ZX4gLpzArkiCCTkrxeXPLH.jpg)](https://share.vidyard.com/watch/ZX4gLpzArkiCCTkrxeXPLH?)

## Thank You

The core of the project uses [svgta/webauthn](https://github.com/svgta1/webauthn-php) library.

## We are looking for contributors

We are looking for contributors to help improve the library and add compatibility for Livewire.

## ALERT

> !!!WARNING!!! The command `passkey:install` command must be run ONLY on development and ONLY ONCE. Depending on the options provided, this command will publish the below files and modify them to suit your needs. Please find the details below.

1. Config file, if `--config` parameter is provided
2. Migration file
3. Jetstream flavoured Inertia JS component files, if `--jetstream-inertia` is provided.

> !!WARNING!! Any existing files will be replaced.


## Installation

> You have two ways to run setup - 1, the Setup command in step #2 (below) or 2, the `vendor:publish` command. I recommend the `passkey:install` command since it takes care of replacing some placeholders in the published files apart from publishing them. Use the `--table` and `--username` flags to change the defaults. These values default to `users` and `email` respectively. If using the `vendor:publish` command you'll need to replace `__USERNAME__`, `__TABLE__`, `__USERNAME_LABEL__` and the `__USERNAME_TYPE__` placeholders manually. These placeholders can be found in the `js/Components/ConfirmsPasswordOrPasskey.vue`, `js/Components/ConfirmsPasskey.vue`, `js/Pages/Auth/LoginWithPasskey.vue` and the `config/passkey.php` files.

1. Require the library

    `composer install pioneer-dynamics/laravel-passkey`

2. Run the setup

    > Before running this command, run `php artisan passkey:install --help` to see understand all options.

    `php artisan passkey:install`

    > This will publish the config files, migrations and some Jetstream-Inertia flavoured vue files. It will also replace some contents of these published files.

3. Run the migrations

    `php artisan migrate`

4. Implement the `PasskeyUser` interface to you user model and add the `HasPasskeys` trait

    ```php
    <?php

    namespace App\Models;

    use PioneerDynamics\LaravelPasskey\Traits\HassPasskeys;
    use PioneerDynamics\LaravelPasskey\Contracts\PasskeyUser;
    // ...
    use Illuminate\Foundation\Auth\User as Authenticatable;
    // ...

    class User extends Authenticatable implements PasskeyUser
    {
        // ...
        use HasPasskeys;

        // ...

        /**
         * If you wish to not use `HasPassKeys` trait, you must eager load `passkeys`
         * attribute like this:
         * 
         */

        // protected $with = ['passkeys'];

        /**
         * In addition to some helper methods, the below methods are defined
         * in the `HasPasskeys` trait. Override them here if needed. 
         * 
         * Below are the default definitions in `HasPasskeys`
         */

        // public function getUsername()
        // {
        //      // The username of the user (Shown by the browser passkey interface)
        //      return $this->email;
        // }
        // 
        // public function getUserId()
        // {
        //      // The ID of the user
        //      return $this->id;
        // }
        // 
        // public function getDisplayName()
        // {
        //      // The display name of the user
        //      return $this->name;
        // }
        // 
        // public function getUserIcon()
        // {
        //      // The display picture of the user.
        //      // This MUST be a data URI.
        //      // E.g.
        //      // return 'data:image/svg+xml;utf8,...'
        //     return null;
        // }

        // ...
    }
    ```

5. If you are using Laravel Jetstream with InertiaJS, you can can add the below to the `FortifyServiceProvider`'s `boot()` command in order to override the default Login Vue and use the one provided by this package. 

    ```php
    <?php

    namespace App\Providers;

    // ...

    use Inertia\Inertia;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\ServiceProvider;
    use Laravel\Fortify\Fortify;

    // ...

    class FortifyServiceProvider extends ServiceProvider
    {
        // ...

        /**
        * Bootstrap any application services.
        */
        public function boot(): void
        {
            // ...

            Fortify::loginView(function () {
                return Inertia::render('Auth/LoginWithPasskey', [
                    'canResetPassword' => Route::has('password.request'),
                    'status' => session('status'),
                ]);
            });

            // ...
        }
    }
    ```

6. Replace the `<ConfirmsPassword/>` component with `<ConfirmsPasswordOrPasskey/>` component whereever `<ConfirmsPassword/>` is used. The ``<ConfirmsPasswordOrPasskey/>` optionally accepts a `:seconds` property which defines how long a validation should be valid for. Should you chose to use it, there should also be a corresponding `password.confirm` middleware for the route.

7. Add `<PasskeyForm/>` from `resources/js/Pages/Profile/Partials/PassKeyForm.vue` to `resources/js/Pages/Profile/Show.vue`. This is where users will manage their passkeys.

8. If using Jetstream-InertiaJS the below packages are needed are installed automatically if the command was called with the `--jetstream-inertia` option.
    
    1. npm i luxon
    1. npm i @simplewebauthn/browser
    

## Issues

Feel free to raise any [Issue](https://github.com/pioneer-dynamics/laravel-passkey/issues) here.

## Licence

The MIT License (MIT) Copyright © Mathew Paret

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
