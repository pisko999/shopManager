<?php


namespace App\Repositories;


abstract class ProductModelRepository extends ModelRepository
{
    public function getAllWithProduct()
    {
        return $this->model->with('AllProduct')->get();
    }

    public function getByIdWithProduct($id)
    {
        return $this->model->whereId($id)->with('AllProduct')->first();
    }

    public function getByEditionId($id)
    {
        if (isset($this->model->Edition_id))
            return $this->model->whereEdition_id($id);
        else
            return null;
    }


    public function getPaginate($n)
    {
        return $this->model->paginate($n);
    }

}
