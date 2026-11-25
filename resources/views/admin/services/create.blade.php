@extends('admin.layouts.app')

@section('title', 'Add service')
@section('section', 'Services')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Legal Services</p><h1>Add a service</h1></div></div>
    @include('admin.services._form', ['action' => route('admin.services.store'), 'method' => 'POST', 'service' => null])
@endsection
