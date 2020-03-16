<?php


namespace SanderVanHooft\Invoicable\Services;

use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;
use SanderVanHooft\Invoicable\Bill;
use SanderVanHooft\Invoicable\MoneyFormatter;
use SanderVanHooft\Invoicable\Services\Interfaces\BillServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class BillService implements BillServiceInterface
{
    /**
     * @var Bill
     */
    private $billModel;

    /**
     * RoleService constructor.
     * @param Bill $billModel
     */
    public function __construct(Bill $billModel)
    {
        $this->billModel = $billModel;
    }

    /**
     * @inheritDoc
     */
    public function addAmountExclTax(
        $amount,
        $description,
        $invoicable_id,
        $invoicable_type,
        $taxPercentage = 0
    ): Bill {
        $tax = $amount * $taxPercentage;

        $this->billModel->lines()->create([
            'amount' => $amount + $tax,
            'description' => $description,
            'tax' => $tax,
            'tax_percentage' => $taxPercentage,
            'invoicable_id' => $invoicable_id,
            'invoicable_type' => $invoicable_type,
        ]);
        return $this->recalculate();
    }

    /**
     * @inheritDoc
     */
    public function addAmountInclTax(
        $amount,
        $description,
        $invoicable_id,
        $invoicable_type,
        $taxPercentage = 0
    ): Bill {
        $this->billModel->lines()->create([
            'amount' => $amount,
            'description' => $description,
            'tax' => $amount - $amount / (1 + $taxPercentage),
            'tax_percentage' => $taxPercentage,
            'invoicable_id' => $invoicable_id,
            'invoicable_type' => $invoicable_type
        ]);

        return $this->recalculate();
    }

    /**
     * @inheritDoc
     */
    public function addAmountExclTaxWithAllValues(
        $amount,
        $description,
        $invoicable_id,
        $invoicable_type,
        $is_free,
        $is_complimentary,
        $taxPercentage = 0
    ): Bill {
        $tax = $amount * $taxPercentage;
        $this->billModel->lines()->create([
            'amount' => $amount + $tax,
            'description' => $description,
            'tax' => $tax,
            'tax_percentage' => $taxPercentage,
            'invoicable_id' =>  $invoicable_id,
            'invoicable_type' => $invoicable_type,
            'is_free'         => $is_free,
            'is_complimentary' => $is_complimentary
        ]);
        return $this->billModel;
    }

    /**
     * @inheritDoc
     */
    public function recalculate(): Bill
    {
        $this->billModel->total = $this->billModel->lines()->sum('amount');
        $this->billModel->tax = $this->billModel->lines()->sum('tax');
        $this->billModel->save();
        return $this->billModel;
    }

    /**
     * @inheritDoc
     */
    public function view(array $data = []): \Illuminate\Contracts\View\View
    {
        return View::make('invoicable::receipt', array_merge($data, [
            'invoice' => $this->billModel,
            'moneyFormatter' => new MoneyFormatter(
                $this->billModel->currency,
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
        $filename = $this->billModel->reference . '.pdf';

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
    public static function findByReference(string $reference): ?Bill
    {
        return Bill::where('reference', $reference)->first();
    }

    /**
     * @inheritDoc
     */
    public static function findByReferenceOrFail(string $reference): Bill
    {
        return Bill::where('reference', $reference)->firstOrFail();
    }
}
