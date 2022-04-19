<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 21/03/2019
 * Time: 16:59
 */

namespace App\Repositories;

use App\Http\Requests\ItemAddRequest;
use \App\Models\Item;
use \App\Models\Command;
use App\Models\StatusName;
use App\Models\User;
use App\Services\StockService;
use http\Env\Request;
use Illuminate\Support\Facades\Hash;

class UserRepository extends ModelRepository implements UserRepositoryInterface
{
    private $addressRepository;

    public function __construct(User $user, AddressRepositoryInterface $addressRepository)
    {
        $this->model = $user;
        $this->addressRepository = $addressRepository;
    }

    public function firstOrCreateFromMKM($data)
    {
        $user = $this->model->firstOrCreate([
            'mkm_id' => $data->idUser,
        ], [
            'mkm_username' => $data->username,
            'mkm_country' => isset($data->country) ? $data->country : null,
            'mkm_is_commercial' => $data->isCommercial,
            'mkm_reputation' => $data->riskGroup,
            'mkm_risk_group' => $data->reputation,
            'mkm_ships_fast' => $data->shipsFast,
            'mkm_sell_count' => $data->sellCount,
            'name' => isset($data->name) && isset($data->name->lastName) ? $data->name->lastName : $data->username,
            'forename' => isset($data->name) && isset($data->name->firstName) ? $data->name->firstName : null,
            'email' => $data->idUser . '@mkm.com',
            'password' => Hash::make($data->username),
            'deleted' => 1,
        ]);

        $user->Addresses()->save($this->addressRepository->createFromMKM($data->address));

        return $user;
    }

}
