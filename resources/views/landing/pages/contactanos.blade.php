@extends('landing.layout.principal')
@section('contenido')
    <section class="intro margin-tp-elements backgroundimgCueroNegro contenedor">

        <div class="mask d-flex align-items-center h-100 gradient-custom color6">
            <div class="container ">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-9 col-xl-7">
                        <div class="cardTranslucid ">
                            <div class="card-body p-4 p-md-5">
                                <h3 class="mb-4 pb-2 ">Contáctate con nosotros</h3>

                                <form action="" class="">

                                    <div class="row">
                                        <div class="col-md-6 mb-4">

                                            <div class="form-outline">
                                                <input type="text" id="firstName" class="form-control" />
                                                <label class="form-label" for="firstName">Nombre(s)</label>
                                            </div>

                                        </div>
                                        <div class="col-md-6 mb-4">

                                            <div class="form-outline">
                                                <input type="text" id="lastName" class="form-control" />
                                                <label class="form-label" for="lastName">Apellidos</label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row">

                                    <div class="row">
                                        <div class="col-md-6 mb-4">

                                            <div class="form-outline">
                                                <input type="email" id="emailAddress" class="form-control" />
                                                <label class="form-label" for="emailAddress">Correo</label>
                                            </div>

                                        </div>
                                        <div class="col-md-6 mb-4">

                                            <div class="form-outline">
                                                <input type="tel" id="phoneNumber" class="form-control" />
                                                <label class="form-label" for="phoneNumber">Número telefónico</label>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mt-4">
                                            <input class="btn btn-warning btn-lg btnSubmit" type="submit" value="Enviar"/>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection()
@section('js')
        <script src="{{asset('js/validatorFields.js')}}"></script>
<@endsection()