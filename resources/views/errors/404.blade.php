@extends('errors.layout')
@section('code', '404')
@section('title', 'Halaman Tidak Ditemukan')
@section('heading', 'Halaman Tidak Ditemukan')
@section('message', 'URL yang diminta tidak tersedia atau telah dipindahkan.')
@section('extra')
    <div class="detail">{{ request()->url() }}</div>
@endsection
