<?php


namespace NeptuneSoftware\Invoicable\Services;

use Dompdf\Dompdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use NeptuneSoftware\Invoicable\Invoice;
use NeptuneSoftware\Invoicable\MoneyFormatter;
use NeptuneSoftware\Invoicable\Services\Interfaces\InvoiceServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class InvoiceService implements InvoiceServiceInterface
{
    /**
     * @var Invoice $invoiceModel
     */
    private $invoiceModel;

    /**
     * @var Model $reference
     */
    private $reference;

    /**
     * RoleService constructor.
     * @param Invoice $invoiceModel
     */
    public function __construct(Invoice $invoiceModel)
    {
        $this->invoiceModel = $invoiceModel;
    }

    /**
     * @inheritDoc
     */
    public function setReference(Model $model): InvoiceServiceInterface
    {
        $this->reference = $model;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addAmountExclTax(
        $amount,
        $description,
        $taxPercentage = 0
    ): Invoice {
        $tax = $amount * $taxPercentage;

        $this->invoiceModel->lines()->create([
            'amount'         => $amount + $tax,
            'description'    => $description,
            'tax'            => $tax,
            'tax_percentage' => $taxPercentage,
            'reference_id'   => $this->reference->id,
            'reference_type' => get_class($this->reference),
        ]);
        return $this->recalculate();
    }

    /**
     * @inheritDoc
     */
    public function addAmountInclTax(
        $amount,
        $description,
        $taxPercentage = 0
    ): Invoice {
        $this->invoiceModel->lines()->create([
            'amount'         => $amount,
            'description'    => $description,
            'tax'            => $amount - $amount / (1 + $taxPercentage),
            'tax_percentage' => $taxPercentage,
            'reference_id'   => $this->reference->id,
            'reference_type' => get_class($this->reference),
        ]);

        return $this->recalculate();
    }

    /**
     * @inheritDoc
     */
    public function recalculate(): Invoice
    {
        $this->invoiceModel->total = $this->invoiceModel->lines()->sum('amount');
        $this->invoiceModel->tax = $this->invoiceModel->lines()->sum('tax');
        $this->invoiceModel->save();
        return $this->invoiceModel;
    }

    /**
     * @inheritDoc
     */
    public function view(array $data = []): \Illuminate\Contracts\View\View
    {
        return View::make('invoicable::receipt', array_merge($data, [
            'invoice' => $this->invoiceModel,
            'moneyFormatter' => new MoneyFormatter(
                $this->invoiceModel->currency,
                config('invoicable.locale')
            ),
        ]));
    }

    /**
     * @inheritDoc
     */
    public function pdf(array $data = []): string
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
     * @inheritDoc
     */
    public function download(array $data = []): Response
    {
        $filename = $this->invoiceModel->reference . '.pdf';

        return new Response($this->pdf($data), 200, [
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function findByReference(string $reference): ?Invoice
    {
        return Invoice::where('reference', $reference)->first();
    }

    /**
     * @inheritDoc
     */
    public static function findByReferenceOrFail(string $reference): Invoice
    {
        return Invoice::where('reference', $reference)->firstOrFail();
    }
}
