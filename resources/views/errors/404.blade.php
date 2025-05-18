<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página no encontrada</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #FFEAC2;
            font-family: 'Segoe UI', sans-serif;
            text-align: center;
            color: #003D34;
            padding: 4rem;
        }
        img {
            max-width: 300px;
            margin-bottom: 2rem;
        }
        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
        }
        a {
            display: inline-block;
            background-color: #003D34;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #012622;
        }
    </style>
</head>
<body>
    <img src="/images/perrito2.png" alt="Perrito perdido">
    <h1>404 - Página no encontrada</h1>
    <p>Ups... parece que este perrito no encuentra el camino.</p>
    <a href="{{ route('home') }}">Volver al inicio</a>
</body>
</html>
