@extends('layouts.app')

@section('content')
    <script>
        let existsLocationsOrService = false;
    </script>

    <div class="container-fluid mt-5">
        <div class="row justify-content-center w-background">
            <div class="col-12 mt-5 p-5">
                <h1 class="d-flex justify-content-center">
                    Meus anúncios
                </h1>
            </div>
            @foreach ($locationsNServices as $locationOrService)
                @if ($loop->first)
                    <script>
                        existsLocationsOrService = true;
                    </script>
                @endif
                @if (strval($locationOrService->user_id) === strval($id))
                    @include('layouts/servicesNLocationsCard')
                @endif
            @endforeach
        </div>
    </div>

    <script>
        if (!existsLocationsOrService) {
            let messageContainer = document.createElement('center');
            messageContainer.appendChild(
                document.createTextNode("Você não possui nenhum anúncio de serviço ou aluguel.")
            );

            document.getElementsByClassName("col-12")[0].appendChild(messageContainer);
        }
    </script>
@endsection
