@extends('admin.layouts.app')
@section('title', 'Edit Law')
@section('section', 'Philippine Laws')
@section('content')
    <div class="page-heading"><div><p class="eyebrow">Legal library</p><h1>Edit law reference</h1></div></div>
    @include('admin.laws._form', ['action' => route('admin.laws.update', $law), 'method' => 'PUT'])
@endsection
