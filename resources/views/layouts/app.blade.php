<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tejadent Clinic</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @livewireStyles
</head>

<body class="bg-gray-50 min-h-screen">
    @include('components.homepage.header-section')

    <main>
        {{ $slot }}
    </main>

    @include('components.homepage.footer-section')
    @livewireScripts
</body>

</html>
