@extends('admin.layouts.app')
@section('title', 'Add Law')
@section('section', 'Philippine Laws')
@section('content')
    <div class="page-heading"><div><p class="eyebrow">Legal library</p><h1>Add a law reference</h1></div></div>
    @include('admin.laws._form', ['action' => route('admin.laws.store'), 'method' => 'POST'])
@endsection
