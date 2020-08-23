<?php


namespace App\Repositories;


use App\Models\ScryfallEdition;
use Illuminate\Support\Facades\Storage;

class scryfallEditionRepository extends ModelRepository implements scryfallEditionRepositoryInterface
{
    public function __construct(ScryfallEdition $scryfallEdition)
    {
        $this->model = $scryfallEdition;
    }

    public function add($set)
    {
        $edition = ScryfallEdition::firstOrCreate([
                'id' => $set->id,
            ]
            ,
            [
                'name' => $set->name,
                'code' => strtoupper($set->code),
                'uriScryfall' => $set->uri,
                'uriSearch' => $set->search_uri,
                'setType' => $set->set_type,
                'cardCount' => $set->card_count,
                'parentSetCode' => (isset($set->parent_set_code) ? strtoupper($set->parent_set_code) : null),
                'iconSVGUri' => $set->icon_svg_uri
            ]);

        $path = 'Icons';

        if (!Storage::exists($path))
            Storage::makeDirectory($path);

        $filename = $edition->code . '.svg';

        if (!Storage::exists($path . '/' . $filename))
            Storage::put($path . '/' . $filename, file_get_contents($edition->iconSVGUri));

        return;
    }

    public function getAllWithExpansions()
    {
        return $this->model->with('Expansions')->get();
    }

    public function getByCode($code)
    {
        return $this->model->whereCode($code)->first();
    }

}
