<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use guzzlehttp\guzzle;
use App\Transaction;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function edit()
    {
        return view ('edit-user', [
            'id' => Auth::user()->id,
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'rank' => Auth::user()->rank,
            'isVip' => Auth::user()->isVip,
        ]);
    }

    public function turnVip(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $curl = curl_init();

        //select product
        $option = array();
        switch($request->vipOption){
            case 1:
                /*$user->isVip = 31;
                $user->save();
                return ('Você adicionou 31 dias de vip.');*/

                $option['id'] = '1';
                $option['description'] = '31 dias de vip.';
                $option['amount'] = '24.99';
                break;
            case 2:
                /*$user->isVip = 184;
                $user->save();
                return ('Você adicionou 184 dias de vip.');*/

                $option['id'] = '2';
                $option['description'] = '184 dias de vip.';
                $option['amount'] = '141.49';
                break;
            case 3:
                /*$user->isVip = 366;
                $user->save();
                return ('Você adicionou 366 dias de vip.');*/

                $option['id'] = '3';
                $option['description'] = '366 dias de vip.';
                $option['amount'] = '259.99';
                break;
            default:
                return ('Algo deu errado na seleção de compra. Entre em conttao com o suporte.');
                break;
        }

        //xml mount
        $xml = '<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>
            <checkout>
                <currency>BRL</currency>
                <redirectURL>http://lugae-api.test/home</redirectURL>
                <items>
                    <item>
                        <id>'.$option['id'].'</id>
                        <quantity>1</quantity>
                        <description>'.$option['description'].'</description>
                        <amount>'.$option['amount'].'</amount>
                    </item>
                </items>
                <sender>
                    <name>'.Auth::user()->name.'</name>
                    <email>'.Auth::user()->email.'</email>
                </sender>
            </checkout>
        ';

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout?email=helpaecomercial@gmail.com&token=09E866B44E7A4808B57B6F71AAB11D38',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $xml,
            CURLOPT_HTTPHEADER => [
              "Content-Type: application/xml; charset=ISO-8859-1"
        ],]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return $err;
        } else {

            $client = new \GuzzleHttp\Client();

            $code = simplexml_load_string($response)->code;

            header('location: https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code='.$code);

            exit();
        }
    }

    public function paymentReturn(Request $request){
        if($request->getHttpHost() === 'pagseguro.uol.com.br'){

            if($request->notificationType === 'transaction'){
                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/'.$request->notificationCode.'?email=helpaecomercial@gmail.com&token=09E866B44E7A4808B57B6F71AAB11D38',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [
                      "Content-Type: application/xml; charset=ISO-8859-1"
                ],]);

                $response = simplexml_load_file(curl_exec($curl));
                $err = curl_error($curl);
                curl_close($curl);

                $transaction = new Transaction;

                /* legenda do status da compra do mercado livre
                    1 - Aguardando pagamento
                    2 - Em análise
                    3 - Paga
                    4 - Disponível
                    5 - Em disputa
                    6 - Devolvida
                    7 - Cancelada
                    8 - Debitado
                    9 - Retenção temporária
                */

                if (Transaction::find($response->code) !== null){
                    $transacion = Transaction::find($response->code);
                    $transacion->status = $response->transaction->status;

                    //Se o retorno for "aprovado" adiciona os dias de vip, caso seja "cancelado" ou "devolvido" remove os dias de vip.
                    $user = User::find(Auth::user()->id);

                    if ($response->transaction->status === '3'){

                        switch ($response->transaction->items->item->id){
                            case 1:
                                $user->isVip = $user->isVip+31;
                                break;
                            case 2:
                                $user->isVip = $user->isVip+184;
                                break;
                            case 3:
                                $user->isVip = $user->isVip+366;
                                break;
                            default:
                                exit();
                                break;
                        }
                        
                        $user->save();
                    }else if (
                        $response->transaction->status === '6' || $response->transaction->status === '7'
                    ){
                        switch ($response->transaction->items->item->id){
                            case 1:
                                $user->isVip = $user->isVip-31;
                                break;
                            case 2:
                                $user->isVip = $user->isVip-184;
                                break;
                            case 3:
                                $user->isVip = $user->isVip-366;
                                break;
                            default:
                                exit();
                                break;
                        }
                        
                        $user->save();
                    }

                    $transaction->save();
                } else {
                    $transaction->id = $response->transaction->code;
                    $transaction->status = $response->transaction->status;

                    //Se o retorno for "aprovado" adiciona os dias de vip, caso seja "cancelado" ou "devolvido" remove os dias de vip.
                    if ($response->transaction->status === '3'){
                        $user = User::find(Auth::user()->id);

                        switch ($response->transaction->items->item->id){
                            case 1:
                                $user->isVip = $user->isVip+31;
                                break;
                            case 2:
                                $user->isVip = $user->isVip+184;
                                break;
                            case 3:
                                $user->isVip = $user->isVip+366;
                                break;
                            default:
                                exit();
                                break;
                        }
                    } else if (
                        $response->transaction->status === '6' || $response->transaction->status === '7'
                    ){
                        switch ($response->transaction->items->item->id){
                            case 1:
                                $user->isVip = $user->isVip-31;
                                break;
                            case 2:
                                $user->isVip = $user->isVip-184;
                                break;
                            case 3:
                                $user->isVip = $user->isVip-366;
                                break;
                            default:
                                exit();
                                break;
                        }
                        
                        $user->save();
                    }

                    $transaction->user_id = Auth::user()->id;
                    $transaction->save();
                }
            }

            return response(200);
        } else {
            return response('Not authorized host.',500);
        }
    }

    public function changeName(Request $request){
        $user = User::find(Auth::user()->id);

        //Valid and save field
        if ($user->name === $request->name){
            return view ('edit-user', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rank' => $user->rank,
                'isVip' => $user->isVip,
                'error' => "O nome digitado é igual ao nome já salvo."
            ]);
        } else if (strlen($request->name) < 3 || strlen($request->name) > 60){
            return view ('edit-user', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rank' => $user->rank,
                'isVip' => $user->isVip,
                'error' => "O campo 'nome' deve possuir entre 3 e 60 caracteres."
            ]);
        } else {
            $user->name = $request->name;
            
            if($user->save()){
                return view ('edit-user', [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rank' => $user->rank,
                    'isVip' => $user->isVip,
                    'success' => "Nome alterada com sucesso."
                ]);
            } else {
                return view ('edit-user', [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rank' => $user->rank,
                    'isVip' => $user->isVip,
                    'error' => "Erro no salvamento da informação. Favor entrar em contato com o suporte."
                ]);
            }
        }
    }

    public function changeEmail(Request $request){
        $user = User::find(Auth::user()->id);

        //Valid and save field
        if ($user->email === $request->email){
            return view ('edit-user', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rank' => $user->rank,
                'isVip' => $user->isVip,
                'error' => "O e-mail digitado é igual ao e-mail já salvo."
            ]);
        } else if (
            strlen($request->email) <= 5 ||
            strlen($request->email) >= 100 ||
            !filter_var($request->email, FILTER_VALIDATE_EMAIL)
        ){
            return view ('edit-user', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rank' => $user->rank,
                'isVip' => $user->isVip,
                'error' => "O campo 'e-mail' deve possuir entre 5 e 100 caracteres e deve ser um e-mail válido."
            ]);
        } else {
            $user->email = $request->email;

            if($user->save()){
                return view ('edit-user', [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rank' => $user->rank,
                    'isVip' => $user->isVip,
                    'success' => "Email alterado com sucesso."
                ]);
            } else {
                return view ('edit-user', [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rank' => $user->rank,
                    'isVip' => $user->isVip,
                    'error' => "Erro no salvamento da informação. Favor entrar em contato com o suporte."
                ]);
            }
        }
    }

    public function changePassword(Request $request){
        $user = User::find(Auth::user()->id);

        if(!Auth::attempt(['email' => $user->email, 'password' => $request->password])){
            return view ('edit-user', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rank' => $user->rank,
                'isVip' => $user->isVip,
                'error' => "A senha atual está incorreta"
            ]);
        } else if (strlen($request->newPassword) < 8 || strlen($request->newPassword) > 32){
            return view ('edit-user', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rank' => $user->rank,
                'isVip' => $user->isVip,
                'error' => $request->newPassword
            ]);
        } else {
            
            $user->password = Hash::make($request->newPassword);

            if($user->save()){
                return view ('edit-user', [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rank' => $user->rank,
                    'isVip' => $user->isVip,
                    'success' => "Senha alterada com sucesso."
                ]);
            } else {
                return view ('edit-user', [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rank' => $user->rank,
                    'isVip' => $user->isVip,
                    'error' => "Erro no salvamento da informação. Favor entrar em contato com o suporte."
                ]);
            }
        }
    }

    public function getData(Request $request){
        $user = new User;
        return $user::where('id', $request->id)->first(['name', 'email']);
    }
}