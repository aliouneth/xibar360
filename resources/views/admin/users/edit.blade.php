@extends('layouts.admin')

@section('title', __('Edit user'))

@section('content')
<h1 class="mb-4">{{ __('Edit user') }}</h1>

<form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf
    @method('PUT')
    @include('admin.users._form')
</form>
@endsection
