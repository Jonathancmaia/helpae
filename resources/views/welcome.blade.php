<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>

            /* Animated background */
            * {
                margin: 0px;
                padding: 0px;
                font-family: "Nunito", sans-serif;
                color: var(--branco);
            }

            a:visited{
                color: #fff;
            }

            a{
                text-decoration: none;
            }

            nav{
                display: flex;
                flex-direction: row-reverse;
                align-items: center;
                font-size: 1.4rem;
                margin: 0rem 1rem 0rem 1rem;
                font-weight: 800;
                height: 10vh;
            }

            h1{
                display: flex;
                justify-content: center;
                align-items: center;
                height: 90vh;
                font-size: 4rem;
            }

            section{
                padding: 1.2rem;
                display: flex;
                flex-direction: row;
            }

            section>.image-handler{
                display: flex;
                justify-content: center;
                align-items: center;
                width: 35vw;
            }

            section>.text-handler{
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-content: center;
                width: 65vw;
            }

            @media (max-width: 576px){
                section>.image-handler{
                    display: none;
                }
                section>.text-handler{
                    width: 100%;
                }
            }

            section>.text-handler>h2{
                margin-bottom: 1rem;
            }

            /* Navbar */
            .register-link{
                margin-right: 1rem;
            }
            
            /* Footer style */
            footer{
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
            }
        </style>

        <link href="{{ asset('css/style.css') }}" rel="stylesheet"/>
    </head>
    <body>
        <div class="flex-center position-ref full-height blue-background">
            <div class="context">

                <!-- Navbar -->
                @if (Route::has('login'))
                    <nav>
                        @auth
                            <a href="{{ url('/home') }}">Home</a>
                        @else
                            <a href="{{ route('login') }}">Login</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="register-link">Register</a>
                            @endif
                        @endauth
                    </nav>
                @endif
                
                <!-- Title -->
                <div class="white-background">
                    <img src="{{ asset('images/logo.png') }}" class="welcome-logo">
                </div>

                <!-- 1st section -->
                <section>
                    <div class='image-handler'>
                        <script src="https://cdn.lordicon.com/qjzruarw.js"></script>
                        <lord-icon
                            src="https://cdn.lordicon.com/imamsnbq.json"
                            trigger="loop"
                            colors="primary:#121331,secondary:#ffffff"
                            style="width:250px;height:250px">
                        </lord-icon>
                    </div>
                    <div class='text-handler'>
                        <h2>
                            O que é a Helpaê?
                        </h2>
                        <div>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum urna quis metus ultricies, sed fermentum erat posuere. Etiam aliquet risus ac turpis tincidunt sollicitudin. Donec rutrum, risus at mollis elementum, massa augue tincidunt quam, non congue lacus turpis eu nibh. Nullam sodales eleifend quam non lobortis. Cras porttitor, dolor vitae fringilla molestie, augue odio aliquam lacus, quis placerat neque ipsum id purus. Nulla ligula nisi, mollis at diam a, rutrum condimentum quam. Fusce sodales, ante sit amet consequat semper, nisi nulla vehicula ligula, a tempor arcu tortor vitae magna. Sed et ultricies odio. Nunc mattis ex est, nec ullamcorper lacus ultrices vehicula. Nunc gravida, augue in semper ultricies, tellus sapien fringilla urna, ut cursus purus nisi a nisi. Aenean tempus mi nec elit malesuada eleifend. Vivamus suscipit elit ante, eget laoreet ante maximus et. Ut ornare nibh sed ipsum sollicitudin interdum. Fusce dictum nunc dolor, nec consequat nulla mattis quis. Aenean laoreet erat velit, ut aliquam tellus cursus blandit.
                        </div>
                    </div>
                </section>

                <!-- 2nd section -->
                <section>
                    <div class='text-handler'>
                        <h2>
                            Como funciona?
                        </h2>
                        <div>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum urna quis metus ultricies, sed fermentum erat posuere. Etiam aliquet risus ac turpis tincidunt sollicitudin. Donec rutrum, risus at mollis elementum, massa augue tincidunt quam, non congue lacus turpis eu nibh. Nullam sodales eleifend quam non lobortis. Cras porttitor, dolor vitae fringilla molestie, augue odio aliquam lacus, quis placerat neque ipsum id purus. Nulla ligula nisi, mollis at diam a, rutrum condimentum quam. Fusce sodales, ante sit amet consequat semper, nisi nulla vehicula ligula, a tempor arcu tortor vitae magna. Sed et ultricies odio. Nunc mattis ex est, nec ullamcorper lacus ultrices vehicula. Nunc gravida, augue in semper ultricies, tellus sapien fringilla urna, ut cursus purus nisi a nisi. Aenean tempus mi nec elit malesuada eleifend. Vivamus suscipit elit ante, eget laoreet ante maximus et. Ut ornare nibh sed ipsum sollicitudin interdum. Fusce dictum nunc dolor, nec consequat nulla mattis quis. Aenean laoreet erat velit, ut aliquam tellus cursus blandit.
                        </div>
                    </div>
                    <div class='image-handler'>
                        <script src="https://cdn.lordicon.com/qjzruarw.js"></script>
                        <lord-icon
                            src="https://cdn.lordicon.com/zpxybbhl.json"
                            trigger="loop"
                            colors="primary:#121331,secondary:#ffffff"
                            style="width:250px;height:250px">
                        </lord-icon>
                    </div>
                </section>

                <!-- Footer -->
                <footer>
                    <div>
                        All rights reserved @Helpaê
                    </div>
                    <div>
                        <small>
                            Icons by: https://lordicon.com/
                        </small>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
