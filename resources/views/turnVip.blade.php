@extends('layouts.app')

@section('content')
<div>
  <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Torne-se um vip!') }}</div>

                <div class="card-body justify-content-center">
                  <form
                    action='{{ route('turnVip') }}'
                    method="post"
                  >
                    @csrf
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-12 justify-content-center d-flex mb-5">
                          <h1>
                            Escolha a melhor opção
                          </h1>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-4 d-flex justify-content-center">
                          <div class="form-check form-check-inline">
                            <div class='h-100 w-100 flex-column'>
                              <label class="form-check-label" for="inlineRadio1">
                                <center>
                                  <h3>
                                    Plano mensal
                                  </h3>
                                </center>
                              </label>
                              <small id="passwordHelp" class="form-text text-muted d-flex justify-content-center mb-2">
                                Valor sem desconto.
                              </small>
                              <div class="w-100">
                                <center>
                                  <h4>
                                    Por apenas
                                    <br/>
                                    R$24,99
                                  </h4>
                                </center>
                              </div>
                              <input class="form-check-input w-100" type="radio" name="vipOption" id="inlineRadio1" value="1" checked>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-4 d-flex justify-content-center">
                          <div class="form-check form-check-inline">
                            <div class='h-100 w-100 d-flex flex-column'>
                              <label class="form-check-label" for="inlineRadio2">
                                <center>
                                  <h3>
                                    Plano semestral
                                  </h3>
                                </center>
                              </label>
                              <small id="passwordHelp" class="form-text text-muted d-flex justify-content-center mb-2">
                                Valor com 5% de desconto.
                              </small>
                              <div class="w-100">
                                <center>
                                  <h4>
                                    Por apenas
                                    <br/>
                                    R$141,49
                                  </h4>
                                </center>
                              </div>
                              <input class="form-check-input w-100" type="radio" name="vipOption" id="inlineRadio2" value="2">
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-4 d-flex justify-content-center">
                          <div class="form-check form-check-inline">
                            <div class='h-100 w-100 d-flex flex-column'>
                              <label class="form-check-label" for="inlineRadio3">
                                <center>
                                  <h3>
                                    Plano anual
                                  </h3>
                                </center>
                              </label>
                              <small id="passwordHelp" class="form-text text-muted d-flex justify-content-center mb-2">
                                Valor com 15% de deconto.
                              </small>
                              <div class="w-100">
                                <center>
                                  <h4>
                                    Por apenas
                                    <br/>
                                    R$259,99
                                  </h4>
                                </center>
                              </div>
                              <input class="form-check-input w-100" type="radio" name="vipOption" id="inlineRadio3" value="3">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class='mt-5 d-flex justify-content-center'>
                      <button type="submit" class="btn btn-primary btn-lg btn-block">
                        Torne-se um usuário vip!
                      </button>
                    </div>
                  </form>
                </div>
              </div>
          </div>
      </div>
    </div>
  </div>
</div>
@endsection