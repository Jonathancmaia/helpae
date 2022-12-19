<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

use App\Service;
use App\Service_pic;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function getDesc(Request $request)
    {
        $service = new Service;
        return $service::where('id', $request->id)->first('desc');
    }

    public function create()
    {
        return view('create-service');
    }

    public function store(Request $request)
    {
        $service = new Service;

        $service->user_id = Auth::user()->id;
            
        //Verify if desc input is valid
        if(strlen($request->desc) >= 10 && strlen($request->desc) <= 300){
            $service->desc = $request->desc;
        } else {
            return view('create-service', [
                'error' => 'Digite uma descrição entre 10 e 300 dígitos.'
            ]);
            exit();
        }
        
        //Verify if real and centavos inputs are valid
        if(intval($request->valueR) >= 10 && intval($request->valueR) <= 99999){

            if(intval($request->valueC) >= 00 && intval($request->valueC) <= 99){
                $service->value = floatval($request->valueR.".".$request->valueC);
            } else {
                return view('create-service', [
                    'error' => 'Digite um valor no campo de centavos entre 00 e 99.'
                ]);
                exit();
            }
        } else {
            return view('create-service', [
                'error' => 'Digite um valor no campo de reais entre 10 e 99999.'
            ]);
            exit();
        }

        if ($service->save()){
            return redirect()->route('home', [
                'success' => 'Seu serviço foi publicado com sucesso.'
            ]);
            exit();
        } else {
            return view('create-service', [
                'error' => 'Houve um erro no salvamento do serviço. por favor, entre cm contato com o suporte.'
            ]);
            exit();
        }
    }

    public function show($id)
    {
        $service = new Service;
        $user = Auth::user();

        if ($service::find($id) !== 'undefined'){
            if ($service::find($id)->user_id === strval($user->id)){
                return view('edit-service', ['data' => $service::find($id)]);
            } else {
                return view('show-service', ['data' => $service::find($id)]);
            }
        }
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function add_pic(Request $request){
        return view('edit-service');
    }

    public function store_pic(Request $request){
        if($request->hasFile('pic') && $request->file('pic')->isValid()){
            $service = new Service;
            $service_pic = new Service_pic;

            $service->user_id = Auth::user()->id;

            $extension = $request->pic->extension();

            $photo = $request->file('pic');

            $name = uniqid(date('His'));

            $nameFile="{$name}.{$extension}";

            $upload = Image::make($photo)->crop(300)->save(
                public_path('storage/service-pic/' . $nameFile)
            );

            if(!$upload){
                return redirect()->route('show-service', [
                    (int)$request->service_id,
                    'error'=>"Falha no upload do arquivo."
                ]);
            } else {
                $service_pic->pic_id = $nameFile;
                $service_pic->service_id = (int)$request->service_id;

                if($service_pic->save()){
                    return redirect()->route('show-service', [
                        (int)$request->service_id,
                        "success"=>"Arquivo enviado com sucesso."
                    ]);
                } else {
                    return redirect()->route('show-service', [
                        (int)$request->service_id,
                        "error"=>"Falha no salvamento do arquivo."
                    ]);
                }
            }
        } else {
            return redirect()->route('show-service', [
                (int)$request->service_id,
                "error"=>"Faça o upload de um arquvo de imagem."
            ]);
        }
    }

    public function delete_pic(Request $request){

        $service = new Service;
        $service_pic = new Service_pic;
        
        $service_id = $service_pic::where('pic_id',$request->pic_id)->first('service_id');
        $user_id = $service::find($service_id->service_id)->user_id;

        if ((int)$user_id === Auth::user()->id){
            if(Storage::disk('public')->exists('service-pic/'.$request->pic_id)){

                Storage::disk('public')->delete('service-pic/'.$request->pic_id);

                $service_pic::where('pic_id',$request->pic_id)->delete();

                return redirect()->route('show-service', [
                    (int)$service_id->service_id,
                    "success"=>"Midia apagada."
                ]);
            } else {
                return redirect()->route('show-service', [
                    (int)$service_id->service_id,
                    "error"=>"Midia não encontrada."
                ]);
            }
        } else {
            return redirect()->route('show-service', [
                (int)$service_id->service_id,
                "error"=>"Esta midia não pretence ao usuário."
            ]);
        }

    }
}
