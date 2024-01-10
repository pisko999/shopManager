<?php

namespace App\Console\Commands;

use App\Models\AllProduct;
use App\Models\BuyCommand;
use App\Models\BuyItem;
use Illuminate\Console\Command;

class repairBuyItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:repairBuyItems';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $files = glob('public/export-vykup-2023-*');
        foreach(array_reverse($files) as $file) {
            if ($file == "public/export-vykup-2023-10.xml" || $file == "public/export-vykup-2023-9.xml") {
                continue;
            }
            $xml = new \DOMDocument();
            $xml->load($file);
            $orders = [];
            foreach($xml->getElementsByTagName('dataPackItem') as $order) {
                $id = substr($order->getAttribute('id'),1);
                echo $id . "\n";
                $buyOrder = BuyCommand::where('document_no', $id)->first();
                $array = [];
                $items = $order->getElementsByTagName('voucherItem');
                $i = 0;
                foreach ($items as $item) {
                    $i++;
                    echo "item no " . $i . "\n";
                    $itemName = $item->getElementsByTagName('text')->item(0)->nodeValue;
                    $itemQuantity = $item->getElementsByTagName('quantity')->item(0)->nodeValue;
                    $itemPrice = $item->getElementsByTagName('unitPrice')->item(0)->nodeValue;
                    $products = AllProduct::where('name', $itemName)->get();
                    $buyItem = null;
                    foreach($products as $product) {
                        $buyItem = BuyItem::where('id_product', $product->id)->where('quantity', intval($itemQuantity))->where('price', floatval($itemPrice))/*->where('id_buy_command', 406)*/->orderBy('id', 'DESC')->first();
//                        if ($i == 6) {
//                            var_dump($product->id);
//                            var_dump($itemQuantity);
//                            var_dump($itemPrice);
//                        }
                        if ($buyItem) {
                            if ($buyItem->id_buy_command != $buyOrder->id) {
                                $buyItem->update(['id_buy_command' => $buyOrder->id]);
                            }
                            break;
                        }
                    }
                    if ($buyItem == null) {
                        echo "No item found for " . $itemName . " " . $id . "\n";
                    }
                }
            }
        }
    }
}
