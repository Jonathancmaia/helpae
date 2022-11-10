@extends('layouts.app')

@section('content')
    <?php
    use App\Location;
    use App\Location_pic;
    use App\User;
    
    $location = new Location();
    $location_pic = new Location_pic();
    $user = new User();
    
    $location_id = $location::find(Request::segment(2));
    
    $user_offered = $user::where('id', $location::find(Request::segment(2))->first()->user_id)->first();
    ?>
    <style>
        #conversation-cointainer {
            height: 50vh;
            overflow-x: auto
        }
    </style>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 w-background mt-5">
                <h1 class="p-5">
                    Aluge esta ferramenta
                </h1>
                <hr />
                <div class="d-flex flex-row justify-content-center">
                    <div id="carousel-show" class="carousel slide carousel-show" data-ride="carousel">
                        <div class="carousel-inner">
                            @if ($location_pic::where('location_id', $location::find(Request::segment(2))->id)->get() !== null)
                                @foreach ($location_pic::where('location_id', $location::find(Request::segment(2))->id)->get() as $pic)
                                    <div
                                        @if ($loop->first) class="carousel-item active"
                        @else
                            class="carousel-item" @endif>
                                        <img class="d-block w-100 rounded"
                                            src={{ Storage::url('location-pic/' . $pic->pic_id) }} alt="First slide">
                                    </div>
                                @endforeach
                            @else
                                <div
                                    @if ($loop->first) class="carousel-item active"
                        @else
                            class="carousel-item" @endif>
                                    <img class="d-block w-100 rounded"
                                        src={{ Storage::url('location-pic/default.jpg') }}alt="First slide">
                                </div>
                            @endif
                        </div>
                        <a class="carousel-control-prev" href="#carousel-show" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carousel-show" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                    <div class='d-flex flex-column p-4'>
                        <h2 class='p-3'>{{ $data->desc }}</h2>
                        <h4 class='p-3'>R$ {{ $data->value }}</h4>
                    </div>
                </div>
                <hr />
                <div class="p-3">
                    <h1>{{ $user_offered->name }}</h1>
                    <small>{{ $user_offered->email }}</small>
                </div>
                <hr />
                <div id="conversation-cointainer" class="p-5">
                </div>
            </div>
        </div>

        <script>
            //Scrolls chat
            function scrolls() {
                const messageInput = document.getElementById('message');
                const conversationCointainer = document.getElementById('conversation-cointainer');
                conversationCointainer.scrollTop = conversationCointainer.scrollHeight;
            }

            async function renderConversation() {
                const options = {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_locationOrService: 1,
                        type: 'l'
                    })
                }

                const response = await fetch("{{ route('show-messages') }}", options)
                    .then((response) => response.json())
                    .then((data) => {

                        //Clear chat-container
                        document.getElementById('conversation-cointainer').innerText = "";

                        let chatContainer = document.createElement("div");
                        chatContainer.id = "fullChatContainer";

                        document.getElementById('conversation-cointainer').appendChild(chatContainer);

                        //Add chat to document
                        let messagesContainer = document.createElement("div");
                        messagesContainer.classList.add("messages-container");
                        chatContainer.appendChild(messagesContainer);

                        //Add messages to chat-container
                        let messages = data[Object.keys(data)[0]][Object.keys(data[Object.keys(data)[0]])[0]];
                        Object.keys(messages).forEach(messageKey => {

                            let messageContainer = document.createElement("div");
                            let messageSpan = document.createElement("span");
                            messageContainer.classList.add("message");

                            //add 'my-message' class to user messages
                            if (String(messages[messageKey].id_sender) === String({{ Auth::user()->id }})) {
                                messageContainer.classList.add("my-message");
                            }

                            messageSpan.appendChild(
                                document.createTextNode(
                                    messages[messageKey].message
                                )
                            );

                            messageContainer.appendChild(messageSpan);
                            messagesContainer.appendChild(messageContainer);
                        });

                        //Creating send messag form
                        let messageForm = document.createElement("div");
                        messageForm.classList.add("input-group");
                        messageForm.classList.add("mt-5");
                        let messageField = document.createElement("input");
                        messageField.classList.add("form-control");
                        messageField.placeholder = "Mesnsagem";
                        messageField.id = "message";
                        let messageButton = document.createElement("button");
                        messageButton.classList.add("btn");
                        messageButton.classList.add("panel-button");
                        messageButton.type = "button";
                        messageButton.onclick = () => {
                            sendMessage(Object.keys(data)[0], Object.keys(data[Object.keys(data)[0]])[0]);
                        }
                        messageButton.appendChild(document.createTextNode("enviar"));

                        messageForm.appendChild(messageField);
                        messageForm.appendChild(messageButton);

                        //Add send message form
                        messagesContainer.appendChild(messageForm);
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });
                scrolls();
            }

            renderConversation();

            async function sendMessage(locationOrServiceKey, userKey) {

                const options = {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_locationOrService: locationOrServiceKey.substr(0, locationOrServiceKey.length - 1),
                        type: locationOrServiceKey.substr(-1),
                        partner: userKey,
                        message: document.getElementById("message").value
                    })
                }

                const response = await fetch("{{ route('store-message') }}", options)
                    .then((response) => response.json())
                    .then((data) => {
                        if (data) {
                            document.getElementById("message").value = "";
                            renderConversation();
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });
            }

            //new message event listener
            window.Echo.private('App.User.' + {{ Auth::user()->id }}).
            listen('chatNotificate', (e) => {
                renderConversation();
            });
        </script>
    @endsection
