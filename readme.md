# ModelReflection

![ci.yml][link-ci]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

The package allows you to reflect properties and methods of the model and save the received information in the form of a JSON schema.

## Installation

Via Composer

``` bash
$ composer require shirokovnv/model-reflection
```

## Usage

This package is based on Doctrine/DBAL

Once installed you can do stuff like this:

```php
    $user_schema = ModelReflection::reflect(\App\Models\User::class);
```

this will return ReflectedModel containing information about:
- class name
- table name
- fields
- relations
- table foreign keys
- scopes

or you can do: 

```php
    $user_schema->toArray();
```
this will return an associative array for JSON representation.

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email shirokovnv@gmail.com instead of using the issue tracker.

## Credits

- [Nickolai Shirokov][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/shirokovnv/model-reflection.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/shirokovnv/model-reflection.svg?style=flat-square

[link-ci]: https://github.com/shirokovnv/model-reflection/actions/workflows/ci.yml/badge.svg
[link-packagist]: https://packagist.org/packages/shirokovnv/model-reflection
[link-downloads]: https://packagist.org/packages/shirokovnv/model-reflection
[link-author]: https://github.com/shirokovnv
[link-contributors]: ../../contributors
