@extends('layouts.app')

@section('content')
<?php
 use App\Estado;
 $estados = new Estado;
?>
    <div class="container-fluid">

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if (Auth::user()->email_verified_at === NULL)
            <div class="alert alert-warning" role="alert">
                O seu e-mail ainda não foi verificado. Para utilizar todos os recursos da HelpAê, <a href="{{ url('email/verify') }}">verifiquei o seu e-mail</a>.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Search row -->
        <div class="row justify-content-center p-5 mt-5 w-background">
            <form class="input-group input-group-lg search_line" action="{{ route('home') }}" method="get">
                <input type="text" class="form-control" placeholder="O que procura?" aria-label="serach" aria-describedby="basic-addon2" name="search"
                    @if(isset($_GET["search"]))
                        value={{$_GET["search"]}}
                    @endif
                >
                <div class="input-group-prepend">
                    <span class="input-group-text search_text">UF</span>
                </div>
                <select class="form-select form-control" aria-label="estado" id="estado" name="estado" onchange="attCities()">
                    @foreach ($estados::all() as $estado)
                        <option value={{$estado->id}} 
                            @if(isset($_GET["estado"]))
                                @if(intval($_GET["estado"]) == $estado->id)
                                    selected
                                @endif
                            @endif
                        >{{$estado->name}}</option>
                    @endforeach
                </select>
                <div class="input-group-prepend">
                    <span class="input-group-text search_text">Cidade</span>
                </div>
                <select class="form-select form-control" aria-label="estado" name="cidade" id="cidade" disabled>
                </select>
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary search_button" type="submit">
                        <script src="https://cdn.lordicon.com/qjzruarw.js"></script>
                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" delay="2000"
                            colors="primary:#121331,secondary:#2b58e7" style="width:1.8rem;height:1.8rem">
                        </lord-icon>
                    </button>
                </div>
            </form>
        </div>

        <!-- Start announce row -->
        <div class="row justify-content-center p-5">
            <div class='col-12 d-flex justify-content-center'>
                <div class="btn-group btn-group-lg" role="group">
                    <a type="button" class="btn start-announce-button btn-lg pl-5 pr-5" href="{{ route('create-location') }}">
                        Alugue o seu produto
                    </a>
                    <a class="btn start-announce-button pl-5 pr-5
                        @if (Auth::user()->cnpj === NULL)
                            disabled
                        @endif"
                        
                        @if (Auth::user()->cnpj === NULL)
                            href="#"
                            style="pointer-events: auto;"
                            data-toggle="tooltip"
                            data-html="true"
                            data-delay='{"show":"0", "hide":"3000"}'
                            title="Você deve adicionar o seu cnpj para anunciar o seu produto para aluguel. Adicione no seu <a href={{route('panel')}}>painel</a>"
                        @else
                            href="{{ route('create-service') }}"
                        @endif
                    >
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
                    @if (mb_strpos(strtolower($locationOrService->desc), strtolower($_GET['search'])) !== false && $locationOrService->cidade == $_GET["cidade"])
                        @if (!$locationOrService->suspended)
                            @include('layouts/servicesNLocationsCard')
                        @endif
                    @endif
                @else
                    @if (!$locationOrService->suspended)
                        @include('layouts/servicesNLocationsCard')
                    @endif
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

        //Function that att cities based on UF values
        function attCities(){
            async function renderCities() {
                const options = {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        uf_id: document.getElementById('estado').value
                    })
                }



                let citiesList = document.getElementById('cidade');
                citiesList.innerHTML = "";
                citiesList.disabled = true;

                const response = await fetch("{{ route('show-cities') }}", options)
                 .then((response) => response.json())
                 .then((data) => {
                    
                    data.forEach(city =>{
                        let cityOption =  document.createElement("option");
                        cityOption.value = city.id;

                        var queryDict = {}
                        location.search.substr(1).split("&").forEach(function(item) {queryDict[item.split("=")[0]] = item.split("=")[1]})
                        if (queryDict.cidade !== undefined && queryDict.cidade == city.id){
                            cityOption.selected = true;
                        }

                        const cityName = document.createTextNode(city.name);

                        cityOption.appendChild(cityName);
                        citiesList.appendChild(cityOption);
                        citiesList.disabled = false;
                    })
                 });
            }

            renderCities();
        }

        attCities();
    </script>
@endsection
