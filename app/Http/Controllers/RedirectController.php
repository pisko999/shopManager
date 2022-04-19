<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function shopping($id) {
        return redirect('http://www.mtgforfun.cz/shopping/item/' . $id);
    }
}
