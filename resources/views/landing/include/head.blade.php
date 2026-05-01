<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta name="description" content="" />
<meta name="author" content="" />
<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Favicon-->
<link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
<!-- Font Awesome icons (free version)-->
<script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
<!-- Simple line icons-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.5.5/css/simple-line-icons.min.css" rel="stylesheet" />
<!-- Google fonts-->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css" />
<!-- Core theme CSS (includes Bootstrap)-->
@if (file_exists(public_path('landing/css/styles.css')))
    <link href="{{ asset('landing/css/styles.css') }}" rel="stylesheet" />
@endif


<!-- CSS files for Frangy Control -->
@if (file_exists(public_path('landing/css/style.css')))
    <link rel="stylesheet" href="{{ asset('landing/css/style.css') }}">
@endif

<!--CSS for forms in Admin-->
@if (file_exists(public_path('landing/css/inputForm.css')))
    <link rel="stylesheet" href="{{ asset('landing/css/inputForm.css') }}">
@endif
@yield('stylesheet')

