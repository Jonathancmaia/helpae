@extends('layouts.app')

@section('content')
    <?php
    
    use App\Rate;
    use App\Comment;
    use App\User;
    
    $rate = new Rate();
    $comment = new Comment();
    $user = new User();
    
    $ratings = Rate::where('to', '=', Request::segment(2))->get();
    $comments = Comment::where('to', '=', Request::segment(2))->get();
    
    $numOfRatings = count($ratings);
    
    //User average rating definition
    $mediaOfRatings = 0;
    $i = 0;
    foreach ($ratings as $rating) {
        $i++;
        $mediaOfRatings = $mediaOfRatings + $rating->grade;
    }
    
    if ($i != 0) {
        $mediaOfRatings = ceil($mediaOfRatings / $i);
    } else {
        $mediaOfRatings = 0;
    }
    
    ?>

    <div class="container-fluid w-background mt-5 p-5" onLoad="descriptionValidator()">

        <!-- PROFILE SESSION -->
        <div class="row justify-content-center">
            <div class="col-4 d-flex justify-content-center">
                @if ($pic != null)
                    <img class="profile-pic" src={{ Storage::url('user-pic/' . $pic) }} />
                @else
                    <img class="profile-pic" src={{ Storage::url('user-pic/default.jpg') }} />
                @endif
            </div>
            <div class="col-8 user-data-container">
                <h3>
                    {{ $name }}
                </h3>
                <h5>
                    {{ $email }}
                </h5>
                <div>
                    <!-- User rating -->
                    <form class="rating-container" action={{ route('rate-user') }} method="post">
                        @csrf
                        <input type="hidden" name="to" value={{ $id }}>
                        <label id="star-5" class="star">
                            <input type="radio" name="grade" onchange="this.form.submit()" value="5" />
                        </label>
                        <label id="star-4" class="star">
                            <input type="radio" name="grade" onchange="this.form.submit()" value="4" />
                        </label>
                        <label id="star-3" class="star">
                            <input type="radio" name="grade" onchange="this.form.submit()" value="3" />
                        </label>
                        <label id="star-2" class="star">
                            <input type="radio" name="grade" onchange="this.form.submit()" value="2" />
                        </label>
                        <label id="star-1" class="star">
                            <input type="radio" name="grade" onchange="this.form.submit()" value="1" />
                        </label>
                    </form>

                    <small>
                        Média: {{ $mediaOfRatings }}
                    </small>
                    <br />
                    <small>
                        Número de avaliações: {{ $numOfRatings }}
                    </small>

                    <!-- Show if user is vip -->
                    @if ($isVip !== 0)
                        <div>
                            Este usuário é vip.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- USER'S SERVICE AND LOCATIONS SESSIONS -->
        <hr class="mt-5 mb-5" />
        <div class="row d-flex justify-content-center">
            <h1>
                Anúncios deste usuário
            </h1>
        </div>
        <div class="row locationsNServices-cntainer">
            @foreach ($locationsNServices as $locationOrService)
                @if ($locationOrService->user_id == $id)
                    @include('layouts/servicesNLocationsCard')
                @endif
            @endforeach
        </div>

        <!-- COMMENTS SESSION -->
        <hr class="mt-5 mb-5" />
        <div class="row">
            <div class="col-12">
                <form action={{ route('post-comment') }} method="post">
                    @csrf
                    <input type="hidden" name="to" value={{ $id }}>
                    <div class="form-group">
                        <label for="comment">Commentário:</label>
                        <textarea class="form-control" name="comment" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn panel-button btn-block mb-2">Enviar Comentário</button>
                </form>
            </div>
        </div>
        <div class="row">
            @foreach ($comments as $comment)
                <?php
                $user = User::where('id', $comment->from)->get(['id', 'name', 'updated_at', 'created_at']);
                ?>
                <div
                    class="comment-container col-12 m-2
                    @if ($user[0]->id == Auth::user()->id) my-comment @endif
                ">
                    <div class="comment-header p-2">
                        <div>
                            Usuário: {{ $user[0]->name }}
                        </div>
                        <div>
                            Comentado em: {{ $comment->created_at }}
                        </div>
                        @if ($comment->created_at != $comment->updated_at)
                            <div>
                                Editado em: {{ $comment->updated_at }}
                            </div>
                        @endif
                    </div>
                    <div class="comment-body p-2 w-100">
                        {{ $comment->comment }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        let media = {{ $mediaOfRatings }};

        for (i = 1; i <= media;) {
            let starId = "star-" + i;
            document.getElementById(starId).classList.add('active-star');
            i++;
        }
    </script>
@endsection
