@extends('layouts.app', ['title' => 'Page Not Found | Hyper Adz', 'description' => 'The Hyper Adz page you requested could not be found.'])

@section('content')
<section class="error-page">
    <div class="container text-center">
        <span class="eyebrow">404</span>
        <h1>This screen is offline.</h1>
        <p>The page you were looking for is not available, but the Hyper Adz network is ready to explore.</p>
        <a href="{{ route('home') }}" class="btn btn-primary btn-lg"><i class="bi bi-house"></i> Back Home</a>
    </div>
</section>
@endsection
