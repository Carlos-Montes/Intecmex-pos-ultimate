@extends('master')

@section('subRouter')users_{{ $type }}@endsection

@section('content')
    @if (getPermissions(Auth::user()->permissions, 'new_user'))
        <div class="row mtop16">
            <div class="col-md-12">
                <div class="new-user">
                    <a href="{{ url('/users/new_user') }}" class="a-link">Agregar nuevo usuario</a>
                </div>
            </div>
        </div>
    @endif
    <div class="row mtop16">
        <div class="col-md-12">
            <div class="list-card-profile-avatar">
                @foreach ($users as $item)
                    <div class="card sh">
                        <div class="body">
                            <div class="avatar">
                                @if ($item->avatar)
                                    <div class="photo">
                                        <img src="{{ getFileUrl($item->avatar, '64') }}">
                                    </div>
                                @else
                                    <div class="photo {{ 'avatar_' . rand(1, 5) }}">
                                        {{ generate_avatar($item->name) }}
                                    </div>
                                @endif
                            </div>
                            <div class="info">
                                <h5>{{ $item->name }}</h5>
                                <p>{{ $item->email }} @if ($item->email_verified_at)
                                        <span class="verified"><i class="bi bi-check2-all"></i></span>
                                    @endif
                                </p>
                                <p>{{ getUserRole($item->role) }} | {{ getUsersStatus($item->status) }}</p>
                            </div>
                        </div>
                        <div class="actions">
                            <ul>
                                @if (getPermissions(Auth::user()->permissions, 'users_views'))
                                    <li>
                                        <a href="{{ url('/users/' . $item->id . '/view') }}">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                    </li>
                                @endif
                                @if (getPermissions(Auth::user()->permissions, 'users_permissions'))
                                    <li>
                                        <a href="{{ url('/users/' . $item->id . '/permissions') }}">
                                            <i class="bi bi-clipboard2-check"></i> Permisos
                                        </a>
                                    </li>
                                @endif
                                @if (getPermissions(Auth::user()->permissions, 'new_user'))
                                    <li>
                                        @if ($item->status == 0)
                                            <a href="#" class="btn-inactive" data-path="api-js" data-action="inactive"
                                                data-object="form_inactive_user_{{ $item->id }}" data-toggle="tooltip"
                                                data-placement="top" title="Desactivar">
                                                <i class="bi bi-trash"></i> Desactivar
                                            </a>
                                        @endif
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="mtop16">
        {{ $users->links() }}
    </div>
@endsection
