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
<!-- QR Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4 text-center border-0 shadow-sm rounded-4">

            <h4 class="mb-4 fw-bold text-success">💳 Scan QR to Pay</h4>

            <div class="mb-3">
                <img id="qrImage" src="" class="img-fluid rounded-3 border p-2" style="max-width: 220px;">
            </div>

            <p id="qrAmount" class="h5 fw-semibold mb-1">Amount: $0.00</p>
            <p id="paymentStatus" class="fw-bold text-primary mb-3">Waiting for payment...</p>

            <div class="d-flex justify-content-center gap-3 mt-3">
                <button id="cancelPaymentBtn" class="btn btn-outline-secondary rounded-pill px-4">
                    Cancel
                </button>
                <button id="confirmPaymentBtn" class="btn btn-success rounded-pill px-4">
                    Confirm
                </button>
            </div>

            <small class="text-muted d-block mt-3">
                Please scan the QR code using your banking app to complete the payment.
            </small>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let pollingInterval = null;
let currentMd5 = null;

/* --------------------------
   AUTO POLLING CHECK
--------------------------- */
async function autoCheckPayment() {
    const response = await fetch("{{ route('checkout.checkPayment') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            md5: currentMd5,
            manualPay: false
        })
    });

    return response.json();
}

/* --------------------------
   MANUAL CONFIRM PAYMENT CLICK
--------------------------- */
async function manualCheckPayment() {
    const response = await fetch("{{ route('checkout.checkPayment') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            md5: currentMd5,
            manualPay: true
        })
    });

    return response.json();
}

document.addEventListener('DOMContentLoaded', () => {

    const payBtn = document.getElementById('payWithQrBtn');
    const qrImg = document.getElementById('qrImage');
    const qrAmount = document.getElementById('qrAmount');
    const paymentStatus = document.getElementById('paymentStatus');
    const cancelBtn = document.getElementById('cancelPaymentBtn');
    const confirmBtn = document.getElementById('confirmPaymentBtn');

    payBtn.addEventListener('click', async () => {

        paymentStatus.textContent = "";

        const resp = await fetch("{{ route('checkout.generateQr') }}");
        const data = await resp.json();

        console.log(data);

        if (!data.qrUrl) {
            alert(data.error || "Failed to generate QR code");
            return;
        }

        currentMd5 = data.md5;
        qrImg.src = data.qrUrl;
        qrAmount.textContent = "Amount: $" + parseFloat(data.amount).toFixed(2);
        paymentStatus.textContent = "Waiting for payment...";

        const modal = new bootstrap.Modal(document.getElementById('qrModal'));
        modal.show();

        // Start polling
        if (pollingInterval) clearInterval(pollingInterval);

        pollingInterval = setInterval(async () => {
            const result = await autoCheckPayment();
            console.log("Auto check:", result);

            if (result.paid) {
                paymentStatus.textContent = "Payment Successful!";
                clearInterval(pollingInterval);

                setTimeout(() => {
                    window.location.href = "{{ route('home') }}?payment=success";
                }, 1500);
            }
        }, 4000);

    });

    /* Cancel */
    cancelBtn.addEventListener('click', () => {
        clearInterval(pollingInterval);
        paymentStatus.textContent = "Payment cancelled.";
    });

    /* Manual confirm */
    confirmBtn.addEventListener('click', async () => {
        paymentStatus.textContent = "Checking...";

        const result = await manualCheckPayment();
        console.log("Manual check:", result);

        if (result.paid) {
            paymentStatus.textContent = "Payment Successful!";
            clearInterval(pollingInterval);

            setTimeout(() => {
                window.location.href = "{{ route('home') }}?payment=success";
            }, 1500);

        } else {
            paymentStatus.textContent = "Payment still pending…";
        }
    });

});
</script>
@endsection
