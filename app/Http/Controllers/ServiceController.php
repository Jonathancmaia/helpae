<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

use App\Service;
use App\Service_pic;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function getDesc(Request $request)
    {
        $service = new Service;
        return $service::where('id', $request->id)->first('desc');
    }

    public function create()
    {
        if (Auth::user()->cnpj === NULL){
            return view('home', [
                'error' => 'Você só pode anúnciar um serviço caso possua um cnpj. Você pode adicionar o seu cnpj no pianel.'
            ]);
            exit();
        } else {
            return view('create-service');
        }
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

        //Verify if city input is valid
        if(intVal($request->cidade) >= 1 && intVal($request->cidade) <= 5564){
            $service->cidade = $request->cidade;
        } else {
            return view('create-service', [
                'error' => 'A localização é inválida.'
            ]);
            exit();
        }

        

        //Verify if user isn't vip and have 3 or more announces
        $announces = DB::table('services')->where('user_id', $service->user_id)->count() + DB::table('locations')->where('user_id', $service->user_id)->count();

        if (Auth::user()->isVip == 0 && $announces < 3 || Auth::user()->isVip > 0){
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
        } else {
            return view('create-service', [
                'error' => 'Usuários sem acesso vip não podem criar mais que 3 anúncios.'
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

    public function delete(Request $request)
    {
        $service = new Service;
        $service = $service::find($request->id);
        $service_pics = new Service_pic;
        $service_pics = $service_pics->where('service_id', $request->id)->get();

        if (intval(Auth::user()->id) === intval($service->user_id)){

            foreach($service_pics as $service_pic){
                if(Storage::disk('public')->exists('service-pic/'.$service_pic->pic_id)){

                    Storage::disk('public')->delete('service-pic/'.$service_pic->pic_id);
    
                    $service_pic::where('pic_id',$service_pic->pic_id)->delete();
                }
            }

            if($service::where('id',$request->id)->delete()){
                return redirect()->route('home', [
                    'success'=> 'Sua publicação foi apagada com sucesso.',
                ]);
            }

        } else {
            return redirect()->route('show-service', [
                'id'=>$request->id,
                'error'=> 'Você não é o dono desta publicação.',
                'data' => $service
            ]);
        }
        
    }

    public function add_pic(Request $request)
    {
        return view('edit-service');
    }

    public function store_pic(Request $request)
    {
        if($request->hasFile('pic') && $request->file('pic')->isValid()){
            $service = new Service;
            $service_pic = new Service_pic;

            $service->user_id = Auth::user()->id;

            $extension = $request->pic->extension();

            $photo = $request->file('pic');

            $name = uniqid(date('His'));

            $nameFile="{$name}.{$extension}";

            $upload = Image::make($photo)->fit(300)->save(
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

    public function delete_pic(Request $request)
    {

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

    public function suspend(Request $request)
    {
        $service = new Service;
        $service = $service::find($request->id);

        
        if ($service->suspended){
            $service->suspended = false;
        } else {
            $service->suspended = true;
        }

        if ($service->save()){
            return redirect()->route('show-service', [
                $service,
                "success"=>"Estado da suspenção alterado."
            ]);
        } else {
            return redirect()->route('show-service', [
                $service,
                "error"=>"Ocorreu um erro ao alterar o estado da suspenção."
            ]);
        }
    }
}
