<div class="col-md-3 d-flex justify-content-center">
    <div class="card">
        @if (sizeof($locationOrService->pics) != 0)
            <div id={{ 'carousel' . $locationOrService->id . $locationOrService->type }} class="carousel slide"
                data-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($locationOrService->pics as $pic)
                        <!-- EXIBIR CARROSSEL DE FOTOS -->
                        <div
                            @if ($loop->first) class="carousel-item active"
                          @else
                              class="carousel-item" @endif>
                            <img class="d-block w-100 rounded"
                                src={{ Storage::url($locationOrService->type . '-pic/' . $pic->pic_id) }}
                                alt="Third slide">
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <img src={{ Storage::url($locationOrService->type . '-pic/default.jpg') }} class="card-img-top rounded" />
        @endif
        <div class="card-body">
            <h5 class="card-title">
                {{ $locationOrService->type === 'location' ? 'Locação' : 'Serviço' }}
            </h5>
            <p class="card-text">{{ $locationOrService->desc }}</p>
            <p class="card-text">R$ {{ $locationOrService->value }}</p>
        </div>
        <div>
            @if ((int) $locationOrService->user_id === (int) Auth::user()->id)
                <a href={{ $locationOrService->type === 'location'
                    ? 'show-location/' . $locationOrService->id
                    : 'show-service/' . $locationOrService->id }}
                    class='btn btn-warning btn-block'>
                    Edite sua publicação
                </a>
            @else
                <a href={{ $locationOrService->type === 'location'
                    ? 'show-location/' . $locationOrService->id
                    : 'show-service/' . $locationOrService->id }}
                    class='btn contract-button btn-block'>
                    Contrate
                </a>
            @endif
        </div>
    </div>
</div>
