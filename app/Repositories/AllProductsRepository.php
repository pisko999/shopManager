<?php


namespace App\Repositories;


use App\Models\AllProduct;
use App\Models\Categories;
use App\Models\Expansion;
use App\Models\ExpansionsLocalisation;
use App\Models\Language;
use Illuminate\Http\Request;

class AllProductsRepository extends ModelRepository implements AllProductsRepositoryInterface
{
    public function __construct(AllProduct $allProduct)
    {
        $this->model = $allProduct;
    }

    public function add($data)
    {
        $category = Categories::firstOrCreate(
            [
                'id' => $data[2],
            ],
            [
                'name' => $data[3],
            ]
        );

        $newAllProduct = AllProduct::firstOrCreate(
            [
                'id' => $data[0]
            ],
            [
                'name' => $data[1],
                'idCategory' => $category->id,
                'idExpansion' => $data[4] == '' ? null : $data[4],
                'idMetacard' => $data[5] == '' ? null : $data[5],
            ]);


        //return $newAllProduct;
        return;
    }
    public function search(Request $request) {
        if($request->input('name')) {
            return $this->model->where('name', 'like', '%' . $request->input('name') . '%')->paginate(10);
        }
        return null;
    }
}
