@extends('errors.layout')

@section('code', '403')
@section('title', 'Akses Ditolak')
@section('badge_bg', '#DC2626')
@section('badge_fg', '#FFF')
@section('message', !empty($exception?->getMessage()) ? $exception->getMessage() : 'Maaf, akun Anda tidak memiliki izin untuk mengakses halaman atau berkas ini.')
