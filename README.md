# laravel-invoicable

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what
PSRs you support to avoid any confusion with users and contributors.

## Structure

If any of the following are applicable to your project, then the directory structure should follow industry best practises by being named the following.

```
bin/        
config/
src/
tests/
vendor/
```


## Install

Via Composer

``` bash
$ composer require sander-van-hooft/laravel-invoicable
```

## Usage

``` php
$skeleton = new SanderVanHooft\Invoicable();
echo $skeleton->echoPhrase('Hello, League!');
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

### To do's:
- blade view
- invoice line discounts (amount / percentage)
- invoice total discounts (amount / percentage)
- pdf generation

## Security

If you discover any security related issues, please email info@sandervanhooft.nl instead of using the issue tracker.

## Credits

- [Sander van Hooft][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/sandervanhooft/laravel-invoicable.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/sandervanhooft/laravel-invoicable/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/sandervanhooft/laravel-invoicable.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/sandervanhooft/laravel-invoicable.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/sandervanhooft/laravel-invoicable.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/sandervanhooft/laravel-invoicable
[link-travis]: https://travis-ci.org/sandervanhooft/laravel-invoicable
[link-scrutinizer]: https://scrutinizer-ci.com/g/sandervanhooft/laravel-invoicable/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/sandervanhooft/laravel-invoicable
[link-downloads]: https://packagist.org/packages/sandervanhooft/laravel-invoicable
[link-author]: https://github.com/sandervanhooft
[link-contributors]: ../../contributors
