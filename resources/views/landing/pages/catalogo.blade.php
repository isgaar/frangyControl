@extends('landing.layout.principal')
@section('contenido')
<!-- Services-->
<section class="content-section bg-primary text-white text-center color7" id="services">
    <div class="container px-4 px-lg-5" style="color:black">
        <div class="content-section-heading">
            <h1 class="text-secondary mb-0">¿Zapatos?</h1>
            <h2 class="mb-5">Los mejores del mercado</h2>
        </div>
        <div class="row gx-4 gx-lg-5">
            <div class="col-lg-3 col-md-6 mb-5 mb-lg-0 ">
                <img src="../../assets/img/damaCuero.jpg" alt="" class="imgCircle">
                <h4><strong>¿Para dama?</strong></h4>
                <p class="text-faded mb-0">Los más coquetos</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-5 mb-lg-0">
                <img src="../../assets/img/caballeroCuero.jpg" alt="" class="imgCircle">
                <h4><strong>¿Para caballero?</strong></h4>
                <p class="text-faded mb-0">Los más valientes</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-5 mb-md-0">
                <img src="../../assets/img/cinturones.jpg" alt="" class="imgCircle">
                <h4><strong>¿Cinturones?</strong></h4>
                <p class="text-faded mb-0">En cuero somos los reyes</p>
            </div>
            <div class="col-lg-3 col-md-6">
                <img src="../../assets/img/vaca.jpg" alt="" class="imgCircle">
                <h4><strong>¿Por qué nuestra piel?</strong></h4>
                <p class="text-faded mb-0"></p>
            </div>
        </div>
    </div>
</section>
@endsection()