# laravel-invoicable

![PHP Composer](https://github.com/neptunesoftware/laravel-invoicable/workflows/PHP%20Composer/badge.svg)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

This package has been changed, updated and redistributed.
To utilize original distribution, see [sandervanhooft/laravel-invoicable](https://github.com/sandervanhooft/laravel-invoicable)
repository.

**IMPORTANT**
> This fork is going to be maintained by @neptunesoftware and 
> it's not compatible with original repository.

Easy invoice creation for Laravel. Unlike Laravel Cashier, this package is payment gateway agnostic.

## What is different?

In order to follow changes, see [changelog](CHANGELOG.md) file. Basically, this package will be updated and maintained
by Neptune Software.

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
$ composer require neptunesoftware/laravel-invoicable
```

Next, you must install the service provider if you work with Laravel 5.4:

``` php
// config/app.php
'providers' => [
    ...
    NeptuneSoftware\Invoicable\Providers\InvoicableServiceProvider::class,
];
```

You can publish the migration with:

``` bash
$ php artisan vendor:publish --provider="NeptuneSoftware\Invoicable\Providers\InvoicableServiceProvider" --tag="migrations"
```

After the migration has been published you can create the invoices and invoice_lines tables by running the migrations:

``` bash
$ php artisan migrate
```

Optionally, you can also publish the `invoicable.php` config file with:

``` bash
$ php artisan vendor:publish --provider="NeptuneSoftware\Invoicable\Providers\InvoicableServiceProvider" --tag="config"
```

This is what the default config file looks like:

``` php

return [
    'default_currency' => 'TRY',
    'default_status' => 'concept',
    'locale' => 'tr_TR',
];
```

If you'd like to override the design of the invoice blade view and pdf, publish the view:

``` bash
$ php artisan vendor:publish --provider="NeptuneSoftware\Invoicable\Providers\InvoicableServiceProvider" --tag="views"
```

You can now edit `receipt.blade.php` in `<project_root>/resources/views/invoicable/receipt.blade.php` to match your style.


## Usage

__Money figures are in cents!__

Add the invoicable trait to the Eloquent model which needs to be invoiced (typically an Order model):

``` php
use Illuminate\Database\Eloquent\Model;
use NeptuneSoftware\Invoicable\IsInvoicable\InvoicableTrait;

class Order extends Model
{
    use InvoicableTrait; // enables the ->invoices() Eloquent relationship
}
```

Now you can create invoices for an Order:


``` php
$customer = Customer::first();
$product = Product::first(); // Any model to be referenced in an invoice line
$service = $service->create($customer); // Injected dependency 

// To add a line to the invoice, use these example parameters:
//  Amount:
//      121 (€1,21) incl tax
//      100 (€1,00) excl tax
//  Description: 'Some description'
//  Tax percentage: 0.21 (21%)
$service->setReference($product)->addAmountInclTax(121, 'Some description', 0.21);
$service->setReference($product)->addAmountExclTax(100, 'Some description', 0.21);

// Invoice totals are now updated
$invoice = $service->getInvoice();
echo $invoice->total; // 242
echo $invoice->tax; // 42

// Set additional information (optional)
$invoice->currency; // defaults to 'TRY' (see config file)
$invoice->status; // defaults to 'concept' (see config file)
$invoice->receiver_info; // defaults to null
$invoice->sender_info; // defaults to null
$invoice->payment_info; // defaults to null
$invoice->note; // defaults to null

// access individual invoice lines using Eloquent relationship
$service->lines;
$service->lines();

// Access as pdf
$service->download(); // download as pdf (returns http response)
$service->pdf(); // or just grab the pdf (raw bytes)

// Handling discounts
// By adding a line with a negative amount.
$invoice = $invoice->setReference($product)->addAmountInclTax(-121, 'A nice discount', 0.21);

// Or by applying the discount and discribing the discount manually
$invoice = $invoice->setReference($product)->addAmountInclTax(121 * (1 - 0.30), 'Product XYZ incl 30% discount', 0.21);

// Convenience methods
$service->findByReference($reference);
$service->findByReferenceOrFail($reference);
$service->invoicable() // Access the related model
```


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email info@neptunyazilim.com instead of using the issue tracker.

## Credits
- [Burak](https://github.com/ikidnapmyself)
- [Fatih](https://github.com/kablanfatih)
- [Uğur](https://github.com/ugurdnlr)
- [Sander van Hooft](https://github.com/sandervanhooft)
- [All Contributors][link-contributors]
- Inspired by [Laravel Cashier](https://github.com/laravel/cashier)'s invoices.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/neptunesoftware/laravel-invoicable.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[ico-downloads]: https://img.shields.io/packagist/dt/neptunesoftware/laravel-invoicable.svg

[link-packagist]: https://packagist.org/packages/neptunesoftware/laravel-invoicable
[link-downloads]: https://packagist.org/packages/neptunesoftware/laravel-invoicable
[link-contributors]: ../../contributors
