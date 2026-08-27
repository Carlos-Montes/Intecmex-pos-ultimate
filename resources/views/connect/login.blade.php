@extends('connect.master')

@section('content_connect')
<div class="box">
    <div class="in">
        <div class="logo">
            <img src="{{ url('/static/images/general/Logo.png') }}" alt="{{ config('intecmex.app_name') }}">
        </div>
        <h3>¡Bienvenido a {{ config('intecmex.app_name') }}!</h3>
        <p>Ingresa a tu cuenta y explora las herramientas</p>
        <div class="form mtop16">
            <form action="/" method="post" id="form_connect_login">
                <input type="hidden" name="autocomplete" class="autocomplete">
                <label for="email">Correo Electrónico:</label>
                <input type="email" name="email" class="disableac">
                <label for="password" class="mtop8">Contraseña:</label>
                <input type="password" name="password" class="disableac">
                <input type="submit" value="Ingresar" class="mtop16">
            </form>
        </div>
    </div>
</div>
@endsection