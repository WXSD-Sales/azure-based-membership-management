@extends('layouts.app')

@section('content')
    <setup :csrf='"{!! csrf_token() !!}"'
           :email='"{!! session('email') ?? old('email') !!}"'
           :azure='"{!! session('azure.id') ?? "" !!}"'
           :webex='"{!! session('webex.id') ?? "" !!}"'
           :error='{!! $errors->any() ? $errors : "{}" !!}'
           :url='{!! json_encode($url) !!}'>
    </setup>
@endsection
