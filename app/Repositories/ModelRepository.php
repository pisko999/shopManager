<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 14/03/2019
 * Time: 12:57
 */

namespace App\Repositories;


use Illuminate\Database\Eloquent\Collection;

abstract class ModelRepository implements ModelRepositoryInterface
{
    protected $model;

    public function getPaginate($n = 25)
    {
        return $this->model->paginate($n);
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function store(Array $inputs)
    {
        return $this->model->create($inputs);
    }

    public function getById($id)
    {
        return $this->model->whereId($id)->first();
    }

    public function update($id, Array $inputs)
    {
        return $this->getById($id)->update($inputs);
    }

    public function destroy($id)
    {
        return $this->getById($id)->delete();
    }
}
