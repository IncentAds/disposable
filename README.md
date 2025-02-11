# Laravel Disposable Contact Guard

Forked package from [Propaganistas/Laravel-Disposable-Email](https://github.com/Propaganistas/Laravel-Disposable-Email)
- Adds a validator to Laravel for checking whether a given email address isn't originating from disposable email services such as `Mailinator`, `Guerillamail`, ...
Uses the disposable domains blacklist from [disposable/disposable](https://github.com/disposable/disposable) by default.

- Adds a validator to Laravel for checking wheter a given phone number(E.164format) isn't origin from disposable phone services..
Uses the disposable number list from [disposable-number](https://github.com/Propaganistas/Laravel-Phone)

Have the option for using numcheckr service for backup source

### Installation

1. Run the Composer require command to install the package. The service provider is discovered automatically.

    ```bash
    composer require cristianpeter/laravel-disposable-contact-guard
    ```

2. Publish the configuration file and adapt the configuration as desired:

    ```bash
    php artisan vendor:publish --tag=laravel-disposable-guard
    ```

3. Run the following artisan command to fetch an up-to-date list of disposable domains:
    
    ```bash
    php artisan disposable:update
    ```
4. Run the following artisan command to fetch an up-to-date list of disposable numbers:
    
    ```bash
    php artisan disposable-numbers:update
    ```


5. (optional) It's highly advised to update the disposable domains list regularly. You can either run the command yourself now and then or, if you make use of Laravel's scheduler, you can register the `disposable:update` command: 

   In `routes/console.php`:
    ```php
    use Illuminate\Support\Facades\Schedule;
    
    Schedule::command('disposable:update')->weekly();
    Schedule::command('disposable-numbers:update')->weekly();

    ```

    Or if you use Laravel 10 or below, head over to the Console kernel:
   ```php
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('disposable:update')->weekly();
        $schedule->command('disposable-numbers:update')->weekly();
       
    }
    ```
### Usage

Use the `indisposable` validator to ensure a given field doesn't hold a disposable email address. You'll probably want to add it after the `email` validator to make sure a valid email is passed through:

```php
'field' => 'email|indisposable',
```
Use the `real_phone` validator to ensure a given field doesn't hold a disposable number.
```php
'field' => 'real_phone',
```

### Custom fetches

By default the package retrieves a new list by using `file_get_contents()`. 
If your application has different needs (e.g. when behind a proxy) please review the `fetcher` configuration value.
