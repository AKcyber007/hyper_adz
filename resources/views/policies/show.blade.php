@extends('layouts.app')

@php
    $title = $policy->title . ' | Hyper Adz';
    $description = 'Read our ' . strtolower($policy->title) . '.';
@endphp

@section('content')
<section class="policy-section py-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <h1 class="mb-4 fw-bold text-center">{{ $policy->title }}</h1>
                    
                    <div class="policy-content">
                        {!! $policy->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
