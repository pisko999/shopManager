<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Objects\pdfAddress;
use App\Objects\PdfAddressStartPositionObject;
use App\Objects\pdfFacture;
use App\Objects\Status;
use App\Repositories\CommandRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Repositories\GiftItemRepositoryInterface;
use App\Repositories\StatusNamesRepositoryInterface;
use App\Services\messagerieService;
use Illuminate\Http\Request;

class CommandController extends Controller
{
    protected $commandRepository;
    protected $giftItemRepository;

    public function __construct(
        CommandRepositoryInterface $commandRepository,
    GiftItemRepositoryInterface $giftItemRepository
    ) {
        $this->commandRepository = $commandRepository;
        $this->giftItemRepository = $giftItemRepository;
        $this->middleware('auth');
    }

    public function showCommand($id){
        $command = $this->commandRepository->getById($id);
        \Debugbar::info($command->delivery_address);
        return view('command.show', compact('command'));
    }

    public function showPrintableCommand($id){
        $command = $this->commandRepository->getById($id);
        $printable = true;
        return view('command.show', compact('command', 'printable'));
    }

    public function getCommands(Request $request, StatusNamesRepositoryInterface $statusNamesRepository)
    {

        if(!isset($request->commandType))
            $commandType = 2;
        else
            $commandType = $request->commandType;
        $presale = isset($request->presale);

        $statusNames = $statusNamesRepository->getAll();

        $commands = $this->commandRepository->getCommandsPaginate($commandType, $presale);
        $commands->appends($request->except('page'));
        \Debugbar::info($commands);
        $links = $commands->render();
        return view('command.index', compact('commands', 'links', 'statusNames', 'commandType', 'presale'));
    }

    public function trackingNumber(Request $request){
        return $this->commandRepository->setTrackingNumber($request->id,$request->trackingNumber);
    }
    public function showCommandsByType(Request $request){

        if(!isset($request->commandType))
            $commandType = 0;
        else
            $commandType = $request->commandType;

        $commands = $this->commandRepository->getByType($request->commandType);

        return view('command.index', compact('commands'));
    }

    public function printAddress($id)
    {
        $fpdf = new pdfAddress();

        $command = $this->commandRepository->getById($id);
        $myAddress = Address::find(1);
        $fpdf->init($myAddress);
            $fpdf->show($command->delivery_address);
        $fpdf->Output("D", "Addresses.pdf", true);
    }

    public function printAddresses( pdfAddress $fpdf)
    {
        $commands = $this->commandRepository->getByType('paid');
        $myAddress = Address::find(1);
        $fpdf->init($myAddress);
        foreach ($commands as $command) {
            $fpdf->show($command->delivery_address);
        }
        $fpdf->Output("D", "Addresses.pdf", true);
    }

    public function setPosition($position){
        if($position <0 || $position>3)
            abort();
        PdfAddressStartPositionObject::setPosition($position);
        return PdfAddressStartPositionObject::getPosition();
    }

    public function printPaidFactures(pdfFacture $fpdf){
        $commands = $this->commandRepository->getByType('paid');

        foreach ($commands as $command){
            $fpdf->show($command);
        }
        $fpdf->Output("D","Factures.pdf", true);
    }

    public function printFacture($id){
        $fpdf = new pdfFacture();

        $command = $this->commandRepository->getById($id);

            $fpdf->show($command);
        $fpdf->Output("D","Factures.pdf", true);
    }

    public function setSend($id){
        $command = $this->commandRepository->setSend($id);
        if(!$command)
            return response()->json(['message' => 'Command ' . $id . ' not found.'],404);
        return $command;
    }
    public function setPaid($id){
        $command = $this->commandRepository->setPaid($id);
        if(!$command)
            return response()->json(['message' => 'Command ' . $id . ' not found.'],404);
        return $command;
    }

    public function acceptCancellation($id, $relistItems){
        return $this->commandRepository->acceptCancellation($id, $relistItems);

    }

    public function sendAll(){
        /*
        $commands = $this->commandRepository->getByType(Status::PAID, $onlyMKM = true);

        foreach ($commands as $command) {

            $this->commandRepository->setSend($command->id);
            messagerieService::successMessage('Command #'. $command->id . ' was marked as sent.');
        }

        return redirect()->back();
        */
    }

    public function checkMKM(Request $request){
        $command = $this->commandRepository->checkByIdMKM($request->idOrderMKM);
        \Debugbar::info($command);
        return view('command.show', compact('command'));
    }

    public function action(Request $request){
        if(!isset($request->action) || empty($request->commandIds)) {
            messagerieService::successMessage('No actions or commmands selected');
            return redirect()->back();
        }
        $commands = $this->commandRepository->getByIds($request->commandIds);
        \Debugbar::info($commands);

        switch ($request->action) {
            case 'address':
                $fpdf = new pdfAddress();
                $myAddress = Address::find(1);
                $fpdf->init($myAddress);
                foreach ($commands as $command) {
                    $fpdf->show($command->delivery_address, $command->shippingMethod);
                }
                $fpdf->Output("D", "Addresses.pdf", true);
                break;
            case 'facture':
                $fpdf = new pdfFacture();

                foreach ($commands as $command){
                    $fpdf->show($command);
                }
                $fpdf->Output("D","Factures.pdf", true);
                break;
            case 'send':

                foreach ($commands as $command) {

                    $this->commandRepository->setSend($command->id);
                    messagerieService::successMessage('Command #'. $command->id . ' was marked as sent.');
                }
                return redirect()->back();
            case 'addGift':
                foreach ($commands as $command) {
                    $count = 4;
                    $countRest = $this->giftItemRepository->getItemsCount(2);
                    if($countRest == 0) {
                        messagerieService::successMessage('Command #' . $command->id . ': there are no more gifts.');
                        continue;
                    } else if ($countRest < $count) {
                        $count = $countRest;
                    }
                    $added = $this->commandRepository->addGift($command->id, $count);
                    if ($added === false) {
                        messagerieService::successMessage('Command #' . $command->id . ' has allready gift.');
                    } else {
                        messagerieService::successMessage('Command #' . $command->id . ' has added ' . $added . ' gifts.');
                    }
                }
                return redirect()->back();
            case 'showGifts':
                return view('command.showGifts', compact('commands'));
        }

        return view('home');
    }
}
