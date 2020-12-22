<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 21/03/2019
 * Time: 16:20
 */

namespace App\Repositories;


use App\Models\Payment;

class PaymentRepository extends ModelRepository implements PaymentRepositoryInterface
{

    public function __construct(Payment $payment)
    {
        $this->model = $payment;
    }

    public function new()
    {
        $payment = new $this->model;

        $payment->type = 'cash';
        $payment->address = ""; //generation
        $payment->amount = 0;
        $payment->currency = "czk";
        $payment->status = "new";
        $payment->save();

        return $payment;
    }

    public function getByIdWithCommand($id)
    {
        //var_dump($id);
        return $this->model->whereId($id)->with('command')->first();
    }

}
