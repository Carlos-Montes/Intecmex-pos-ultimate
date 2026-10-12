@extends('master')

@section('subRouter')inventory_{{'live_weight'}}@endsection

@section('custom_js')
    <script src="{{ url('/static/js/live_weight.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 mtop16">
                <div class="panel sh">
                    <div class="panel-header">
                        <div class="inside">
                            <h5><i class="bi bi-check-circle-fill"></i> Ingresar Peso Vivo</h5>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="inside">
                            <form action="/" class="form" autocomplete="off" id="form_live_weight">
                                <input type="hidden" name="autocomplete">
                                <label for="category">Selecciona una categoría</label>
                                <select name="category" id="category" class="form-select">
                                    @foreach (getCategorieLiveWeight() as $item => $label)
                                        <option value="{{ $item }}"> {{ $label }}</option>
                                    @endforeach
                                </select>
                                <label for="weight" class="mtop16">Ingresa el peso</label>
                                <input type="number" name="weight" class="disableac" placeholder="Ingresa el peso en kilos...">
                                <button type="submit" class="btn btn-success mtop16">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 mtop16">
                <div class="panel sh">
                    <div class="panel-header">
                        <div class="inside">
                            <h5><i class="bi bi-clipboard2-check"></i> Peso registrado el dia de hoy: {{ date('d-m-Y') }}</h5>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="inside d-flex">
                            <div class="col-md-6">
                                <h2> <i class="bi bi-piggy-bank-fill"></i> Cerdos contados el dia de hoy {{ $count_pig }}</h2>
                            </div>
                            <div class="col-md-6">
                                <h2> <i class="bi bi-gitlab"></i> Reses contados el dia de hoy {{ $count_cattle}}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection