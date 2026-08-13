<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>{{ config('intecmex.app_name') }}</title>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="routeName" content="{{ Route::currentRouteName() }}">
    <meta name="SubRouteName" content="@yield('subRouter')">

    @section('custom_meta')
    @show

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    {{-- GOOGLE CSS FAMILY --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@200;300;400&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    
    {{-- CSS GLOBAL --}}
    <link rel="stylesheet" href="{{ url('/static/css/core.css?v='.time()) }}">
    <link rel="stylesheet" href="{{ url('/static/css/style.css?v='.time()) }}">
    <link rel="stylesheet" href="{{ url('/static/css/mdalert.css?v='.time()) }}">
    
    {{-- SCRIPTS GLOBAL --}}
    <script src="{{ url('/static/js/app.js?v='.time()) }}"></script>
    <script src="{{ url('/static/js/lang.js?v='.time()) }}"></script>
    <script src="{{ url('/static/libs/ckeditor/ckeditor.js') }}"></script>

    {{-- ICONS BOOSTRAP --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/evo-calendar@1.1.2/evo-calendar/css/evo-calendar.min.css"/>
</head>
<body>
    @include('components.loader_action')
    @include('components.mdalert')

    <div class="wrapper">
        @include('components.sidebar')
        <div class="content">
            @include('components.content_topbar')
            
            @if(Session::has('message'))
                <div class="alert alert-{{ Session::get('typealert') }} mtop32 calert" style="display: block; margin-top:16px;">
                    {{ Session::get('message') }}
                    <script>
                        document.getElementsByClassName('calert')[0].style.display = 'block';
                        setTimeout(function(){
                            document.getElementsByClassName('calert')[0].style.display = 'none';
                        }, 10000);
                    </script>

                    @if ($errors->any())
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif


            @section('content')
            @show
        </div>
    </div>
    
    {{-- ALERTS GLOBAL --}}
    <script src="{{ url('/static/js/system.js?v='.time()) }}"></script>
    <script src="{{ url('/static/js/mdalert.js?v='.time()) }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    @section('custom_js')
    @show
    
</body>
</html>