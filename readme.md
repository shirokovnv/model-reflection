# ModelReflection

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

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
    $user_schema = ModelReflection::make(\App\Models\User::class);
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

coming soon...

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
[ico-travis]: https://img.shields.io/travis/shirokovnv/model-reflection/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/shirokovnv/model-reflection
[link-downloads]: https://packagist.org/packages/shirokovnv/model-reflection
[link-travis]: https://travis-ci.org/shirokovnv/model-reflection
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/shirokovnv
[link-contributors]: ../../contributors
