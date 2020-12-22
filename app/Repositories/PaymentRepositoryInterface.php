<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 21/03/2019
 * Time: 16:19
 */

namespace App\Repositories;


interface PaymentRepositoryInterface
{
    public function new();

    public function getByIdWithCommand($id);

}
