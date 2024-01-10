<?php


namespace App\Objects;


use App\Models\Item;
use Codedge\Fpdf\Fpdf\Fpdf;

class pdfFacture extends FPDF
{
    private $facture;
    private $centerPage = 115;

    private $perPage = 27;

    private $wrongChars = ["ě","č", "Č", "ř", "ų"];
    private $rightChars = ["e", "c", "C", "r", "u"];

    private $tva = [
        0 => 0,
        21 => 0
    ];
    function show($facture)
    {
        $this->facture = $facture;
        $this->tva[0] = 0;
        $this->tva[21] = 0;

        $this->newPage();

        $this->SetFont('Arial', '', 12);
        $this->showItemsTable($this->facture->items);
        $this->SetFont('Arial', '', 12);

        $this->newThankPage();

    }

    private function newThankPage(){
        $this->AddPage();

        $firstLine = 2;

        $this->printCenteredText("Thank you for",$firstLine + 1);
        $this->printCenteredText("your purchase.",$firstLine + 2);
        $this->printCenteredText("Try my site",$firstLine + 4);
        $this->printCenteredText("MtgForFun.cz",$firstLine + 5);
        $this->printCenteredText("is under construction, but working",$firstLine + 6);
        if($this->facture->buyer->mkm_username != null) {
            $this->SetFont('Arial', '', 12);
            $this->printCenteredText("For account just contact me", $firstLine + 12);
            $this->printCenteredText("by message on cardmarket", $firstLine + 13);
            $this->printCenteredText("or email me", $firstLine + 14);
            $this->printCenteredText("mtg@mtgforfun.cz", $firstLine + 15);
        }

        if (count($this->facture->gifts)) {
            $this->printCenteredText("MERRY CHRISTMAS", $firstLine + 20);
            $this->printCenteredText("It is Christmas time And I want to celebrate it with you :)", $firstLine + 22);
            $this->printCenteredText("I opened one Kaldheim Set Booster box,", $firstLine + 24);
            $this->printCenteredText("and someone will receive BETA Forest", $firstLine + 26);
            $this->printCenteredText("Your randomly chosen card are: ", $firstLine + 28);


        }
        $i = 0;
        foreach($this->facture->gifts as $gift) {
            foreach($gift->giftItems as $giftItem) {
                $i++;
                $this->printCenteredText($giftItem->product->name, $firstLine + 29 + $i);
            }
        }

        if (count($this->facture->gifts)) {
            $this->printCenteredText("Video from unboxing", $firstLine + 32 + $i, 55);
            $this->Image(getcwd() . '/storage/christmas_youtube_2023.png',$this->centerPage - 80,6 * ($firstLine + 33 + $i), 50,50);
            $this->printCenteredText("Whole list of cards", $firstLine + 32 + $i, -45);
            $this->Image(getcwd() . '/storage/giftlist2.png',$this->centerPage + 20,6 * ($firstLine + 33 + $i), 50,50);
        }


    }

    private function printCenteredText($text, $line, $left = 0){
        $this->text($this->centerPage - $this->GetStringWidth($text) / 2 - $left,6 * $line, $text);

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

        $this->SetXY(10, 30);
        $this->SetFont('Arial', '', 10);
        $this->showAddress($this->getAddressString($this->facture->storekeeper->address), 5);
        $this->SetFont('Arial', '', 8);
        $this->text(11,60,"IC: 17662672");
        $this->text(11,64,"DIC (VAT ID): CZ17662672");
        $this->text(11,68,"IBAN: CZ4720100000002702375690");
        $this->text(11,72,"BIC: FIOBCZPPXXX");

        $this->SetXY(125, 50);
        $this->SetFont('Arial', '', 12);
        $this->showAddress($this->getAddressString($this->facture->billing_address), 6);
        if ($this->facture->client->mkm_is_commercial) {
            $this->cell(115,6);
            $this->cell(50,6, 'VAT ID: ' . $this->facture->client->vat_id);
        }

    }

    private function showAddress($address, $h)
    {
        try {
            $this->MultiCell(85, $h, iconv('UTF-8', 'windows-1250', str_replace($this->wrongChars, $this->rightChars, $address)), 0, "L");
        } catch (\Exception $e) {
            error_log($address);
            $this->MultiCell(85, $h, iconv('UTF-8', 'windows-1252', str_replace($this->wrongChars, $this->rightChars, $address)), 0, "L");
        }
    }

    private function showHead()
    {
        $this->Image(getcwd() . '/storage/title-black.png',10,10, 50,12);
//        $head = $this->getHeadString();
//        $this->SetFont('Arial', '', 24);
//
//        $this->MultiCell(95, 20, iconv('UTF-8', 'windows-1252', str_replace(["ě","č", "Č", "ř"], ["e", "c", "C", "r"], $head)), 0, "L");

    }

    private function showFactureHead()
    {
        $this->SetFont('Arial', '', 12);

        $factureHead = $this->getFactureHeadString($this->facture);
        $this->MultiCell(95, 8, iconv('UTF-8', 'windows-1252', str_replace($this->wrongChars, $this->rightChars, $factureHead)), 0, "L");

    }

    private function showShipping()
    {

        $this->cell(130, 5, $this->facture->shippingMethod != null ? $this->facture->shippingMethod->method->name : "", "L");
        $this->cell(20, 5, $this->facture->shippingMethod != null ? ($this->facture->shippingMethod->is_letter ? "Letter" : "Online") : "", "L", 0, "C");
        $this->cell(20, 5, $this->facture->shippingMethod != null ? ($this->facture->shippingMethod->is_insured ? "Insured" : "") : "", "L", 0, "C");
        $this->cell(20, 5, $this->facture->shippingMethod != null ? $this->facture->shippingMethod->price : "", "LR", 1, "C");
        $this->tva[21] += $this->facture->shippingMethod->price;
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
        $page = 0;
        foreach ($this->facture->items as $item) {
            $i++;
            $this->showItemRow($item);
            $key = $item->stock->is_new ? 21 : 0;
            $this->tva[$key] += $item->quantity * $item->price;
            if ($i == $this->perPage) {
                $page++;
                $this->cell(0, 0, '', 1, 1);
                $this->showFooter($page, ceil($this->facture->items->count()/$this->perPage));
                $this->newThankPage();
                $this->newPage();
                $i = 0;
            }
        }
        for (; $i < $this->perPage; $i++)
            $this->showItemRow(null);
        $this->cell(0, 0, '', 1, 1);

        $this->showShipping();
        $this->showTotal();
        $this->showTVATable();
    }

    private function showTVATable(){
        if ($this->tva[0] > 0) {
            $this->cell(20, 5, 'Order contains used items sold under', 0, 1);
            $this->cell(20, 5, iconv('UTF-8', 'windows-1252','Special regime according to § 90 TAX law for taxation of surcharge in Czechia'), 0, 1);
            $this->cell(0, 5, '', 0, 1);
        }
        $this->cell(20, 5, 'TVA %', 1);
        $this->cell(20, 5, 'Base', 1);
        $this->cell(25, 5, 'TVA value', 1, 1);
        foreach($this->tva as $tva => $value) {
            $this->cell(20, 5, $tva . '%', 1);
            $this->cell(20, 5, $value, 1);
            $this->cell(25, 5, round($value / 121 * $tva, 2), 1, 1);

        }
    }

    private function showTableHead()
    {
        $this->cell(15, 6, "Exp.", 1);
        $this->cell(85, 6, "Product", 1);
        $this->cell(15, 6, "Cond.", 1);
        $this->cell(15, 6, "Extra", 1);
        $this->cell(15, 6, "P./U.", 1);
        $this->cell(15, 6, "Quant.", 1);
        $this->cell(15, 6, "TVA", 1);
        $this->cell(15, 6, "Price", 1, 1);
    }

    private function showItemRow($item)
    {
        $this->cell(15, 5, ($item != null && isset($item->stock->product->expansion) && $item->stock->product->expansion != null) ? $item->stock->product->expansion->sign : "", "L");
        $this->cell(85, 5, $item != null && isset($item->stock->product) ? $item->stock->product->name : "", "L");
        $this->cell(15, 5, $item != null ? $item->stock->state : "", "L", 0, "C");
        $this->cell(15, 5, $item != null ? ($item->stock->isFoil ? "F" : "") .
            ($item->stock->playset ? "P" : "") .
            ($item->stock->altered ? "A" : "") .
            ($item->stock->signed ? "S" : "") : "", "L", 0, "C");
        $this->cell(15, 5, $item != null ? $item->price : "", "L", 0, "C");
        $this->cell(15, 5, $item != null ? $item->quantity : "", "L", 0, "C");
        $this->cell(15, 5, $item != null ? $item->stock->is_new ? "21%" : "0%" : "", "L", 0, "C");
        $this->cell(15, 5, $item != null ? $item->price * $item->quantity : "", "LR", 1, "C");
    }

    private function getAddressString($address)
    {
        return $address != null ?
            ($address->name . "\n"
                . ($address->extra != null ? $address->extra . "\n" : '')
                . $address->street . ($address->number != null && $address->flat != null
                    ? $address->number . "/" . $address->flat . "\n"
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
        return ($this->facture->idOrderMKM ? "F" : "FS") . $this->facture->invoice_no . "\n"
            . "Ordered:\t" . substr($this->facture->status->date_bought, 0, 10) . "\n"
            . "Paid:\t" . substr($this->facture->status->date_paid, 0, 10) . "\n\n";
    }

    private function showFooter($page, $count)
    {
        $this->cell(0, 5, "page " . $page . '/' . $count, 0, 1, "C");
    }

}

