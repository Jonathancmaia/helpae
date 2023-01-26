@extends('layouts.app')

@section('content')
<?php
 use App\Estado;
 $estados = new Estado;
?>
    <div class="container-fluid w-background mt-5 p-5" onLoad="descriptionValidator()">
        <div class="row justify-content-center">
            <div class="col-12">
                <form method="post" action="{{ route('store-service') }}">
                    @csrf

                    <h2>
                        Anuncie o seu serviço
                    </h2>

                    <hr />

                    <!-- Description input -->
                    <div class="input-group mt-5">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="desc">
                                Descreva o seu serviço
                            </span>
                        </div>
                        <textarea id="description" name="desc" type="text" class="form-control" placeholder="O meu serviço é..."
                            aria-label="desc" aria-describedby="desc" oninput="descriptionValidator()" maxlength="300"></textarea>
                        <div class="container">
                            <div class="d-flex justify-content-end">
                                <small id="characters-counter"></small><small>/300</small>
                            </div>
                        </div>
                    </div>

                    <!-- Value input -->
                    <div class="input-group mt-5">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="">Valor do seu serviço ( R$ )</span>
                        </div>
                        <input type="number" class="form-control" placeholder="10" name="valueR" oninput="realValidator()"
                            id="valueR">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="">,</span>
                        </div>
                        <input type="number" class="form-control" name="valueC" id="valueC" placeholder="00"
                            oninput="centavosValidator()">
                    </div>

                    <!-- Where is input -->
                    <div class="input-group mt-5">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Estado</span>
                        </div>
                        <select class="form-select form-control" aria-label="estado" id="estado" name="estado" onchange="attCities()">
                            @foreach ($estados::all() as $estado)
                                <option value={{$estado->id}}>{{$estado->name}}</option>
                            @endforeach
                        </select>
                        <div class="input-group-prepend">
                            <span class="input-group-text">Cidade</span>
                        </div>
                        <select class="form-select form-control" aria-label="estado" name="cidade" id="cidade" disabled>
                        </select>
                    </div>

                    <!-- Submit input -->
                    <div class="input-group mt-5">
                        <input type="submit" class="btn panel-button btn-block" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Javascript functions -->
    <script>
        //Function that validate and limits description field
        function descriptionValidator() {
            let actualCharacters = document.getElementById("description").value.length;
            document.getElementById("characters-counter").innerHTML = actualCharacters;
        }

        descriptionValidator();

        //Function that limits value on REAL field
        function realValidator() {

            let realField = document.getElementById("valueR");
            if (realField.value.length > 5) {
                realField.value = realField.value.slice(0, 5);
            }
        }

        //Function that limits value on REAL field
        function centavosValidator() {

            let centavosField = document.getElementById("valueC");
            if (centavosField.value.length > 2) {
                centavosField.value = centavosField.value.slice(0, 2);
            }
        }

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
