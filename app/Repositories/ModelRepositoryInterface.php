<?php


namespace App\Repositories;


interface ModelRepositoryInterface
{

    public function getPaginate($n);

    public function getAll();

    public function store(Array $inputs);

    public function getById($id);

    public function update($id, Array $inputs);

    public function destroy($id);
}
