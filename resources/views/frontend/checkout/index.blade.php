@extends('frontend.layout.master')

@section('title', 'Checkout')

@section('content')
<section class="checkout_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>Checkout</h2>
        </div>

        @if($esimsGrouped->isEmpty())
            <div class="text-center my-5">
                <h3>Your cart is empty.</h3>
                <a href="{{ route('cart.index') }}" class="btn btn-secondary mt-3">Back to Cart</a>
            </div>
        @else
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Plan Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach($esimsGrouped as $item)
                            @php
                                $subtotal = $item->price * $item->quantity;
                                $grandTotal += $subtotal;
                            @endphp
                            <tr>
                                <td>{{ $item->plan_name }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="text-end"><strong>Grand Total</strong></td>
                            <td><strong>${{ number_format($grandTotal, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-center">
                <button id="payWithQrBtn" class="btn btn-success btn-lg">Pay with QR</button>
            </div>
        @endif
    </div>
</section>

<!-- QR Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <h4 class="mb-3">Scan QR to Pay</h4>
            <img id="qrImage" src="" alt="QR Code" class="img-fluid mb-2" />
            <p id="qrAmount" class="mb-3"></p>
            <p id="paymentStatus" class="text-success fw-bold"></p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payBtn = document.getElementById('payWithQrBtn');
    const qrImg = document.getElementById('qrImage');
    const qrAmount = document.getElementById('qrAmount');
    const paymentStatus = document.getElementById('paymentStatus');
    let pollingInterval;

    payBtn.addEventListener('click', async function() {
        paymentStatus.textContent = '';
        try {
            const response = await fetch("{{ route('checkout.generateQr') }}", {
                method: 'Get',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            console.log(data);
            if(!data.qrUrl) {
                alert(data.error || 'Failed to generate QR code.');
                return;
            }

            qrImg.src = data.qrUrl;
            qrAmount.textContent = "Amount: $" + parseFloat(data.amount).toFixed(2);
            paymentStatus.textContent = "Waiting for payment...";

            // Show modal
            const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
            qrModal.show();

            // Poll for payment confirmation every 5 seconds
            if(pollingInterval) clearInterval(pollingInterval);
            pollingInterval = setInterval(async () => {
                const checkResp = await fetch("{{ route('checkout.checkPayment') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ md5: data.md5 || null }) // make sure backend returns md5
                });

                const result = await checkResp.json();
                if(result.paid) {
                    paymentStatus.textContent = "Payment Successful!";
                    clearInterval(pollingInterval);

                    // Redirect to home page with a success message
                    setTimeout(() => {
                        window.location.href = "{{ route('home') }}?payment=success";
                    }, 1000);
                }
            }, 3000);

        } catch (err) {
            console.error(err);
            alert('Something went wrong. Try again.');
        }
    });
});
</script>
@endsection
