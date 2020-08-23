<?php


namespace App\Repositories;


use App\Models\ActivatedAbility;
use App\Models\AllProduct;
use App\Models\BorderColor;
use App\Models\CardFace;
use App\Models\Card;
use App\Models\CardType;
use App\Models\Categories;
use App\Models\Cmc;
use App\Models\Color;
use App\Models\Creature;
use App\Models\Expansion;
use App\Models\ExpansionsLocalisation;
use App\Models\Image;
use App\Models\Language;
use App\Models\ManaCost;
use App\Models\Planeswalker;
use App\Models\PromoType;
use App\Models\Rarity;
use App\Models\SpellType;
use App\Models\StaticAbility;
use App\Models\TriggeredAbility;
use Illuminate\Support\Facades\Storage;

class CardRepository extends ModelRepository implements CardRepositoryInterface
{
    public function __construct(Card $card)
    {
        $this->model = $card;
    }

    public function add($product, $data)
    {
        //addind card
        //Storage::append('Logs\\cards.log', '*** Adding ' . $product->name . ' ***');

        $card = Card::firstOrCreate(
            [
                'id' => $product->id
            ],
            [
                'foil' => $data->foil,
                'nonfoil' => $data->nonfoil,
                'oversized' => $data->oversized,
                'reserved' => $data->reserved,
                'booster' => $data->booster,
                'scryfallCollectorNumber' => $data->collector_number,
                'fullArt' => $data->full_art,
                'promo' => $data->promo,
                'story_spotlight' => $data->story_spotlight,
                'textless' => $data->textless,
            ]);
        //nemelo by to takhle byt
        //$card->id = $product->id;
        \Debugbar::info($card->id . ' ' . $card->name);
        Storage::append('Logs\\cards.log', '--- ' . $card->id . ' couldn`t been synced ---');
//return json_encode($card);
        //adding card_faces
        $this->addCardFaces($card, $data);

        //adding promo_types
        if (isset($data->promo_types))
            $this->addPromoTypes($card, $data->promo_types);

        //adding rarity
        $this->addRarity($card, $data->rarity);

        //adding border_color
        $this->addBorderColor($card, $data->border_color);

        //adding cmc (converted mana cost)
        if (isset($data->cmc))
            $this->addCmc($card, $data->cmc);

        //adding all color identities
        $this->addColorIdentities($card, $data->color_identity);

        //adding properties
        $this->addBasics($card, $data);
        $product->added = true;
        $product->save();
        return $this;
    }

    public function exists(AllProduct $product)
    {
        //var_dump($this->getById($product->id));
        if ($this->getById($product->id) == null)
            return false;
        return true;
    }

    public function addCardFaces($card, $data)
    {
        if (isset($data->card_faces))
            foreach ($data->card_faces as $card_face) {
                $cardFace = CardFace::firstOrCreate([
                    'name' => $card_face->name,
                    'collector_number' => $data->collector_number
                ]);

                $card->CardFaces()->save($cardFace);

                //add properies
                $this->addBasics($cardFace, $card_face, $data);
            }
    }

    private function addBasics($card, $data, $card_data = null)
    {

        //adding all colors
        $this->addColor($card, $data->colors);

        //adding if it is a planeswalker
        if (isset($data->loyalty))
            $this->addPlaneswalker($card, $data->loyalty);

        //adding if it is a creature
        if (isset($data->power)) {
            $this->addCreature($card, $data);
        }

        //adding mana_cost
        if (isset($data->mana_cost))
            $this->addManaCost($card, $data->mana_cost);

        //adding abilities
        $this->addAbilities($card, $data);

        //adding CardTypes
        $this->addCardTypes($card, $data);

        //adding image
        //we can`t add images here
        $this->addImage($card, $data, $card_data);

        return;
    }

    private function addImage($card, $data, $card_data = null)
    {
        $name = $data->name;
        $img_url = $data->image_uris->normal;
        if ($card_data != null) {
            $tmp = $data;
            $data = $card_data;
            $card_data = $tmp;
        }

        if (!Storage::exists('public\\image'))
            Storage::makeDirectory('public\\image');

        if (!Storage::exists('public\\image\\' . $data->set))
            Storage::makeDirectory('public\\image\\' . $data->set);
        $path3 = 'public\\image\\' .
            $data->set .
            '\\' .
            str_replace(':', '',
                str_replace(' ', '',
                    str_replace('\'', '',
                str_replace('"', '',
                    $name . '.jpg'
                ))));
        $path = 'public\\image\\' .
            $data->set .
            '\\' .
            str_replace(':', '',
                str_replace('?', 'X',
                str_replace('"', '',
                    $data->collector_number . '-'
                    . $name . '.jpg'
                )));
        if (Storage::exists($path3) && !Storage::exists($path)) {
            Storage::move($path3, $path);
        } elseif (!Storage::exists($path)) {
            $path2 = 'public\\image\\' .
                $data->set .
                '\\' .
                str_replace(':', '',
                    str_replace('"', '',
                        str_replace(' ', '',
                            $data->collector_number . '-'
                            . $name . '.jpg'
                        )));
            if (!Storage::exists($path2)) {
                $content = file_get_contents($img_url);
                Storage::put($path, $content);

            } else
                Storage::move($path2, $path);


        }

        $image = Image::firstOrCreate(//find($card->id);
            [
                'product_id' => $card->id,
            ],
            [
                'alt' => $name,
                'path' => $path,
            ]);

        if ($image == null)
            //$image = Image::create
            return $image;
    }

    private function addPromoTypes($card, $promo_types)
    {
        foreach ($promo_types as $promo_type) {
            $type = PromoType::firstOrCreate([
                'type' => $promo_type
            ]);

            $card->PromoType()->associate($type);
        }
    }

    private function addRarity($card, $rarity)
    {
        //taking first letter from word
        $rId = $this->getRarityId($rarity);

        $rarityl = Rarity::firstOrCreate([
            'id' => $rId,
            'name' => $rarity
        ]);

        $card->Rarity()->associate($rarityl);
    }

    private function addBorderColor($card, $border_color)
    {
        $borderColor = BorderColor::firstOrCreate([
            'color' => $border_color
        ]);

        $card->BorderColor()->associate($borderColor);
    }

    private function addManaCost($card, $mana_cost)
    {
        $mana_cost = ManaCost::firstOrCreate([
            'cost' => $mana_cost
        ]);

        $card->ManaCost()->associate($mana_cost);
    }

    private function addCmc($card, $cmc)
    {
        $cmcl = Cmc::firstOrCreate([
            'cmc' => $cmc
        ]);

        $card->Cmc()->associate($cmcl);
    }

    private function addAbilities($card, $data)
    {
        //Storage::append('Logs\\cards.log', '--- ' . $data->oracle_text . ' couldn`t been synced ---');

        $abilities = explode('\n', $data->oracle_text);
        foreach ($abilities as $ability) {
            $pos = strpos($ability, ':');
            if ($pos !== false) {
                $exploded = explode(': ', $ability);
                $activatedAbility = ActivatedAbility::firstOrCreate([
                    'activation_cost' => $exploded[0],
                    'ability' => $exploded[1]]);
                $card->ActivatedAbilities()->save($activatedAbility);
            } elseif
            (strpos($ability, 'When') === 0 || strpos($ability, 'At the') === 0) {
                $pos = strpos($ability, ',');
                $triggeredAbility = TriggeredAbility::firstOrCreate([
                    'trigger' => substr($ability, 0, $pos),
                    'ability' => substr($ability, $pos + 2)
                ]);
                $card->TriggeredAbilities()->save($triggeredAbility);
            } else {
                $staticAbility = StaticAbility::firstOrCreate([
                    'ability' => $ability
                ]);
                $card->StaticAbilities()->save($staticAbility);
            }
        }
    }

    private function addCardTypes($card, $data)
    {
        $cardTypes = explode(' ', $data->type_line);
        $spellTypeBool = false;

        foreach ($cardTypes as $cardTypeString) {
            if ($cardTypeString == '-') {
                $spellTypeBool = true;
                continue;
            }
            if (!$spellTypeBool) {
                $cardType = CardType::firstOrCreate([
                    'name' => $cardTypeString
                ]);
                $card->CardTypes()->save($cardType);
            } else {
                $spellType = SpellType::firstOrCreate([
                    'name' => $cardTypeString
                ]);
                $card->SpellTypes()->save($spellType);
            }
        }
    }

    private function addCreature($card, $data)
    {

        $creature = Creature::firstOrCreate([
            'power' => $data->power,
            'toughness' => $data->toughness
        ]);

        $card->Creature()->associate($creature);
    }

    private function addPlaneswalker($card, $loyalty)
    {
        $planeswalker = Planeswalker::firstOrCreate([
            'loyalty' => $loyalty
        ]);

        $card->Planeswalker()->associate($planeswalker);

    }

    private function addColor($card, $colors)
    {
        foreach ($colors as $color) {
            $name = $this->getColorName($color);

            $c = Color::firstOrCreate([
                'id' => $color,
                'name' => $name
            ]);

            $card->Colors()->save($c);
        }
    }

    private function addColorIdentities($card, $colorIdentities)
    {
        foreach ($colorIdentities as $color_identity) {
            $name = $this->getColorName($color_identity);

            $c = Color::firstOrCreate([
                'id' => $color_identity,
                'name' => $name
            ]);

            $card->ColorIdentities()->save($c);
        }

    }

    private function getRarityId($rarity)
    {
        return strtoupper($rarity[0]);
    }

    private function getColorName($color)
    {
        switch ($color) {
            case 'B':
                $name = "Black";
                break;
            case 'U':
                $name = "Blue";
                break;
            case 'G':
                $name = "Green";
                break;
            case 'R':
                $name = "Red";
                break;
            case "W":
                $name = "White";
                break;
            default:
                $name = "No";

        }
        return $name;
    }
}
