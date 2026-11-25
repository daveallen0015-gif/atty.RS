@extends('admin.layouts.app')

@section('title', 'Edit service')
@section('section', 'Services')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Legal Services</p><h1>Edit a service</h1></div></div>
    @include('admin.services._form', ['action' => route('admin.services.update', $service), 'method' => 'PUT', 'service' => $service])
@endsection
