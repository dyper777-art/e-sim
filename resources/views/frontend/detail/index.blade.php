@extends('frontend.layout.master')

@section('title', $esimPlan->plan_name)

@section('content')
    <!-- esim plan detail section -->

    <section class="about_section layout_padding">
        <div class="container">

            <div class="row">
                <!-- Left Image -->
                <div class="col-md-6">
                    <div class="img-box">
                        <img src="{{ asset( $esimPlan->image )  }}" alt="eSIM Plan">
                    </div>
                </div>

                <!-- Right Content -->
                <div class="col-md-6">
                    <div class="detail-box">

                        <div class="heading_container">
                            <h2>{{ $esimPlan->plan_name }}</h2>
                        </div>

                        <p><strong>Category:</strong> {{ $esimPlan->category->name ?? 'No Category' }}</p>
                        <p><strong>Data:</strong> {{ $esimPlan->data }}</p>
                        <p><strong>Validity:</strong> {{ $esimPlan->validity_days }} days</p>
                        <p><strong>Price:</strong> ${{ number_format($esimPlan->price, 2) }}</p>
                        <p><strong>Description:</strong></p>
                        <p>{{ $esimPlan->description }}</p>

                        <hr>

                        <h5>Available Quantity: {{ $esimPlan->quantity }}</h5>

                        <br>

                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            Back to Plans
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- end esim plan detail section -->
@endsection
