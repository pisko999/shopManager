<?php


namespace App\Repositories;


use Illuminate\Database\Eloquent\Collection;

interface ModelRepositoryInterface
{

    public function getPaginate($n);

    public function getAll(): Collection;

    public function store(Array $inputs);

    public function getById($id);

    public function update($id, Array $inputs);

    public function destroy($id);
}
