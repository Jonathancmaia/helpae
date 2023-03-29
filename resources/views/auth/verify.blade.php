@extends('layouts.app')

@section('content')
<div class="container-fluid w-background mt-5 p-5">
    <div class="row justify-content-center">
        <div class="col-12">
                <h2>Verifique o seu endereço de e-mail</h2>

                <div>
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            Um link de verificação será enviado para o seu e-mail.
                        </div>
                    @endif

                    Se você ainda não verificou o seu e-mail,
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('clique aqui para solictar outro.') }}</button>.
                    </form>
                </div>
        </div>
    </div>
</div>
@endsection
