<?php

namespace App\Objects;

use App\Models\Address;
use App\Models\BuyCommand;
use App\Models\Command;
use App\Models\User;
use PHPUnit\Util\Exception;

class XmlPohoda
{
    private \DOMDocument $domDocument;

    private \DOMElement $document;

    public function __construct(\DOMDocument $domDocument)
    {
        $this->domDocument = $domDocument;
        $this->domDocument->formatOutput = true;
        $this->domDocument->appendChild($this->document = $this->domDocument->createElement('dat:dataPack'));
        $this->document->setAttribute('id', '122022');
        $this->document->setAttribute('ico', '17662672');
        $this->document->setAttribute('application', 'MtgForFun shop');
        $this->document->setAttribute('version', '2.0');
        $this->document->setAttribute('note', '122022');
        $this->document->setAttribute('xmlns:dat', 'http://www.stormware.cz/schema/version_2/data.xsd');
        $this->document->setAttribute('xmlns:typ', 'http://www.stormware.cz/schema/version_2/type.xsd');
    }

    public function getXml(string $filename = null): string
    {
        if (!$filename) {
            $filename = 'export.xml';
        }
        return $this->domDocument->save($filename);
    }

    public function addCommand(Command $command) {
        $this->document->appendChild($this->parseCommand($command));
    }

    public function addRebuy(BuyCommand $command) {
        $this->document->appendChild($this->parseRebuy($command));
    }

    public function addIntern(Command $command, &$amount) {
        $intern = $this->parseIntern($command, $amount);
        if ($intern) {
            $this->document->appendChild($intern);
        }
    }

    public function initCommands()
    {
        $this->document->setAttribute('xmlns:inv', 'http://www.stormware.cz/schema/version_2/invoice.xsd');
    }

    public function initAddress()
    {
        $this->document->setAttribute('xmlns:adb', 'http://www.stormware.cz/schema/version_2/addressbook.xsd');
    }


    public function initRebuys()
    {
        $this->document->setAttribute('xmlns:vch', 'http://www.stormware.cz/schema/version_2/voucher.xsd');
    }

    public function initIntern()
    {
        $this->document->setAttribute('xmlns:int', 'http://www.stormware.cz/schema/version_2/intDoc.xsd');
    }

    private function parseIntern(Command $command, &$totalAmount)
    {

        $address = $command->billing_address;
        if (is_null($address))
        {
            $address = $command->buyer->address;
        }
        if (is_null($address)) {
//            throw new Exception("faktura fakturacni adresu. id:" . $command->id);
        }

        $xmlCommand = $this->domDocument->createElement("dat:dataPackItem");
        $xmlCommand->setAttribute('id', 'INT' . $command->invoice_no);
        $xmlCommand->setAttribute('version', '2.0');
        $xmlCommand->appendChild($intern = $this->domDocument->createElement('int:intDoc'));
        $intern->setAttribute('version', '2.0');
        $intern->appendChild($internHeader = $this->domDocument->createElement('int:intDocHeader'));
        $internHeader->appendChild($this->domDocument->createElement('int:id', $command->invoice_no));
        $internHeader->appendChild($this->domDocument->createElement('int:date', date('Y-m-d', strtotime($command->status->date_paid))));
        $internHeader->appendChild($this->domDocument->createElement('int:dateTax', date('Y-m-d', strtotime($command->status->date_paid))));
        $internHeader->appendChild($this->domDocument->createElement('int:dateAccounting', date('Y-m-d', strtotime($command->status->date_paid))));
        $internHeader->appendChild($classificationVAT = $this->domDocument->createElement('int:classificationVAT'));
        $classificationVAT->appendChild($this->domDocument->createElement('typ:classificationVATType', 'inland')); // TODO: doplnit
        $internHeader->appendChild($this->parseAddress($address, $command->buyer, 'int'));
//        $partnerIdentity->appendChild($extId = $this->domDocument->createElement('typ:extId'));
//        $extId->appendChild($this->domDocument->createElement('inv:ids', $this->parseAddress($address, $command->buyer)));
        $intern->appendChild($invoiceDetail = $this->domDocument->createElement('int:intDocDetail'));

        $amount = 0;
        $count = 0;

        foreach($command->items as $item)
        {
            if ($item->stock->is_new) {
                continue;
            }
            $count++;
            foreach($item->buyItems as $buyItem) {
                $invoiceDetail->appendChild($this->parseInternItem($item, $buyItem, false));
                $invoiceDetail->appendChild($this->parseInternItem($item, $buyItem, true, $amount));
            }
        }
        $totalAmount += $amount;
\Debugbar::info($amount);
        $intern->append($this->parseSummary($command, 'int', $amount));
        if ($count) {
            return $xmlCommand;
        } else {
            return false;
        }

    }

    private function parseRebuy(BuyCommand $command): \DOMElement
    {
        if(is_null($command->client->address))
        {
            \Debugbar::info($command->client->id);
//            throw new \Exception('neplatna adresa buycommand id: ' . $command->id);
        }
        $xmlRebuy = $this->domDocument->createElement("dat:dataPackItem");
        $xmlRebuy->setAttribute('id', 'R' . $command->document_no);
        $xmlRebuy->setAttribute('version', '2.0');
        $xmlRebuy->appendChild($voucher = $this->domDocument->createElement('vch:voucher'));
        $voucher->setAttribute('version', '2.0');
        $voucher->appendChild($header = $this->domDocument->createElement('vch:voucherHeader'));
        $header->appendChild($this->domDocument->createElement('vch:voucherType', 'expense'));
        $header->appendChild($cashAccount = $this->domDocument->createElement('vch:cashAccount'));
        $cashAccount->appendChild($this->domDocument->createElement('typ:ids', 'HPEUR'));
        $header->appendChild($this->domDocument->createElement('vch:date', date('Y-m-d', strtotime($command->status->date_paid))));
        $header->appendChild($this->domDocument->createElement('vch:datePayment', date('Y-m-d', strtotime($command->status->date_paid))));
        $header->appendChild($this->domDocument->createElement('vch:dateTax', date('Y-m-d', strtotime($command->status->date_paid))));
        $header->appendChild($accounting = $this->domDocument->createElement('vch:accounting'));
        $accounting->appendChild($this->domDocument->createElement('typ:ids', '6Pp')); // TODO: doplnit
        $header->appendChild($classificationVAT = $this->domDocument->createElement('vch:classificationVAT'));
        $classificationVAT->appendChild($this->domDocument->createElement('typ:classificationVATType', 'nonSubsume')); // TODO: doplnit
        $header->appendChild($this->domDocument->createElement('vch:text', 'vykup karet'));
        $header->appendChild($this->parseAddress($command->client->address, $command->client, 'vch'));
        $voucher->appendChild($detail = $this->domDocument->createElement('vch:voucherDetail'));

        foreach($command->items as $item)
        {
            if ($item->stock) {
                $detail->appendChild($this->parseItem($item, 'vch'));
            }
        }

        $voucher->append($this->parseSummary($command, 'vch'));
        return $xmlRebuy;
    }

    private function parseCommand(Command $command): \DOMElement
    {
        if (is_null($command->invoice_no))
            throw new Exception("faktura nema cislo. id:" . $command->id);

        $address = $command->billing_address;
        if (is_null($address))
        {
            $address = $command->buyer->address;
        }
        if (is_null($address)) {
//            throw new Exception("faktura nema fakturacni adresu. id:" . $command->id);
        }

        $xmlCommand = $this->domDocument->createElement("dat:dataPackItem");
        $xmlCommand->setAttribute('id', 'F' . $command->invoice_no);
        $xmlCommand->setAttribute('version', '2.0');
        $xmlCommand->appendChild($invoice = $this->domDocument->createElement('inv:invoice'));
        $invoice->setAttribute('version', '2.0');
        $invoice->appendChild($invoiceHeader = $this->domDocument->createElement('inv:invoiceHeader'));
        $invoiceHeader->appendChild($this->domDocument->createElement('inv:invoiceType', 'issuedInvoice'));
        $invoiceHeader->appendChild($numberRequested = $this->domDocument->createElement('inv:number'));
        $numberRequested->appendChild($this->domDocument->createElement('typ:numberRequested', $command->id));
        $invoiceHeader->appendChild($this->domDocument->createElement('inv:date', date('Y-m-d', strtotime($command->status->date_paid))));
        $invoiceHeader->appendChild($this->domDocument->createElement('inv:dateTax', date('Y-m-d', strtotime($command->status->date_paid))));
        $invoiceHeader->appendChild($this->domDocument->createElement('inv:dateAccounting', date('Y-m-d', strtotime($command->status->date_paid))));
        $invoiceHeader->appendChild($classificationVAT = $this->domDocument->createElement('inv:classificationVAT'));
        $classificationVAT->appendChild($this->domDocument->createElement('typ:classificationVATType', !$address || $address->country == 'CZ' ? 'inland' : 'nonSubsume')); // TODO: doplnit
        $invoiceHeader->appendChild($paymentType = $this->domDocument->createElement('inv:paymentType'));
        $paymentType->appendChild($this->domDocument->createElement('typ:paymentType', 'draft')); // TODO: doplnit
        $invoiceHeader->appendChild($this->parseAddress($address, $command->buyer, 'inv'));
//        $partnerIdentity->appendChild($extId = $this->domDocument->createElement('typ:extId'));
//        $extId->appendChild($this->domDocument->createElement('inv:ids', $this->parseAddress($address, $command->buyer)));
        $invoice->appendChild($invoiceDetail = $this->domDocument->createElement('inv:invoiceDetail'));

        foreach($command->items as $item)
        {
            $invoiceDetail->appendChild($this->parseItem($item, 'inv'));
        }

        if (!is_null($command->shippingMethod)){
            $invoiceDetail->appendChild($this->parseShipping($command));
        }

        $invoice->append($this->parseSummary($command, 'inv'));
        return $xmlCommand;
    }

    private function parseSummary(Command|BuyCommand $command, string $type, $amount = null): \DOMElement
    {

        $invoiceSummary = $this->domDocument->createElement($type . ':' . ( $type == 'inv' ? 'invoiceSummary' :  ($type == 'int' ? 'intDocSummary' : 'voucherSummary')));
        $invoiceSummary->appendChild($this->domDocument->createElement($type . ':roundingVAT', 'noneEveryRate'));
        $invoiceSummary->appendChild($foreignCurrency = $this->domDocument->createElement($type . ':foreignCurrency'));
        $foreignCurrency->appendChild($currency = $this->domDocument->createElement('typ:currency'));
        $currency->appendChild($this->domDocument->createElement('typ:ids', 'EUR'));
        $foreignCurrency->appendChild($this->domDocument->createElement('typ:priceSum', $amount ?? ($command instanceof Command ? $command->total_value : $command->value)));
        return $invoiceSummary;
    }

    private function parseShipping(Command $command)
    {
        $invoiceItem = $this->domDocument->createElement('inv:invoiceItem');
        $invoiceItem->appendChild($this->domDocument->createElement('inv:text', $command->shippingMethod->method->name));
        $invoiceItem->appendChild($this->domDocument->createElement('inv:quantity', 1));
        $invoiceItem->appendChild($this->domDocument->createElement('inv:unit', 'piece'));
        $invoiceItem->appendChild($this->domDocument->createElement('inv:rateVAT', 'high'));
        $invoiceItem->appendChild($this->domDocument->createElement('inv:payVAT', 'true'));
        $invoiceItem->appendChild($classificationVATType = $this->domDocument->createElement('inv:classificationVAT'));
        $classificationVATType->appendChild($vatRateType = $this->domDocument->createElement('typ:ids', "UD"));
        $invoiceItem->appendChild($foreignCurrency = $this->domDocument->createElement('inv:foreignCurrency'));
        $foreignCurrency->appendChild($this->domDocument->createElement('typ:unitPrice', $command->shippingMethod->price));
        return $invoiceItem;
    }

    private function parseAddress(?Address $address, User $user, string $type = 'inv'): \DOMElement
    {
        $xmlAddress = $this->domDocument->createElement($type . ':partnerIdentity');
//        $xmlAddress->setAttribute('id', $id);
//        $xmlAddress->setAttribute('version', '2.0');
//        $xmlAddress->appendChild($addressBook = $this->domDocument->createElement('adb:addressBook'));
//        $addressBook->setAttribute('version', '2.0');
//        $addressBook->appendChild($addressBookHeader = $this->domDocument->createElement('adb:addressBookHeader'));
//        $addressBookHeader->appendChild($identity = $this->domDocument->createElement('adb:identity'));
//        $identity->appendChild($extId = $this->domDocument->createElement('typ:extId'));
//        $extId->appendChild($this->domDocument->createElement('typ:ids', $id));
//        $extId->appendChild($this->domDocument->createElement('typ:exSystemName', 'MtgForFun Shop'));
//        $extId->appendChild($this->domDocument->createElement('typ:exSystemText', 'MtgForFun shop database'));
//        $identity->appendChild(
            $taddress = $this->domDocument->createElement('typ:address');

        if ($address == null) {
            $taddress->appendChild($name = $this->domDocument->createElement('typ:name',$user->name));

        } else {
            $taddress->appendChild($name = $this->domDocument->createElement('typ:name', $address->forename . ' ' . $address->name));
//        $name->appendChild( $this->domDocument->createCDATASection($address->forename . ' ' . $address->name));
            $taddress->appendChild($city = $this->domDocument->createElement('typ:city', $address->city));
//        $city->appendChild($this->domDocument->createCDATASection($address->city));
            $taddress->appendChild($street = $this->domDocument->createElement('typ:street', $address->street . ' ' . $address->number));
//        $street->appendChild($this->domDocument->createCDATASection($address->street . ' ' . $address->number));


            $taddress->appendChild($this->domDocument->createElement('typ:zip', $address->postal));
            $taddress->appendChild($country = $this->domDocument->createElement('typ:country'));
            $country->appendChild($this->domDocument->createElement('typ:ids', $address->country));
            if ($user->mkm_is_commercial) {
                $taddress->appendChild($this->domDocument->createElement('typ:company', $user->company));
                $taddress->appendChild($this->domDocument->createElement('typ:dic', $address->vat_id ?? ''));

            }
        }
        $xmlAddress->appendChild($taddress);
//        $this->document->appendChild($xmlAddress);
        return $xmlAddress;
    }

    private function parseItem($item, $type): \DOMElement
    {
        if (is_null($item->stock))
        {
            throw new \Exception("Neplatny stock id :" . $item->id_stock);
        }
        if (is_null($item->stock->product))
        {
            throw new \Exception("Neplatny produkt product id :" . $item->stock->all_product_id);
        }
        $withTax = $item->stock->is_new;
        $invoiceItem = $this->domDocument->createElement($type . ':' . ( $type == 'inv' ? 'invoiceItem' : 'voucherItem'));
        $invoiceItem->appendChild($this->domDocument->createElement($type . ':text', htmlspecialchars( $item->stock->product->name)));
        $invoiceItem->appendChild($this->domDocument->createElement($type . ':quantity', $item->quantity));
        $invoiceItem->appendChild($this->domDocument->createElement($type . ':unit', 'piece'));
        $invoiceItem->appendChild($this->domDocument->createElement($type . ':rateVAT', $type == 'inv' && $withTax ? 'high' : 'none'));
        if ($type == 'inv' && $withTax) {
            $invoiceItem->appendChild($this->domDocument->createElement('inv:payVAT',  'true'));
        }
        $invoiceItem->appendChild($classificationVATType = $this->domDocument->createElement($type . ':classificationVAT'));
        $classificationVATType->appendChild($vatRateType = $this->domDocument->createElement('typ:ids', $type == 'inv' && $withTax ? 'UD' : "UN"));
        $invoiceItem->appendChild($foreignCurrency = $this->domDocument->createElement($type . ':foreignCurrency'));
        $foreignCurrency->appendChild($this->domDocument->createElement('typ:unitPrice', $item->price));
        return $invoiceItem;
    }

    private function parseInternItem($item, $buyItem, $withTax, &$amount = null): \DOMElement
    {
        if (is_null($item->stock->product))
        {
            throw new \Exception("Neplatny produkt product id :" . $item->stock->all_product_id);
        }
        $price = $withTax ? (($item->price - $buyItem->price) >= 0 ? $item->price - $buyItem->price : 0) : ($buyItem->price < $item->price ? $buyItem->price : $item->price);
        if ($amount !== null) {
            $amount += $buyItem->pivot->quantity * $price;
        }
        $invoiceItem = $this->domDocument->createElement('int:intDocItem');
        $invoiceItem->appendChild($this->domDocument->createElement('int:text', htmlspecialchars( $item->stock->product->name)));
        $invoiceItem->appendChild($this->domDocument->createElement('int:quantity', $buyItem->pivot->quantity));
        $invoiceItem->appendChild($this->domDocument->createElement('int:unit', 'piece'));
        $invoiceItem->appendChild($this->domDocument->createElement('int:rateVAT', $withTax ? 'high' : 'none'));
        $invoiceItem->appendChild($classificationVATType = $this->domDocument->createElement('int:classificationVAT'));
        $classificationVATType->appendChild($vatRateType = $this->domDocument->createElement('typ:ids', $withTax ? 'UD' : "UDobch"));
        $invoiceItem->appendChild($foreignCurrency = $this->domDocument->createElement('int:foreignCurrency'));
        $foreignCurrency->appendChild($this->domDocument->createElement('typ:unitPrice', $price));
        return $invoiceItem;
    }
}
