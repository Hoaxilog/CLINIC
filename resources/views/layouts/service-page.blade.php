<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <title>{{ $service['title'] ?? 'Service' }} - Tejada Clinic</title>
    @vite('resources/css/app.css')
</head>

<body class="font-['Roboto']">
    @include('components.homepage.header-section')

    <main class="pt-20">
        @yield('content')
        @include('components.homepage.footer-section')
    </main>
</body>

</html>
