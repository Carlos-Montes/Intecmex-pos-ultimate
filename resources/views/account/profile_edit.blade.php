@extends('master')

@section('custom_js')
    <script src="{{ url('/static/js/account.js?v='.time()) }}"></script>
@endsection

@section('content')
    <form action="/" method="post" autocomplete="off" files="true" class="form" id="form_profile_edit">
        <input type="hidden" name="autocomplete" class="autocomplete">
        <div class="row">
            <div class="col-md-3">
                <div class="panel mtop16 sh">
                    <div class="panel-header">
                        <div class="inside">
                            <h5><i class="bi bi-images"></i> Avatar</h5>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="inside">
                            <div class="align-center">
                                <input type="file" name="avatar" id="profile_avatar" class="hide image_prew", data-to-prew="image_avatar">
                                @if(is_null(Auth::user()->avatar))
                                    <img src="{{ url('/static/images/general/default-avatar.png') }}" width="120" height="120" class="rounded-circle" id="image_avatar">
                                @else
                                    <img src="{{ getFileUrl(Auth::user()->avatar, '256') }}" width="120" height="120" class="rounded-circle" id="image_avatar">
                                @endif
                            </div>
                            <div class="mtop16">
                                @include('components.file_input_mask', ['target' => 'profile_avatar', 'icon' => 'file-select-image.png'])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="panel mtop16 sh">
                    <div class="panel-header">
                        <div class="inside">
                            <h5><i class="bi bi-info-circle"></i> Mi información</h5>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="inside">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="name label-gris">Nombre: </label>
                                    <input type="text" name="name" class="disableac" value="{{ $user->name }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="phone label-gris">Teléfono: </label>
                                    <input type="text" name="phone" class="disableac" value="{{ $user->phone }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="gender label-gris">Genero: </label>
                                    <select name="gender" class="form-select">
                                        @foreach(gender() as $value => $label)
                                            <option value="{{ $value }}" {{ $user->gender == $value ? 'selected' : '' }}> {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-header">
                        <div class="inside">
                            <h5><i class="bi bi-database-lock"></i> Actulizar contraseña</h5>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="inside">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="password label-gris">Nueva contraseña: </label>
                                    <input type="password" name="password" class="disableac">
                                </div>
                                <div class="col-md-4">
                                    <label for="cpassword label-gris">Confirmar contraseña: </label>
                                    <input type="password" name="cpassword" class="disableac">   
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-12">
                                    <button type="submit" class="btn btn-success mtop16 w-30">Enviar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection