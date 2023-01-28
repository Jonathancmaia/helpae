@extends('layouts.app')

@section('content')
    <?php
    use App\Location;
    use App\Location_pic;
    $location = new Location();
    $location_pic = new Location_pic();

    $isSuspended = $location::find(Request::segment(2))->suspended;
    ?>
    <script>
        let existsMidia = false;
    </script>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 w-background mt-5 p-5">

                <div>
                    <h1 class="d-flex justify-content-center">
                        Enviar mídia
                    </h1>
                    <hr />
                    <div>
                        <h2>
                            Arquivos enviados
                        </h2>
                        <div class='row midia-container'>
                            @foreach ($location_pic::where('location_id', $location::find(Request::segment(2))->id)->get() as $pic)
                                @if ($loop->first)
                                    <script>
                                        existsMidia = true;
                                    </script>
                                @endif
                                <div class='col-3 sended_pics_container'>
                                    <form method='post' action='{{ route('delete_location_pic') }}'>
                                        @csrf
                                        <input type='hidden' value={{ $pic->pic_id }} name='pic_id' />
                                        <button type='input'
                                            class='delete-button btn-sm btn btn-outline-danger btn-block'>
                                            Apagar
                                        </button>
                                    </form>
                                    <img src={{ Storage::url('location-pic/' . $pic->pic_id) }}
                                        class='rounded sended_pics' />
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <hr />
                    <form method="post" action="{{ route('store_location_pic') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="midia">Upload</label>
                            <input type="hidden" name="location_id" value={{ Request::segment(2) }} />
                            <input type="file" id="midia" name='pic'>
                            <p class="help-block">
                                Envie foto ou vídeo do seu produto.
                            </p>
                        </div>
                        <button type="submit" class="btn panel-button btn-lg btn-block">
                            Enviar mídia
                        </button>
                    </form>
                    <hr/>
                    <form method="post" action="{{ route('suspend-location') }}">
                        @csrf
                        <h2>
                            @if ($isSuspended)
                                Remover suspenção da publicação
                            @else
                                Suspender publicação
                            @endif
                        </h2>
                        <label>Ao suspender a sua publicação, a mesma não estará mais visível. A suspensão pode ser cancelada a qualquer momento.</label>
                        <input type="hidden" value={{Request::segment(2)}} name="id"/>
                        <button type="submit" class="btn
                            @if($isSuspended)
                                btn-success
                            @else
                                btn-warning
                            @endif
                        btn-lg btn-block">
                            @if ($isSuspended)
                                Remover
                            @else
                                Suspender
                            @endif
                        </button>
                    </form>
                    <hr/>
                    <form method="post" action="{{ route('delete-location') }}">
                        @csrf
                        <div class="form-group">
                            <h2>Deletar esta publicação.</h2>
                            <label>Tenha certeza ao realizar esta ação. Todas as mídias serão apagadas.</label>
                            <input type="hidden" value={{Request::segment(2)}} name="id"/>
                        </div>
                        <button type="submit" class="btn btn-danger btn-lg btn-block">
                            Deletar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            if (!existsMidia) {
                let messageContainer = document.createElement('center');
                messageContainer.appendChild(
                    document.createTextNode("Você não possui nenhuma mídia no seu anúncio de aluguel.")
                );

                document.getElementsByClassName("midia-container")[0].appendChild(messageContainer);
            }
        </script>
    @endsection
