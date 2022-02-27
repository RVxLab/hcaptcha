# Upgrading

## From 4.x to 5.x

### Support

- Laravel 7 and below are no longer supported
- PHP 7.3 and below are no longer supported

### Validation messages

Validation messages are now located in their own translation files.

You can remove your entries in `validation.php` and make sure to run `php artisan vendor:publish tag="hcaptcha-lang"`.
