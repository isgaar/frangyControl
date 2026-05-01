        <!-- Bootstrap core JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        @if (file_exists(public_path('landing/js/scripts.js')))
            <script src="{{ asset('landing/js/scripts.js') }}"></script>
        @endif

        @yield('scripts')
