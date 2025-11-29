@extends('frontend.layout.master')

@section('title', 'home')

@section('content')

    <!-- food section -->

    <section class="food_section layout_padding-bottom">
        <div class="container">
            <div class="heading_container heading_center">
                <h2>
                    Our Recommend Plan
                </h2>
            </div>

            {{-- @if(request('payment') === 'success')
                <div class="alert alert-success text-center mt-3">
                    Payment completed successfully!
                </div>
            @endif --}}

            <div class="filters-content">
                <div class="row grid">

                    @foreach ($plans as $plan)
                        <x-plan-card :plan="$plan" />
                    @endforeach

                </div>
            </div>

            <div class="btn-box">
                <a href="{{ route('pricing') }}">
                    View More
                </a>
            </div>
        </div>
    </section>

    <!-- end food section -->

    <!-- about section -->

    <section class="about_section layout_padding">
        <div class="container  ">

            <div class="row">
                <div class="col-md-6 ">
                    <div class="img-box">
                        <img src="{{ asset('frontend/assets/images/esim-card-line-icon.png') }}" alt="">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-box">
                        <div class="heading_container">
                            <h2>
                                We Are Feane
                            </h2>
                        </div>
                        <p>
                            There are many eSIM solutions designed for government and enterprise use, but most require careful integration and security compliance. Our eSIMs are built to meet strict B2G standards, ensuring seamless connectivity and reliable management. When deploying eSIMs at scale, it’s essential to choose a solution that supports both flexibility and government-grade security.
                        </p>
                        <a href="{{ route('about') }}">
                            Read More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- end about section -->

    <div class="modal fade" id="paymentSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <h4 class="mb-3 text-success">Payment Completed!</h4>
            <p class="mb-0">Your payment was successful. Thank you!</p>
            <button type="button" class="btn btn-success mt-3" data-bs-dismiss="modal">OK</button>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.get('payment') === 'success') {
        // Show modal
        var successModal = new bootstrap.Modal(document.getElementById('paymentSuccessModal'));
        successModal.show();

        // Remove query param without reloading
        const url = window.location.origin + window.location.pathname;
        window.history.replaceState({}, document.title, url);
    }
});
</script>
@endsection
