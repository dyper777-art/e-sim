@php
    $detail = $detail ?? false;
@endphp

<div class="col-sm-6 col-lg-4 all {{ $plan['category_name'] }}">
    <div class="box">
        <div>
            <div class="img-box">
                <h1>{{ $plan['name'] }}</h1>
                <img src="{{ asset('frontend/assets/images/esim-basic-standard.png') }}" alt="{{ $plan['name'] }}">
            </div>
            <div class="detail-box {{ $detail == false ? 'not-detail-box' : '' }}">
                <h5>{{ $plan['plan_name'] }}</h5>
                <p>{{ $plan['description'] }}</p>
                <div class="options">
                    <h6>${{ $plan['price'] }}</h6>

                    @if (Auth::check())
                        <button onclick="addToCart({{ $plan['id'] }})">
                        @else
                            <button onclick="window.location='{{ route('login') }}'">
                    @endif

                    <svg width="20" height="20" viewBox="0 0 456.029 456.029" fill="white"
                        xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <g>
                                <path
                                    d="M345.6,338.862c-29.184,0-53.248,23.552-53.248,53.248c0,29.184,23.552,53.248,53.248,53.248
                                        c29.184,0,53.248-23.552,53.248-53.248C398.336,362.926,374.784,338.862,345.6,338.862z" />
                            </g>
                        </g>
                        <g>
                            <g>
                                <path d="M439.296,84.91c-1.024,0-2.56-0.512-4.096-0.512H112.64l-5.12-34.304C104.448,27.566,84.992,10.67,61.952,10.67H20.48
                                        C9.216,10.67,0,19.886,0,31.15c0,11.264,9.216,20.48,20.48,20.48h41.472c2.56,0,4.608,2.048,5.12,4.608l31.744,216.064
                                        c4.096,27.136,27.648,47.616,55.296,47.616h212.992c26.624,0,49.664-18.944,55.296-45.056l33.28-166.4
                                        C457.728,97.71,450.56,86.958,439.296,84.91z" />
                            </g>
                        </g>
                        <g>
                            <g>
                                <path
                                    d="M215.04,389.55c-1.024-28.16-24.576-50.688-52.736-50.688c-29.696,1.536-52.224,26.112-51.2,55.296
                                        c1.024,28.16,24.064,50.688,52.224,50.688h1.024C193.536,443.31,216.576,418.734,215.04,389.55z" />
                            </g>
                        </g>
                    </svg>
                    </button>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function addToCart(planId) {
        fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    plan_id: planId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || 'Success!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                } else {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: data.message || 'Something went wrong!',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error adding to cart');
            });
    }
</script>
