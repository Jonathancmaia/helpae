@extends('layouts.app')

@section('content')
<div class="container-fluid w-background mt-5 p-5">
    <div class="row justify-content-center">
        <div class="col-12">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <h2>
                    Registre-se
                </h2>
                <hr/>
                <div class="form-group row mt-5">
                    <label for="id" class="col-md-4 col-form-label text-md-right">CPF</label>

                    <div class="col-md-6">
                        <input id="id" type="number" class="form-control @error('id') is-invalid @enderror" name="id" required autofocus>

                        @error('id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nome') }}</label>

                    <div class="col-md-6">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="email" class="col-md-4 col-form-label text-md-right">E-mail</label>

                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label text-md-right">Senha</label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">''
                    <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirme sua senha</label>

                    <div class="col-md-6">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6 offset-md-4">
                        <small class="form-text text-muted">
                            Ao clicar em "Registre-se", você concorda que leu e está de acordo com os <a href="{{ route('terms') }}">termos de serviço<a>.
                        </small>
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn panel-button">
                            Registre-se
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
