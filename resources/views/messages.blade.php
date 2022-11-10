 @extends('layouts.app')

 @section('content')
     <?php
     use App\Location_pic;
     use App\Service_pic;
     use App\Location;
     use App\Service;
     use App\user;
     
     $location_pic = new Location_pic();
     $service_pic = new Service_pic();
     $location = new Location();
     $service = new Service();
     $user = new User();
     
     ?>
     <div class="container-fluid mt-5">
         <div class="row justify-content-center w-background">
             <h1 class="col-12 d-flex w-100 justify-content-center p-5">
                 Mensagens
             </h1>
             <div class="col-md-12" id='conversations-container'>
             </div>
         </div>
     </div>

     <script>
         //Scrolls chat
         function scrolls(messageInputId) {
             const messageInput = document.getElementById(messageInputId);
             const conversationCointainer = document.getElementById('chatContainer');
             conversationCointainer.scrollTop = conversationCointainer.scrollHeight;
         }

         //Function that show/hide conversations
         let selectedConversation = null;

         function selectConversation(conversationId) {
             selectedConversation = conversationId;
             document.getElementById(conversationId.replace("chat", "")).classList.add('active-conversation');

             //Show which conversation is active
             Object.keys(document.getElementsByClassName("user-conversation")).forEach((index) => {
                 document.getElementsByClassName("user-conversation")[index].classList.remove('active-conversation');
             });
             document.getElementById(conversationId.replace("chat", "")).classList.add('active-conversation');


             //Show or hide conversations
             Object.keys(document.getElementsByClassName("messages-container")).forEach((index) => {
                 if (document.getElementsByClassName("messages-container")[index].classList.contains("invisible")) {

                     if (document.getElementsByClassName("messages-container")[index].id === conversationId) {
                         document.getElementsByClassName("messages-container")[index].classList.remove("invisible");
                     }
                 } else {
                     document.getElementsByClassName("messages-container")[index].classList.add("invisible");
                 }
             });

             scrolls(conversationId.replace("chat", "message"));
         }

         //Function that render the chat
         async function renderConversation() {
             const options = {
                 method: "POST",
                 headers: {
                     'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                     'Content-Type': 'application/json'
                 }
             }

             const response = await fetch("{{ route('show-messages') }}", options)
                 .then((response) => response.json())
                 .then((data) => {

                     //Clear chat-container
                     document.getElementById('conversations-container').innerText = "";

                     //Creating location or service list container
                     let locationOrServicerListContainer = document.createElement("div");
                     locationOrServicerListContainer.id = "locationOrServicerListContainer";

                     //Creating chat container
                     let chatContainer = document.createElement("div");
                     chatContainer.id = "chatContainer";

                     //Adding divs to document
                     document.getElementById('conversations-container').appendChild(locationOrServicerListContainer);
                     document.getElementById('conversations-container').appendChild(chatContainer);

                     //Location or services loop
                     Object.keys(data).forEach(locationOrServiceKey => {

                         //Adding location or service to container list
                         let locationOrServiceContainer = document.createElement("div");
                         locationOrServiceContainer.classList.add("LocationOrServiceContainer");
                         locationOrServiceContainer.id = locationOrServiceKey;

                         let locationOrServiceContainerHeader = document.createElement("div");
                         locationOrServiceContainerHeader.classList.add("LocationOrServiceHeader");

                         let locationOrServiceContainerBody = document.createElement("ul");
                         locationOrServiceContainerBody.classList.add("LocationOrServiceBody");

                         //Adding divs to document
                         locationOrServicerListContainer.appendChild(locationOrServiceContainer);
                         locationOrServiceContainer.appendChild(locationOrServiceContainerHeader);
                         locationOrServiceContainer.appendChild(locationOrServiceContainerBody);

                         //Print name of service or location on card
                         fetch(
                                 locationOrServiceKey.slice(-1) === "l" ?
                                 "{{ route('desc-location') }}" :
                                 locationOrServiceKey.slice(-1) === "s" ?
                                 "{{ route('desc-service') }}" : "Invalid type.", {
                                     method: "POST",
                                     headers: {
                                         'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')
                                             .value,
                                         'Content-Type': 'application/json'
                                     },
                                     body: JSON.stringify({
                                         id: locationOrServiceKey.slice(0, locationOrServiceKey
                                             .length - 1)
                                     })
                                 })
                             .then((response) => response.json())
                             .then((locationOrServiceData) => {
                                 locationOrServiceContainerHeader.appendChild(
                                     document.createTextNode(locationOrServiceData.desc)
                                 );

                                 Object.keys(data[locationOrServiceKey]).forEach(userKey => {

                                     //Print user name and e-mail
                                     fetch(
                                             "{{ route('getData-user') }}", {
                                                 method: "POST",
                                                 headers: {
                                                     'X-CSRF-TOKEN': document.querySelector(
                                                             'input[name="_token"]')
                                                         .value,
                                                     'Content-Type': 'application/json'
                                                 },
                                                 body: JSON.stringify({
                                                     id: userKey
                                                 })
                                             })
                                         .then((response) => response.json())
                                         .then((userData) => {

                                             //Add user conversation card to document
                                             let userConversationContainer = document
                                                 .createElement(
                                                     "li");
                                             userConversationContainer.id =
                                                 locationOrServiceKey +
                                                 userKey;
                                             userConversationContainer.onclick = () => {
                                                 selectConversation("chat" +
                                                     locationOrServiceKey +
                                                     userKey);
                                             }
                                             userConversationContainer.classList.add(
                                                 "user-conversation");
                                             locationOrServiceContainerBody.appendChild(
                                                 userConversationContainer);
                                             userConversationContainer.appendChild(
                                                 document.createTextNode(userData.name +
                                                     " ➢ " + userData.email)
                                             );

                                             //Add chat to document
                                             let messagesContainer = document.createElement(
                                                 "div");
                                             messagesContainer.classList.add(
                                                 "messages-container");
                                             messagesContainer.id = 'chat' +
                                                 locationOrServiceKey +
                                                 userKey;
                                             if (selectedConversation !== messagesContainer
                                                 .id) {
                                                 messagesContainer.classList.add(
                                                     "invisible");
                                             }
                                             chatContainer.appendChild(messagesContainer);

                                             //Select first conversation if necessary
                                             if (selectedConversation === null) {
                                                 selectConversation("chat" +
                                                     locationOrServiceKey +
                                                     userKey)
                                             }

                                             //Add messages to chat-container
                                             Object.keys(data[locationOrServiceKey][userKey])
                                                 .forEach(
                                                     messageKey => {

                                                         let messageContainer = document
                                                             .createElement(
                                                                 "div");
                                                         let messageSpan = document
                                                             .createElement(
                                                                 "span");
                                                         messageContainer.classList.add(
                                                             "message");

                                                         //add 'my-message' class to user messages
                                                         if (String(data[
                                                                     locationOrServiceKey][
                                                                     userKey
                                                                 ][
                                                                     messageKey
                                                                 ]
                                                                 .id_sender) === String(
                                                                 {{ Auth::user()->id }})) {
                                                             messageContainer.classList.add(
                                                                 "my-message");
                                                         }

                                                         messageSpan.appendChild(
                                                             document.createTextNode(
                                                                 data[
                                                                     locationOrServiceKey
                                                                 ][userKey][
                                                                     messageKey
                                                                 ]
                                                                 .message
                                                             )
                                                         );

                                                         messageContainer.appendChild(
                                                             messageSpan);
                                                         messagesContainer.appendChild(
                                                             messageContainer);
                                                     });

                                             //Creating send messag form
                                             let messageForm = document.createElement("div");
                                             messageForm.classList.add('input-group');
                                             messageForm.classList.add('pt-5');
                                             messageForm.classList.add(
                                                 'input-group-messagesView');
                                             let messageField = document.createElement(
                                                 "input");
                                             messageField.classList.add("form-control");
                                             messageField.classList.add("message-field");
                                             messageField.placeholder = "Mesnsagem";
                                             messageField.id = "message" +
                                                 locationOrServiceKey +
                                                 userKey;
                                             let messageButton = document.createElement(
                                                 "button");
                                             messageButton.classList.add("btn");
                                             messageButton.classList.add("panel-button");
                                             messageButton.type = "button";
                                             messageButton.id = "button" +
                                                 locationOrServiceKey +
                                                 userKey;
                                             messageButton.onclick = () => {
                                                 sendMessage(locationOrServiceKey,
                                                     userKey);
                                             }
                                             messageButton.appendChild(document
                                                 .createTextNode(
                                                     "enviar"));

                                             messageForm.appendChild(messageField);
                                             messageForm.appendChild(messageButton);

                                             //Add send message form
                                             messagesContainer.appendChild(messageForm);

                                             scrolls(messageField.id);
                                         });
                                 });
                             });
                     });
                 })
                 .catch((error) => {
                     console.error('Error:', error);
                 });
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
                     message: document.getElementById("message" + locationOrServiceKey + userKey).value
                 })
             }

             const response = await fetch("{{ route('store-message') }}", options)
                 .then((response) => response.json())
                 .then((data) => {
                     if (data) {
                         document.getElementById("message" + locationOrServiceKey + userKey).value = "";
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
