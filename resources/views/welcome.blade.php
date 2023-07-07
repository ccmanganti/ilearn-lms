<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>iLearn | Home</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="./css/home.css">
        <link rel="icon" href="img/favicon.png" type="image/x-icon">
        <!-- Styles -->
    </head>
    <body>
        <nav class="nav-bar">
            <a href="/"><img src="./img/templogo.png" alt="iLEARN Home" class="nav-item"></a>
            <div class="nav-right">
                @guest
                    <a href="/lms/login" class="nav-item nav-btn btn-log">Log In</a>
                    <a href="/lms/register" class="nav-item nav-btn btn-sign">Sign Up</a>                                
                @endguest
                @auth
                    <a href="/lms" class="nav-item nav-btn btn-log">Go to iLEARN</a>
                @endauth

            </div>
        </nav>
        <div class="content">
            <h2 class="liner">Elevate learning, simplify management</h2>
            <p class="trailer">Embark on a transformative learning journey with iLEARN, where education meets efficiency.</p>
            <img src="./img/homebg.png" alt="iLEARN LMS" class="showcase-img">
        </div>
        












    </body>
</html>
