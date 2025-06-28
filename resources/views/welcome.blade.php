<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel + Vue + Tailwind</title>

        @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- 👈 Include Tailwind and Vue --}}
    </head>

    <body class="bg-gray-100 flex items-center justify-center min-h-screen">

        <div class="text-3xl font-bold text-red-500">
            Tailwind is working in Blade!
        </div>

        <div id="app" class="mt-6">
            <example-component></example-component> {{-- 👈 Vue component --}}
        </div>

    </body>

</html>