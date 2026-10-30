@extends('master')

@section('content')
<form action="{{ url('/settings') }}" method="post" autocomplete="off" class="form">
    @csrf
    <div class="row">
        <div class="col-md-3">
            <div class="panel mtop16 sh">
                <div class="inside">
                    <div class="d-flex align-items-start">
                        <div class="nav flex-column nav-pills w-100 nav-vertical" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active w-100" id="v-pills-company-tab" data-bs-toggle="pill" data-bs-target="#v-pills-company" type="button" role="tab" aria-controls="v-pills-company" aria-selected="true">
                                <i class="bi bi-building"></i> Empresa
                            </button>
                            <button class="nav-link w-100" id="v-pills-settings-tab" data-bs-toggle="pill" data-bs-target="#v-pills-settings" type="button" role="tab" aria-controls="v-pills-settings" aria-selected="true">
                                <i class="bi bi-life-preserver"></i> Configuración
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="panel mtop16 sh">
                <div class="panel-header">
                    <div class="inside">
                        <h5><i class="bi bi-life-preserver"></i> Configuración</h5>
                    </div>
                </div>
            </div>
            <div class="tab-content" id="#v-pills-tabContent">
                <div class="tab-pane fade active show" id="v-pills-company" role="tabpanel" aria-labelledby="v-pills-company-tab" tabindex="0">
                    <div class="panel mtop16 sh">
                        <div class="inside">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="app_name">Nombre de la empresa:</label>
                                    <input type="text" name="app_name" value="{{ config('intecmex.app_name') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="business_address">Dirección de la empresa:</label>
                                    <input type="text" name="business_address" value="{{ config('intecmex.business_address') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="business_phone">Teléfono de la empresa:</label>
                                    <input type="number" name="business_phone"  value="{{ config('intecmex.business_phone') }}" required>
                                </div>
                            </div>
                            <div class="row mtop16">
                                <div class="col-md-4">
                                    <label for="email_from">Correo electrónico:</label>
                                    <input type="email" name="email_from" value="{{ config('intecmex.email_from') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab" tabindex="0">
                    <div class="panel mtop16 sh">
                        <div class="inside">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="cdn_path">CDN Path:</label>
                                    <input type="text" name="cdn_path" value="{{ config('intecmex.cdn_path') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="cdn">CDN:</label>
                                    <input type="text" name="cdn" value="{{ config('intecmex.cdn') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel mtop16 sh">
                    <div class="inside">
                        <div class="row">
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success">Guardar</button>        
                            </div>
                        </div>    
                    </div>        
                </div>
            </div>
        </div>
    </div>
</form>
@endsection