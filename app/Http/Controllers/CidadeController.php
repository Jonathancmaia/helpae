<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cidade;

class CidadeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private $cidades;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cidades = new Cidade;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Request $request)
    {

        return $this->cidades::where('id_estado', $request->uf_id)->get();
    }
}
