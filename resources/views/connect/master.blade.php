@extends('master_single_page')

@section('custom_js')
    <script src="{{ url('/static/js/connect.js?v='.time()) }}"></script>
@endsection

@section('content')
<div class="connect">
    <div class="col">
        <div class="cover">
            <img src="{{ url('/static/images/general/connect.jpeg') }}">
        </div>
    </div>
    <div class="col">
        @section('content_connect')
        @show
    </div>    
</div>
@endsection

