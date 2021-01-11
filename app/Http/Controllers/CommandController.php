<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Objects\pdfAddress;
use App\Objects\PdfAddressStartPositionObject;
use App\Objects\pdfFacture;
use App\Repositories\CommandRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Repositories\StatusNamesRepositoryInterface;
use Illuminate\Http\Request;

class CommandController extends Controller
{
    protected $commandRepository;

    public function __construct(CommandRepositoryInterface $commandRepository)
    {
        $this->commandRepository = $commandRepository;
        $this->middleware('auth');
    }

    public function showCommand($id){
        $command = $this->commandRepository->getById($id);
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
            $commandType = 5;
        else
            $commandType = $request->commandType;

        $statusNames = $statusNamesRepository->getAll();

        $commands = $this->commandRepository->getCommandsPaginate($commandType);
        $links = $commands->render();
        return view('command.index', compact('commands', 'links', 'statusNames', 'commandType'));
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

    public function acceptCancellation($id, $relistItems){
        return $this->commandRepository->acceptCancellation($id, $relistItems);

    }

}
