<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tejadent Clinic</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">


    @include('components.homepage.header-section')

    <!-- MAIN CONTENT -->
    <main class="flex-grow py-8">
        @livewire('appointment.book-appointment')
    </main>

    @include('components.homepage.footer-section')
    @livewireScripts
</body>

</html>
