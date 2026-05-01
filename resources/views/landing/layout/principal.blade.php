<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('landing.include.head')
</head>
<body id="page-top" class="body-space">
    @include('landing.include.menu')

    <main>
        @yield('contenido')
    </main>

    <footer class="mt-auto">
        @include('landing.include.footer')
    </footer>

    @include('landing.include.script')
</body>
</html>
