# 📧 Disposable for Laravel

[![Laravel Package](https://img.shields.io/badge/Laravel%2010+%20Package-red?logo=laravel&logoColor=white)](https://www.laravel.com)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/incentads/disposable.svg?style=flat-square)](https://packagist.org/packages/cristianpeter/laravel-disposable-contact-guard)


Forked package from [Propaganistas/Laravel-Disposable-Email](https://github.com/Propaganistas/Laravel-Disposable-Email)
- Adds a validator to Laravel for checking whether a given email address isn't originating from disposable email services such as `Mailinator`, `Guerillamail`, ...
Uses the disposable domains blacklist from [disposable/disposable](https://github.com/disposable/disposable) by default.


- Adds a validator to Laravel for checking whether a given phone number (E.164format) isn't origin from disposable phone services.
Used the disposable number list from [disposable-number](https://github.com/iP1SMS/disposable-phone-numbers)

Have the option for using numcheckr service for backup source

### Numcheckr integration
Numcheckr is a service that provides an api for checking if a phone number is disposable or not. You can use this service as the main source for checking disposable phone numbers.

To enable this feature, you need to create an account on [numcheckr](https://numcheckr.com) and get an api key. Then add the api key to the `numcheckr` configuration value.

```php
        'integrations' => [
            'numcheckr' =>  [
                'url' => env('NUMCHECKR_URL', 'https://numcheckr.com/api/check-number'),
                'api_key' => env('NUMCHECKR_API_KEY', ''),
            ]
        ]
```

### Installation

1. Run the Composer require command to install the package. The service provider is discovered automatically.

    ```bash
    composer require incentads/disposable
    ```

2. Publish the configuration file and adapt the configuration as desired:

    ```bash
    php artisan vendor:publish --tag=disposable-config
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

Use the `disposable` validator to ensure a given field doesn't hold a disposable email address. You'll probably want to add it after the `email` validator to make sure a valid email is passed through:

```php
'field' => 'email|disposable_email',
```
Use the `real_phone` validator to ensure a given field doesn't hold a disposable number.
```php
'field' => 'disposable_phone',
```

### Custom fetches

By default, the package retrieves a new list by using `file_get_contents()`. 
If your application has different needs (e.g. when behind a proxy) please review the `fetcher` configuration value.

## 🙏 Credits

This project is developed and maintained by [Cristian Peter](https://github.com/CristianPeter) and contributors.

Special thanks to:

- [Laravel Framework](https://laravel.com/) for providing the most exciting and well-crafted PHP framework.
- [Propaganistas](https://github.com/Propaganistas) for developing the [initial code](https://github.com/Propaganistas/Laravel-Disposable-Email) that serves Sentinel as starting point.
- All the contributors and testers who have helped to improve this project through their contributions.

If you find this project useful, please consider giving it a ⭐ on GitHub!