@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 w-background w-100 mt-5 p-5">

                <!-- Add a pic form -->
                <form class='mb-5' method='post' action='{{ route('add-pic') }}' enctype="multipart/form-data">
                    @csrf
                    <h2>
                        Adicionar ou Alterar foto de perfil
                    </h2>
                    <hr />
                    <div class="d-flex">
                        <div class="form-group col-8 alig-self-ceter">
                            <label for="midia">Upload</label>
                            <input type="file" id="midia" name='pic'>
                            <p class="help-block">
                                Envie uma foto para o seu perfil.
                            </p>
                            <button type="submit" class="btn panel-button">
                                Enviar Foto
                            </button>
                        </div>

                        <div class="col-4 d-flex justify-content-center">
                            @if (Auth::user()->pic != null)
                                <img class="profile-pic" src={{ Storage::url('user-pic/' . Auth::user()->pic) }} />
                            @else
                                <img class="profile-pic" src={{ Storage::url('user-pic/default.jpg') }} />
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Change name form -->
                <form method='post' action='{{ route('change-name') }}'>
                    @csrf
                    <h2>
                        Mudar nome
                    </h2>
                    <hr />
                    <div class="form-group">
                        <label for="name">Nome</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value='{{ $name }}'>
                    </div>
                    <div class="form-group">
                        <button type='input' class='btn panel-button' id="newNameButton">
                            Mudar Nome
                        </button>
                    </div>
                </form>

                <!-- Change e-mail form -->
                <form class='mt-5' method='post' action='{{ route('change-email') }}'>
                    @csrf
                    <h2>
                        Mudar E-mail
                    </h2>
                    <hr />
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email"
                            aria-describedby="emailHelp" value='{{ $email }}' />
                        <small id="emailHelp" class="form-text text-muted">
                            Seu e-mail será compartilhado para outros usuários como forma de contato.
                        </small>
                        <div class="form-group">
                            <button type='input' class='btn panel-button' id="newEmailButton">
                                Mudar E-mail
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Turn vip link -->
                <div class="form-group mt-5">
                    <h2>
                        Status vip
                    </h2>
                    <hr />
                    Alavanque seus anúncios com o vip!
                    <br />
                    @if ($isVip)
                        <small>Você é vip. Aproveite seu benefício!</small>
                    @else
                        <small>Você não é vip.</small>
                        <br />
                        <a href='{{ route('turnVipForm') }}'>
                            <button type='button' class='btn panel-button'>
                                Tornar-se vip
                            </button>
                        </a>
                    @endif
                </div>

                <!-- Change password form -->
                <form class='mt-5' method='post' action='{{ route('change-password') }}'>
                    @csrf
                    <h2>
                        Mudar senha
                    </h2>
                    <hr />
                    <div class="form-group">
                        <label for="senha">Senha atual</label>
                        <input type="password" class="form-control" id="senha" name="password"
                            aria-describedby="password" onkeyup="verifyPassword()" />
                        <small id="passwordHelp" class="form-text text-muted">Digite a sua senha atual.</small>
                    </div>
                    <div class="form-group">
                        <label for="novaSenha">Nova senha</label>
                        <input type="password" class="form-control" id="novaSenha" aria-describedby="passwordChange"
                            name="newPassword" onkeyup="verifyPassword()" />
                        <small id="passwordHelp" class="form-text text-muted">Digite a sua nova senha.</small>
                    </div>
                    <div class="form-group">
                        <label for="senhaConf">Confirme sua nova senha</label>
                        <input type="password" class="form-control" id="senhaConf" aria-describedby="passwordChangeConf"
                            name="newPasswordConf" onkeyup="verifyPassword()" disabled>
                        <small id="passwordHelp" class="form-text text-muted">Digite novamente a sua nova senha.</small>
                    </div>
                    <div class="form-group">
                        <button type='input' class='btn panel-button' id="newPasswordButton" disabled>
                            Mudar Senha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function verifyPassword() {
            let newPasswordField = document.getElementById('novaSenha');
            let newPasswordConfField = document.getElementById('senhaConf');
            let newPasswordButton = document.getElementById('newPasswordButton');

            if (newPasswordField.value.length >= 8 && newPasswordField.value.length <= 32) {

                newPasswordConfField.removeAttribute("disabled");

                if (newPasswordField.value === newPasswordConfField.value) {
                    newPasswordButton.removeAttribute("disabled");
                } else {
                    if (!newPasswordButton.disabled) {
                        newPasswordButton.setAttribute("disabled", true);
                    }
                }

            } else {
                if (!newPasswordConfField.disabled) {
                    newPasswordConfField.setAttribute("disabled", true);
                }
            }
        }
    </script>
@endsection
