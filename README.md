# laravel-invoicable

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

DESCRIPTION: __WORK IN PROGRESS!__

## Structure

```
database/
resources
src/
tests/
vendor/
```

## Install

Via Composer

``` bash
$ composer require sander-van-hooft/laravel-invoicable
```

Next, you must install the service provider:

``` php
// config/app.php
'providers' => [
    ...
    SanderVanHooft\PayableRedirect\InvoicableServiceProvider::class,
];
```

You can publish the migration with:

``` bash
$ php artisan vendor:publish --provider="SanderVanHooft\PayableRedirect\InvoicableServiceProvider" --tag="migrations"
```

After the migration has been published you can create the invoices and invoice_lines tables by running the migrations:

``` bash
$ php artisan migrate
```

## Usage

__Money figures are in cents!__

Add the invoicable trait to the Eloquent model which needs to be invoiced (typically an Order model):

``` php
use Illuminate\Database\Eloquent\Model;
use SanderVanHooft\Invoicable\IsInvoicable\IsInvoicableTrait;

class Order extends Model
{
    use IsInvoicableTrait; // enables the ->invoices() Eloquent relationship
}
```

Now you can create invoices for an Order:
``` php
$order = new Order();
$invoice = $order->invoices()->create([]);

// To add a line to the invoice, use these example parameters:
//  Amount:
//      121 (€1,21) incl tax
//      100 (€1,00) excl tax
//  Description: 'Some description'
//  Tax percentage: 0.21 (21%)
$invoice = $invoice->addAmountInclTax(121, 'Some description', 0.21);
$invoice = $invoice->addAmountExclTax(100, 'Some description', 0.21);

// Invoice totals are now updated
echo $invoice->total; // 242
echo $invoice->tax; // 42

// Set additional information (optional)
$invoice->currency; // defaults to 'EUR' (see config file)
$invoice->status; // defaults to 'concept' (see config file)
$invoice->receiver_information; // defaults to null
$invoice->sender_information; // defaults to null
$invoice->payment_information; // defaults to null
$invoice->note; // defaults to null

// access individual invoice lines using Eloquent relationship
$invoice->lines;
$invoice->lines();
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
[ ] blade view
[ ] pdf generation (based on blade view)

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
