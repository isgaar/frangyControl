@extends('landing.layout.principal')
@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('landing/css/misionVision.css') }}">
@endsection()
@section('contenido')
    <div class="">
        <section class="divContainer divSpace" id="about">
            <div class="container px-4 px-lg-5 text-center ">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="page-width page-width--narrow section-template--15320092508296__main-padding">

                        <div class="rte ">
                            {{-- <h1 style="text-align: center;"><img
                                src="https://cdn.shopify.com/s/files/1/0329/7663/3992/files/OPS_IN_A_NUTSHELL_480x480.png?v=1669230541"
                                alt="" width="30%" height="30%"
                                style="display: block; margin-left: auto; margin-right: auto;"></h1> --}}
                            <div class="imgQuienes">
                                <img class="u-image u-image-default u-image-1"
                                    src="//images01.nicepage.com/a1389d7bc73adea1e1c1fb7e/e58d3459fbb55b74a8fa5357/Untitled-2.png"
                                    alt="team-work-img" width="350rem" height="300rem">
                            </div>
                            <div style="left;" class="tText">
                                <h3>¿Nuestra misión?</h3>
                                <p>En Zapatería los dos zapatos, nuestra misión es proporcionar a nuestros
                                    clientes una experiencia de compra en línea excepcional al ofrecer una amplia
                                    selección de zapatos de alta calidad y estilo. Nos esforzamos por brindar un
                                    servicio al cliente excepcional y garantizar que nuestros clientes estén
                                    satisfechos con sus compras. Nos enfocamos en ofrecer productos de alta calidad
                                    a precios competitivos y en brindar una experiencia de compra fácil y conveniente.</p>
                                <h3>¿Qué hay de nuestra visión?</h3>
                                <p class="tText">En Zapatería los dos zapatos, nuestra visión es convertirnos en la
                                    principal
                                    tienda de calzado en línea, reconocida por nuestra amplia selección de zapatos de alta
                                    calidad y nuestro servicio al cliente excepcional. Nos esforzamos por mantenernos a la
                                    vanguardia de las últimas tendencias en calzado y en ofrecer una experiencia de compra
                                    en línea fácil y conveniente. Queremos ser la opción preferida de nuestros clientes en
                                    línea al momento de buscar zapatos de alta calidad y estilo.</p>

                            </div>
                            <div style="text-align: left;"></div>
                            <div style="text-align: left;"></div>


                            <div class="carrusel-container">
                                <div class="carrusel">
                                    <img src="..\imagenes\carrusel\img5.png" alt="Imagen 5" class="imgCarrusel">
                                    <img src="..\imagenes\carrusel\img2.png" alt="Imagen 2" class="imgCarrusel">
                                    <img src="..\imagenes\carrusel\img3.png" alt="Imagen 3" class="imgCarrusel">
                                    <img src="..\imagenes\carrusel\img4.png" alt="Imagen 4" class="imgCarrusel">

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection()
@section('scripts')
    <script src="{{ asset('landing/js/carrusel.js') }}"></script>
@endsection()
