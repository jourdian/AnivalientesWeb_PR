<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso denegado - AniValientes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #FFEAC2;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #003D34;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        img {
            width: 200px;
            margin-bottom: 1.5rem;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        a {
            padding: 0.75rem 1.5rem;
            background-color: #004C43;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            background-color: #006b61;
        }
    </style>
</head>
<body>
    <img src="{{ asset('images/perrito1.png') }}" alt="Perrito 403">
    <h1>403 - ¡Acceso denegado!</h1>
    <p>Este rincón está reservado para humanos autorizados.</p>
    @auth
    <a href="{{ route('dashboard') }}">Volver al panel</a>
    @else
    <a href="{{ url('/') }}">Volver al inicio</a>
    @endauth
    </body>
</html>
