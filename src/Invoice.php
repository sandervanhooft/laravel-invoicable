<?php

namespace SanderVanHooft\Invoicable;

use Dompdf\Dompdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use SanderVanHooft\Invoicable\Scopes\InvoiceScope;
use Symfony\Component\HttpFoundation\Response;

class Invoice extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoicable_id', 'invoicable_type', 'tax',  'total', 'discount', 'currency',
        'reference', 'status', 'receiver_info', 'sender_info', 'payment_info', 'note', 'is_bill'
    ];

    protected $guarded = [];

    public $incrementing = false;

    /**
     * Invoice constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('invoicable.table_names.invoices'));
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new InvoiceScope());
        static::creating(function ($model) {
            /**
             * @var \Illuminate\Database\Eloquent\Model $model
             */
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }

            $model->currency = config('invoicable.default_currency', 'EUR');
            $model->status = config('invoicable.default_status', 'concept');
            $model->reference = InvoiceReferenceGenerator::generate();
        });
    }

    /**
     * Get the invoice lines for this invoice
     */
    public function lines()
    {
        return $this->hasMany(InvoiceLine::class);
    }

    /**
     * Use this if the amount does not yet include tax.
     * @param Int $amount The amount in cents, excluding taxes
     * @param String $description The description
     * @param int $taxPercentage The tax percentage (i.e. 0.21). Defaults to 0
     * @param $invoicable_id
     * @param $invoicable_type
     * @return Illuminate\Database\Eloquent\Model  This instance after recalculation
     */
    public function addAmountExclTax(
        $amount,
        $description,
        $taxPercentage = 0,
        $invoicable_id,
        $invoicable_type
    ) {
        $tax = $amount * $taxPercentage;
        $this->lines()->create([
            'amount' => $amount + $tax,
            'description' => $description,
            'tax' => $tax,
            'tax_percentage' => $taxPercentage,
            'invoicable_id' =>  $invoicable_id,
            'invoicable_type' =>  $invoicable_type,
        ]);
        return $this->recalculate();
    }

    /**
     * Use this if the amount already includes tax.
     * @param Int $amount The amount in cents, including taxes
     * @param String $description The description
     * @param int $taxPercentage The tax percentage (i.e. 0.21). Defaults to 0
     * @param $invoicable_id
     * @param $invoicable_type
     * @return Illuminate\Database\Eloquent\Model  This instance after recalculation
     */
    public function addAmountInclTax(
        $amount,
        $description,
        $taxPercentage = 0,
        $invoicable_id,
        $invoicable_type
    ) {

        $this->lines()->create([
            'amount' => $amount,
            'description' => $description,
            'tax' => $amount - $amount / (1 + $taxPercentage),
            'tax_percentage' => $taxPercentage,
            'invoicable_id' =>  $invoicable_id,
            'invoicable_type' =>  $invoicable_type
        ]);

        return $this->recalculate();
    }

    /**
     * Recalculates total and tax based on lines
     * @return Illuminate\Database\Eloquent\Model  This instance
     */
    public function recalculate()
    {
        $this->total = $this->lines()->sum('amount');
        $this->tax = $this->lines()->sum('tax');
        $this->save();
        return $this;
    }

    /**
     * Get the View instance for the invoice.
     *
     * @param  array  $data
     * @return \Illuminate\View\View
     */
    public function view(array $data = [])
    {
        return View::make('invoicable::receipt', array_merge($data, [
            'invoice' => $this,
            'moneyFormatter' => new MoneyFormatter(
                $this->currency,
                config('invoicable.locale')
            ),
        ]));
    }

    /**
     * Capture the invoice as a PDF and return the raw bytes.
     *
     * @param  array  $data
     * @return string
     */
    public function pdf(array $data = [])
    {
        if (! defined('DOMPDF_ENABLE_AUTOLOAD')) {
            define('DOMPDF_ENABLE_AUTOLOAD', false);
        }

        if (file_exists($configPath = base_path().'/vendor/dompdf/dompdf/dompdf_config.inc.php')) {
            require_once $configPath;
        }

        $dompdf = new Dompdf;
        $dompdf->loadHtml($this->view($data)->render());
        $dompdf->render();
        return $dompdf->output();
    }

    /**
     * Create an invoice download response.
     *
     * @param  array  $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function download(array $data = [])
    {
        $filename = $this->reference . '.pdf';

        return new Response($this->pdf($data), 200, [
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Type' => 'application/pdf',
        ]);
    }

    public static function findByReference($reference)
    {
        return static::where('reference', $reference)->first();
    }

    public static function findByReferenceOrFail($reference)
    {
        return static::where('reference', $reference)->firstOrFail();
    }

    public function invoicable()
    {
        return $this->morphTo();
    }


    public function addAmountExclTaxWithAllValues(
        $amount,
        $description,
        $taxPercentage = 0,
        $invoicable_id,
        $invoicable_type,
        $is_free,
        $is_complimentary
    ) {
        $tax = $amount * $taxPercentage;
        $this->lines()->create([
            'amount' => $amount + $tax,
            'description' => $description,
            'tax' => $tax,
            'tax_percentage' => $taxPercentage,
            'invoicable_id' =>  $invoicable_id,
            'invoicable_type' => $invoicable_type,
            'is_free'         => $is_free,
            'is_complimentary' => $is_complimentary
        ]);
        return $this;
    }
}
