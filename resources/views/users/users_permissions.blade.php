@extends('master')

@section('subRouter')users_{{ $type }}@endsection

@section('content')
    <div class="row mtop16">
        <div class="col-md-3">
            <div class="panel mtop16 sh">
                <div class="inside">
                    <div class="d-flex align-items-start">
                        <div class="nav flex-column nav-pills w-100 nav-vertical" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            @foreach (user_permissions() as $key => $value)
                                <button class="nav-link @if ($loop->first) active @endif" id="v-pills-{{ $key }}-tab" data-bs-toggle="pill" data-bs-target="#v-pills-{{ $key }}" type="button" role="tab" aria-controls="v-pills-{{ $key }}" aria-selected="true">
                                    {!! $value['icon'] !!} {{ $value['title'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9 mtop16">
            <form action="{{ url('api-js/form_permissions_users_'.$user->id) }}" method="post" class="form" autocomplete="off" files="false">
                @csrf
                <div class="panel sh">
                    <div class="panel-header">
                        <div class="inside">
                            <h5><i class="bi bi-pencil-square"></i> Permisos para el usuario: {{ $user->name }}</h5>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="inside">
                            <div class="tab-content" id="v-pills-tabContent">
                                @foreach (user_permissions() as $item => $value)
                                    <div class="tab-pane fade @if($loop->first) active @endif show" id="v-pills-{{ $item }}" role="tabpanel" aria-labelledby="v-pills-{{ $item }}-tab" tabindex="0">
                                        <div class="panel">
                                            <div class="inside">
                                                @foreach ($value['keys'] as $k => $v)
                                                    <div class="form-check form-switch mb8">
                                                        <input class="form-check-input" name="{{ $k }}" type="checkbox" value="true" role="switch" id="fiel_{{ $k }}" @if(kvfj($user->permissions, $k)) checked @endif>
                                                        <label class="form-check-label" for="field_{{ $k }}">{{ $v }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel mtop16 sh">
                    <div class="inside">
                        <div class="row">
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success">Actualizar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
