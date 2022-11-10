@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <div class="row justify-content-center p-5 mt-5 w-background">

            <!-- Search row -->
            <form class="input-group col-12 input-group-lg w-background" action="{{ route('home') }}" method="get">
                <input type="text" class="form-control" placeholder="O que procura?" aria-label="serach"
                    aria-describedby="basic-addon2" name="search">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">
                        <script src="https://cdn.lordicon.com/qjzruarw.js"></script>
                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" delay="2000"
                            colors="primary:#121331,secondary:#08a88a" style="width:1.8rem;height:1.8rem">
                        </lord-icon>
                    </button>
                </div>
            </form>
        </div>

        <!-- Start announce row -->
        <div class="row justify-content-center p-5">
            <div class='col-12 d-flex justify-content-center'>
                <div class="btn-group btn-group-lg" role="group">
                    <a class="btn start-announce-button btn-lg pl-5 pr-5" href="{{ route('create-location') }}">
                        Alugue o seu produto
                    </a>
                    <a class="btn start-announce-button pl-5 pr-5" href="{{ route('create-service') }}">
                        Anuncie o seu serviço
                    </a>
                </div>
            </div>
        </div>

        <!-- Exihibition of services and locations -->
        <div class='row d-flex justify-content-center w-background' id='locationsNServices-container'>
            <h1 class='d-flex justify-content-center p-4 col-12'>
                Últimos anúncios
            </h1>
            @foreach ($locationsNServices as $locationOrService)
                @if (isset($_GET['search']) && $_GET['search'] !== '')
                    @if (mb_strpos(strtolower($locationOrService->desc), strtolower($_GET['search'])) !== false)
                        @include('layouts/servicesNLocationsCard')
                    @endif
                @else
                    @include('layouts/servicesNLocationsCard')
                @endif
            @endforeach
        </div>
    </div>
    <script>
        function emptyLocationsNServicesContainer() {
            if (document.getElementById('locationsNServices-container').children.length === 0) {

                document.getElementById('locationsNServices-container').innerHTML =
                    "<div class='col-12 d-flex justify-content-center'>Sua busca não possui resultados</center>";
            }
        }

        document.addEventListener("DOMContentLoaded", function(e) {
            emptyLocationsNServicesContainer();
        });
    </script>
@endsection
