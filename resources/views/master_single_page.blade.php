<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('intecmex.app_name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="routeName" content="{{ Route::currentRouteName() }}">
    <meta name="SubRouteName" content="@yield('subRouter')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@200;300;400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ url('/static/css/core.css?v='.time()) }}">
    <link rel="stylesheet" href="{{ url('/static/css/single_page.css?v='.time()) }}">
    <link rel="stylesheet" href="{{ url('/static/css/mdalert.css?v='.time()) }}">
    
    <script src="{{ url('/static/js/app.js?v='.time()) }}"></script>
    <script src="{{ url('/static/js/lang.js?v='.time()) }}"></script>
    

    @section('custom_js')
    @show

</head>
<body>

    @include('components.loader_action')
    @include('components.mdalert')

    <div class="wrapper">
        @section('content')
        @show
    </div>
    
    {{-- ALERTS GLOBAL --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="{{ url('/static/js/mdalert.js?v='.time()) }}"></script>

    
</body>
</html>