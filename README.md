# Laravel Payment Gateways

This package is a collection of online payment gateways that handle each gateway process

## Installation
You can install the package via composer:

```bash
composer require hsmfawaz/payment-gateways
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="payment-gateways-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="payment-gateways-config"
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [hisham fawaz](https://github.com/hsmfawaz)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
