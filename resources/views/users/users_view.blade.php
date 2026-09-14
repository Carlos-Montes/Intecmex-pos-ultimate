@extends('master')

@section('subRouter')users_{{ $type }}@endsection

@section('content')
    <div class="row mtop16">
        <div class="col-md-3">
            <div class="panel sh">
                <div class="panel-body">
                    <div class="inside">
                        <div class="mini-profile">
                            <div class="avatar">
                                @if (is_null($user->avatar))
                                    <img src="{{ url('/static/images/general/default-avatar.png') }}">
                                @else
                                    <img src="{{ getFileUrl($user->avatar, '64') }}">
                                @endif
                            </div>
                            <div class="header-info">
                                <h5>{{ $user->name }}</h5>
                                <small>{{ $user->email }}</small>
                                <small>{{ getUserRole($user->role) }}</small>
                                <small>{{ getUsersStatus($user->status) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="panel sh">
                <div class="panel-header">
                    <div class="inside">
                        <h5><i class="bi bi-pencil-square"></i> Información de - {{ $user->name }}</h5>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="inside">
                        <div class="panel-nav">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="information-tab" data-bs-toggle="tab" data-bs-target="#information-tab-pane" type="button" role="tab" aria-controls="information-tab-pane" aria-selected="true"><i class="bi bi-info-circle"></i> Información</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-tab-pane" type="button" role="tab" aria-controls="password-tab-pane" aria-selected="false"><i class="bi bi-lock"></i> Constraseña</button>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content mtop16" id="myTabContent">
                            <div class="tab-pane fade show active" id="information-tab-pane" role="tabpanel" aria-labelledby="information-tab" tabindex="0">
                                <form action="{{ url('/api-js/form_user_edit_info_'.$user->id) }}" method="post" autocomplete="off" class="form">
                                    @csrf
                                    <input type="hidden" name="autocomplete">
                                    <div class="row d-flex">
                                        <div class="col-md-6">
                                            <label for="name">Nombre(s) </label>
                                            <input type="text" name="name" value="{{ $user->name }}" class="disableac">
                                            <label for="phone" class="mtop16">Telefono </label>
                                            <input type="number" name="phone" value="{{ $user->phone }}" class="disableac">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="gender">Genero</label>
                                            <select name="gender" class="form-select">
                                                @foreach(gender() as $value => $label)
                                                    <option value="{{ $value }}" {{ $user->gender == $value ? 'selected' : '' }}> {{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <label for="role" class="mtop16">Rol</label>
                                            <select name="role" class="form-select">
                                                @foreach (getUserRole() as $item => $key)
                                                    <option value="{{ $item }}" {{ $user->role == $item ? 'selected' : '' }}>{{ $key }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mtop16 d-flex justify-content-end">
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-submit">Actulizar Información</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="password-tab-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
                                <form action="{{ url('/api-js/form_user_edit_password_'.$user->id) }} " method="post" autocomplete="off" class="form">
                                    @csrf
                                    <input type="hidden" name="autocomplete" class="autocomplete">
                                    <div class="row d-flex">
                                        <div class="col-md-6">
                                            <label for="password">Nueva contraseña</label>
                                            <input type="password" name="password" class="disableac">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="cpassword">Confirmar contraseña</label>
                                            <input type="password" name="cpassword" class="disableac">
                                        </div>
                                    </div>
                                    <div class="row mtop16 -flex justify-content-end">
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-submit">Actulizar contraseña</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
