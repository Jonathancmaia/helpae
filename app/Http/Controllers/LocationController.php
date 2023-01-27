<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\Location;
use App\Location_pic;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function getDesc(Request $request)
    {
        $location = new Location;
        return $location::where('id', $request->id)->first('desc');
    }

    public function create()
    {
        return view('create-location');
    }

    public function store(Request $request)
    {
        $location = new Location;
        $location->user_id = Auth::user()->id;
            
        //Verify if desc input is valid
        if(strlen($request->desc) >= 10 && strlen($request->desc) <= 300){
            $location->desc = $request->desc;
        } else {
            return view('create-location', [
                'error' => 'Digite uma descrição entre 10 e 300 dígitos.'
            ]);
            exit();
        }
        
        //Verify if real and centavos inputs are valid
        if(intval($request->valueR) >= 10 && intval($request->valueR) <= 99999){

            if(intval($request->valueC) >= 00 && intval($request->valueC) <= 99){
                $location->value = floatval($request->valueR.".".$request->valueC);
            } else {
                return view('create-location', [
                    'error' => 'Digite um valor no campo de centavos entre 00 e 99.'
                ]);
                exit();
            }
        } else {
            return view('create-location', [
                'error' => 'Digite um valor no campo de reais entre 10 e 99999.'
            ]);
            exit();
        }

        //Verify if city input is valid
        if(intVal($request->cidade) >= 1 && intVal($request->cidade) <= 5564){
            $location->cidade = $request->cidade;
        } else {
            return view('create-location', [
                'error' => 'A localização é inválida.'
            ]);
            exit();
        }

        //Verify if user isn't vip and have 3 or more announces
        $announces = DB::table('services')->where('user_id', $location->user_id)->count() + DB::table('locations')->where('user_id', $location->user_id)->count();

        if (Auth::user()->isVip == 0 && $announces < 3 || Auth::user()->isVip > 0){
            if ($location->save()){
                return redirect()->route('home', [
                    'success' => 'Seu serviço foi publicado com sucesso.'
                ]);
                exit();
            } else {
                return view('create-location', [
                    'error' => 'Houve um erro no salvamento da locação. por favor, entre com contato com o suporte.'
                ]);
                exit();
            }
        } else {
            return view('create-location', [
                'error' => 'Usuários sem acesso vip não podem criar mais que 3 anúncios.'
            ]);
            exit();
        }
    }

    public function show($id)
    {
        $location = new Location;
        $user = Auth::user();

        if ($location::find($id) !== 'undefined'){
            if ($location::find($id)->user_id === strval($user->id)){
                return view('edit-location', ['data' => $location::find($id)]);
            } else {
                return view('show-location', ['data' => $location::find($id)]);
            }
        }
    }

    public function delete(Request $request)
    {
        $location = new Location;
        $location = $location::find($request->id);
        $location_pics = new Location_pic;
        $location_pics = $location_pics->where('location_id', $request->id)->get();

        if (intval(Auth::user()->id) === intval($location->user_id)){

            foreach($location_pics as $location_pic){
                if(Storage::disk('public')->exists('location-pic/'.$location_pic->pic_id)){

                    Storage::disk('public')->delete('location-pic/'.$location_pic->pic_id);
    
                    $location_pic::where('pic_id',$location_pic->pic_id)->delete();
                }
            }

            if($location::where('id',$request->id)->delete()){
                return redirect()->route('home', [
                    'success'=> 'Sua publicação foi apagada com sucesso.',
                ]);
            }

        } else {
            return redirect()->route('show-location', [
                'id'=>$request->id,
                'error'=> 'Você não é o dono desta publicação.',
                'data' => $location
            ]);
        }
        
    }

    public function add_pic(Request $request)
    {
        return view('edit-location', ['message'=>$params['id']]);
    }

    public function store_pic(Request $request)
    {

        if($request->hasFile('pic') && $request->file('pic')->isValid()){
            $location = new Location;
            $location_pic = new Location_pic;

            $location->user_id = Auth::user()->id;

            $extension = $request->pic->extension();

            $photo = $request->file('pic');

            $name = uniqid(date('His'));

            $nameFile="{$name}.{$extension}";

            $upload = Image::make($photo)->fit(300)->save(
                public_path('storage/location-pic/' . $nameFile)
            );

            if(!$upload){
                return redirect()->route('show-location', [
                    (int)$request->location_id,
                    'error'=>"Envie um arquivo de imagem válido."
                ]);
            } else {
                $location_pic->pic_id = $nameFile;
                $location_pic->location_id = (int)$request->location_id;

                if ($location_pic->save()){
                    return redirect()->route('show-location', [
                        (int)$request->location_id,
                        'success'=>"Arquivo enviado com sucesso."
                    ]);
                } else {
                    return redirect()->route('show-location', [
                        (int)$request->location_id,
                        'error'=>"Falha no salvamento do registro."
                    ]);
                }
            }
        } else {
            return redirect()->route('show-location', [
                (int)$request->location_id,
                'error'=>"Envie um arquivo de imagem válido."
            ]);
        }
    }

    public function show_pic($id)
    {
        $location_pic = new Location_pic;

        return $location_pic::find($id);
    }

    public function delete_pic(Request $request)
    {

        $location = new Location;
        $location_pic = new Location_pic;
        
        $location_id = $location_pic::where('pic_id',$request->pic_id)->first('location_id');
        $user_id = $location::find($location_id->location_id)->user_id;

        if ((int)$user_id === Auth::user()->id){
            if(Storage::disk('public')->exists('location-pic/'.$request->pic_id)){

                Storage::disk('public')->delete('location-pic/'.$request->pic_id);

                $location_pic::where('pic_id',$request->pic_id)->delete();

                return redirect()->route('show-location', [
                    (int)$location_id->location_id,
                    "success"=>"Midia apagada."
                ]);
            } else {
                return redirect()->route('show-location', [
                    (int)$location_id->location_id,
                    "error"=>"Midia não encontrada."
                ]);
            }
        } else {
            return redirect()->route('show-location', [
                (int)$location_id->location_id,
                "error"=>"Midia não pertence ao usuário."
            ]);
        }

    }

    public function suspend(Request $request)
    {
        $location = new Location;
        $location = $location::find($request->id);

        
        if ($location->suspended){
            $location->suspended = false;
        } else {
            $location->suspended = true;
        }

        if ($location->save()){
            return redirect()->route('show-location', [
                $location,
                "success"=>"Estado da suspenção alterado."
            ]);
        } else {
            return redirect()->route('show-location', [
                $location,
                "error"=>"Ocorreu um erro ao alterar o estado da suspenção."
            ]);
        }
    }
}
