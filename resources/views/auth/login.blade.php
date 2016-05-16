@extends('app')

@section('title', 'Log in')

@section('content')
<div class="row">
    <div class="col s12 m6 l4 offset-l2">
        <form role="form" method="POST" action="{{ url('auth/login') }}">
            {!! csrf_field() !!}

            <div class="row">
                <div class="input-field col s12">
                    <input id="name_or_email" name="name_or_email" type="text" value="{{ old('name_or_email') }}">
                    <label for="name_or_email">Name or email address</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="password" name="password" type="password">
                    <label for="password">Password</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <a href="{{ url('auth/password/reset') }}" class="waves-effect waves-light btn-large blue-grey lighten-2">
                        Reset password
                    </a>
                    <button type="submit" class="waves-effect waves-light btn-large pull-right">
                        Log in
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="col s12 m6 l4">
        <p><strong>Alternatively, log in via...</strong></p>

        <hr>

        @foreach (config('auth.login_providers') as $key => $provider)
            <p><a href="{{ url("auth/{$key}") }}" class="waves-effect waves-light btn-large block brand-{{ $key }}">{{ $provider }} :D</a></p>
        @endforeach
    </div>
</div>
@endsection