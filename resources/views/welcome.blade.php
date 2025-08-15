<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>Laravel</title>
    </head>
        <div id="registration">
            <registration-component />
        </div>
    </body>
    @vite(['resources/js/app.js'])
</html>
