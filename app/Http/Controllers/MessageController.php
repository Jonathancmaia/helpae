<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Message;
use App\Location;
use App\Service;
use App\Events\chatNotificate;


class MessageController extends Controller
{
    protected $message;
    protected $location;
    protected $service;

    public function __construct (){
        $this->message = new Message;
        $this->location = new Location;
        $this->service = new Service;
    }

    public function index()
    {
        return view('messages');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //Message sender definition
        $this->message->id_sender = Auth::user()->id;
        
        //Message reciever definition
        if (!isset($request->partner)){
            if ($request->type === 'l'){
                $user_id = $this->location::where('id', $request->id_locationOrService)->get('user_id');
                $this->message->id_reciever = $user_id[0]->user_id;
            } else if ($request->type === 's'){
                $user_id = $this->service::where('id', $request->id_locationOrService)->get('user_id');
                $this->message->id_reciever = $user_id[0]->user_id;
            }
        } else {
            $this->message->id_reciever = $request->partner;
        }

        //LocationOrService definition
        $this->message->id_locationOrService = $request->id_locationOrService;

        //Message definition
        $this->message->message = $request->message;

        //type definition
        $this->message->type = $request->type;

        //Send event new message
        broadcast(new chatNotificate($request->partner, $this->message));

        if($this->message->save()){
            return "true";
        } else {
            return "false";
        }
    }

    public function show(Request $request)
    {
        //Verify if there is a selected message
        if (isset($request->id_locationOrService)){

            if ($request->type === 'l'){

                $messages = $this->message::where([
                    ['id_sender', '=', Auth::user()->id],
                    ['id_locationOrService', '=', $request->id_locationOrService],
                    ['type', '=', 'l']
                ])->orWhere([
                    ['id_reciever', '=', Auth::user()->id],
                    ['id_locationOrService', '=', $request->id_locationOrService],
                    ['type', '=', 'l']
                ])->orderBy('created_at')->get();
            }else if ($request->type === 's'){

                $messages = $this->message::where([
                    ['id_sender', '=', Auth::user()->id],
                    ['id_locationOrService', '=', $request->id_locationOrService],
                    ['type', '=', 's']
                ])->orWhere([
                    ['id_reciever', '=', Auth::user()->id],
                    ['id_locationOrService', '=', $request->id_locationOrService],
                    ['type', '=', 's']
                ])->orderBy('created_at')->get();
            }

        } else {

            //Got all messages that logged user is in
            $messages = $this->message::where([
                ['id_sender', '=', Auth::user()->id]
            ])->orWhere([
                ['id_reciever', '=', Auth::user()->id]
            ])->orderBy('created_at')->get();
        }

        //Organize conversations by locationsOrServices
        $organizedMessages = null;

        foreach ($messages as $message) {
            $id_partner = null;

            if((int)$message->id_sender === (int)Auth::user()->id){
                $id_partner = $message->id_reciever;
            } else if ((int)$message->id_reciever === (int)Auth::user()->id) {
                $id_partner = $message->id_sender;
            }

            $organizedMessages[$message->id_locationOrService.$message->type][$id_partner][$message->id] = $message;
        }

        return json_encode($organizedMessages);
    }
}