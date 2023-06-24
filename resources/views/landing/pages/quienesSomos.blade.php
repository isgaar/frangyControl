@extends('landing.layout.principal')
@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('landing/css/quienesSomos.css') }}">
@endsection()
@section('contenido')
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
                                src="..\imagenes\quienesSomos\garage.png"
                                alt="team-work-img" width="350rem" height="300rem">
                        </div>
                        <div style="left;" class="tText">
                            <h3>Todo comenzó en una cochera</h3>
                            <p>En el 2015, mi amigo Carlos y yo decidimos emprender un negocio de zapatería en México. Con solo dos empleados 
                                y una cochera como espacio de trabajo, comenzamos a diseñar y fabricar zapatos de alta calidad.


                                Al principio, fue difícil competir con las grandes marcas de zapatos ya establecidas en el mercado. 
                                Pero nos dimos cuenta de que nuestro compromiso con la calidad y la atención al cliente era lo que nos 
                                diferenciaba. La pasión por los zapatos también nos impulsó a seguir adelante.
                                
                                </p>
                            
                            <p class="tText">Hoy en día, nuestra empresa de zapatería mexicana cuenta con una amplia gama de productos, 
                                desde zapatos casuales hasta zapatos formales de alta calidad, y ha ganado reconocimiento internacional 
                                por su excelencia en diseño y fabricación de zapatos.</p>

                        </div>
                        <div style="text-align: left;"></div>
                        <div style="text-align: left;"></div>

                        <div style="text-align: left;" class="tText"></div>
                        <div style="text-align: left;"></div>
                        <div style="text-align: left;"></div>
                        <div style="text-align: left;"></div>
                        <div style="text-align: left;"></div>
                        <div style="text-align: left;"></div>


                        <div class="container tText">
                            <div class="left "> <p>
                                Hoy en día, nuestra empresa de zapatería mexicana cuenta con una amplia gama de productos, desde zapatos
                                 casuales hasta zapatos formales de alta calidad, y ha ganado reconocimiento internacional por su excelencia en diseño y
                                  fabricación de zapatos.
                            </p> 
                        
                        </div>
                            <div class="right tText">Aunque ahora somos una empresa más grande, nuestro compromiso con la calidad, la atención al cliente y la 
                                innovación en el diseño de zapatos sigue siendo el mismo. Carlos y yo seguimos liderando la empresa y hemos creado una cultura
                                 centrada en la pasión por los zapatos y en el compromiso con la excelencia. Nos sentimos orgullosos de lo que hemos logrado y
                                  estamos emocionados por el futuro de nuestra empresa de zapatería mexicana.</div>
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
