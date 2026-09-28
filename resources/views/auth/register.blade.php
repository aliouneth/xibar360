@extends('layouts.guest')

@section('title', 'Inscription - SunuNews')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-900 via-red-800 to-green-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white rounded-2xl shadow-2xl p-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">Créer un compte</h2>
            <p class="mt-2 text-center text-sm text-gray-600">Rejoignez SunuNews</p>
        </div>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-6">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="name" class="sr-only">Nom</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}" class="appearance-none rounded-t-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-senegal-red focus:border-senegal-red focus:z-10 sm:text-sm" placeholder="Votre nom">
                </div>
                <div>
                    <label for="email" class="sr-only">Email</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-senegal-red focus:border-senegal-red focus:z-10 sm:text-sm" placeholder="Email">
                </div>
                <div>
                    <label for="password" class="sr-only">Mot de passe</label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-b-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-senegal-red focus:border-senegal-red focus:z-10 sm:text-sm" placeholder="Mot de passe">
                </div>
                <div>
                    <label for="password_confirmation" class="sr-only">Confirmer le mot de passe</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-senegal-red focus:border-senegal-red focus:z-10 sm:text-sm" placeholder="Confirmer le mot de passe">
                </div>
            </div>
            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-bold rounded-md text-white bg-senegal-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-senegal-red">
                    Créer le compte
                </button>
            </div>
        </form>
        <p class="mt-6 text-center text-sm text-gray-600">
            Déjà inscrit ? <a href="{{ route('login') }}" class="font-medium text-senegal-red hover:text-red-700">Se connecter</a>
        </p>
    </div>
</div>
@endsection
