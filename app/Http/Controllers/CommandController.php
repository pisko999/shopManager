<?php

namespace App\Http\Controllers;

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

    public function getCommands(Request $request, StatusNamesRepositoryInterface $statusNamesRepository)
    {

        if(!isset($request->commandType))
            $commandType = 0;
        else
            $commandType = $request->commandType;

        $statusNames = $statusNamesRepository->getAll();

        $commands = $this->commandRepository->getCommandsPaginate($commandType);
        $links = $commands->render();
        return view('command.index', compact('commands', 'links', 'statusNames', 'commandType'));
    }
}
