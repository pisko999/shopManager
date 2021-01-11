<?php


namespace App\Objects;


use App\Models\Item;
use Codedge\Fpdf\Fpdf\Fpdf;

class pdfFacture extends FPDF
{
    private $facture;

    function show($facture)
    {
        $this->facture = $facture;

        $this->newPage();

        $this->SetFont('Arial', '', 12);
        $this->showItemsTable($this->facture->items);
    }

    private function newPage()
    {
        $this->AddPage();

        $this->showHeader();

        $this->SetXY(10, 90);
        $this->SetFont('Arial', '', 12);
        $this->showTableHead();

    }

    private function showHeader()
    {


        $this->SetFont('Arial', '', 12);
        $this->SetXY(10, 10);
        $this->showHead();

        $this->SetXY(125, 10);
        $this->showFactureHead();

        $this->SetXY(10, 50);
        $this->SetFont('Arial', '', 10);
        $this->showAddress($this->getAddressString($this->facture->storekeeper->address), 6);

        $this->SetXY(125, 50);
        $this->SetFont('Arial', '', 12);
        $this->showAddress($this->getAddressString($this->facture->billing_address), 6);

    }

    private function showAddress($address, $h)
    {
        $this->MultiCell(95, $h, iconv('UTF-8', 'windows-1252', $address), 0, "L");
    }

    private function showHead()
    {
        $head = $this->getHeadString();
        $this->SetFont('Arial', '', 24);

        $this->MultiCell(95, 20, iconv('UTF-8', 'windows-1252', $head), 0, "L");

    }

    private function showFactureHead()
    {
        $this->SetFont('Arial', '', 12);

        $factureHead = $this->getFactureHeadString($this->facture);
        $this->MultiCell(95, 8, iconv('UTF-8', 'windows-1252', $factureHead), 0, "L");

    }

    private function showShipping()
    {

        $this->cell(130, 5, $this->facture->shippingMethod != null ? $this->facture->shippingMethod->method->name : "", "L");
        $this->cell(20, 5, $this->facture->shippingMethod != null ? ($this->facture->shippingMethod->is_letter ? "Letter" : "Online"): "", "L", 0, "C");
        $this->cell(20, 5, $this->facture->shippingMethod != null ? ($this->facture->shippingMethod->is_insured ? "Insured" : ""): "", "L", 0, "C");
        $this->cell(20, 5, $this->facture->shippingMethod != null ?$this->facture->shippingMethod->price: "", "LR", 1, "C");
        $this->cell(0, 0, '', 1, 1);

    }

    public function showTotal()
    {
        $this->cell(130, 5, "", 0);
        $this->cell(20, 5, "", 0);
        $this->cell(20, 5, "Total:", "LB", 0, "C");
        $this->cell(20, 5, $this->facture->total_value, "LRB", 1, "C");
    }

    private function showItemsTable()
    {
        $i = 0;
        foreach ($this->facture->items as $item) {
            $i++;
            $this->showItemRow($item);
            if ($i == 33) {
                $this->cell(0, 0, '', 1, 1);
                $this->showFooter();
                $this->newPage();
                $i = 0;
            }
        }
        for (; $i <= 32; $i++)
            $this->showItemRow(null);
        $this->cell(0, 0, '', 1, 1);

        $this->showShipping();
        $this->showTotal();
    }

    private function showTableHead()
    {
        $this->cell(15, 6, "Exp.", 1);
        $this->cell(100, 6, "Product", 1);
        $this->cell(15, 6, "Cond.", 1);
        $this->cell(15, 6, "Extra", 1);
        $this->cell(15, 6, "P./U.", 1);
        $this->cell(15, 6, "Quant.", 1);
        $this->cell(15, 6, "Price", 1, 1);
    }

    private function showItemRow($item)
    {
        $this->cell(15, 5, $item != null && $item->stock->product->expansion != null ? $item->stock->product->expansion->sign : "", "L");
        $this->cell(100, 5, $item != null ? $item->stock->product->name : "", "L");
        $this->cell(15, 5, $item != null ? $item->stock->state : "", "L", 0, "C");
        $this->cell(15, 5, $item != null ? ($item->stock->isFoil ? "F" : "") .
            ($item->stock->playset ? "P" : "") .
            ($item->stock->altered ? "A" : "") .
            ($item->stock->signed ? "S" : "") : "", "L", 0, "C");
        $this->cell(15, 5, $item != null ? $item->price : "", "L", 0, "C");
        $this->cell(15, 5, $item != null ? $item->quantity : "", "L", 0, "C");
        $this->cell(15, 5, $item != null ? $item->price * $item->quantity : "", "LR", 1, "C");
    }

    private function getAddressString($address)
    {
        return $address != null ?
            ($address->name . "\n"
                . ($address->extra != null ? $address->extra . "\n" : '')
                . $address->street . ($address->number != null && $address->flat != null
                    ? $address->number . "/" . $address->flat
                    : ($address->number != null
                        ? $address->number
                        : ($address->flat != null
                            ? " flat " . $address->flat . "\n"
                            : "\n")))
                . $address->postal . "   " . $address->city . "\n"
                . Countries::getCountryByCode($address->country) . "\n"
                . ($address->extra == null ? "\n" : "")) :
            "";
    }

    private function getHeadString()
    {
        return "www.MtgForFun.cz\n";
    }

    private function getFactureHeadString()
    {
        return "F" . substr($this->facture->status->date_paid, 2, 2) . "" . str_pad($this->facture->id, 5, "0", STR_PAD_LEFT) . "\n"
            . "Ordered:\t" . substr($this->facture->status->date_bought, 0, 10) . "\n"
            . "Paid:\t" . substr($this->facture->status->date_paid, 0, 10) . "\n\n";
    }

    private function showFooter()
    {
        $this->cell(0, 5, "page", 0, 1, "C");
    }

}

