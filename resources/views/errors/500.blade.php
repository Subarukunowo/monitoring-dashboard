@extends('errors.layout')
@section('code', '500')
@section('title', 'Kesalahan Server')
@section('heading', 'Terjadi Kesalahan pada Server')
@section('message', 'Sistem mengalami kendala internal saat memproses permintaan Anda.')
@section('extra')
    @if(config('app.debug') && isset($exception))
        <div class="detail">{{ get_class($exception) }}: {{ $exception->getMessage() }}</div>
    @endif
@endsection
