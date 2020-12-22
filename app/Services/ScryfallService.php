<?php


namespace App\Services;


use Illuminate\Support\Facades\Storage;

class ScryfallService
{
    private $path = 'ScryfallResponses';

    public function call($command, $parametr = null)
    {
        $path = $this->path . '/' . $command;
        $filename = ($parametr != null ? $parametr : '') . '.data';
        $filepath = $path . '/' . $filename;

        if (in_array($filepath, Storage::files($this->path)))
            return Storage::get($filepath);

        $response = file_get_contents('https://api.scryfall.com/' . $command . ($parametr != null ? '/' . $parametr : ''));

        $pathArray = explode('/', $path);

        $p = '';
        foreach ($pathArray as $dir) {
            if (!Storage::exists($p . $dir))
                Storage::makeDirectory($p . $dir);
            $p .= $dir . '/';
        }

        Storage::put($filepath, $response);

        return $response;

    }

    public function getSets()
    {

        $sets = json_decode($this->call('sets'))->data;
        return $sets;
    }

    public function getSingles($set)
    {

        $path = $this->path . '/singles';
        $filename = $set->code . '.data';
        $filepath = $path . '/' . $filename;

        if (in_array($filepath, Storage::files($path)))
            return json_decode(Storage::get($filepath))->data;

        $response = file_get_contents($set->uriSearch . "&include%5Fvariations=true");
        $responseObject = json_decode($response);

        while ($responseObject->has_more) {
            $response = file_get_contents($responseObject->next_page);
            $responseObject2 = json_decode($response);

            $responseObject->data = array_merge($responseObject->data, $responseObject2->data);

            if ($responseObject2->has_more)
                $responseObject->next_page = $responseObject2->next_page;

            $responseObject->has_more = $responseObject2->has_more;
        }

        $pathArray = explode('/', $path);

        $p = '';
        foreach ($pathArray as $dir) {
            if (!Storage::exists($p . $dir))
                Storage::makeDirectory($p . $dir);
            $p .= $dir . '/';
        }

        Storage::put($filepath, json_encode($responseObject));

        return $responseObject->data;
    }

    public function getById($id){
        return json_decode($this->call('cards', $id));
    }

}
