@extends('layouts.admin')

@section('title', __('Add user'))

@section('content')
<h1 class="mb-4">{{ __('Add user') }}</h1>

<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf
    @include('admin.users._form')
</form>
@endsection
