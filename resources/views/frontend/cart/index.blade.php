@extends('frontend.layout.master')

@section('title', 'Cart')

@section('content')

<!-- Cart Section -->
<section class="cart_table cart_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>Cart Items</h2>
        </div>

        <div class="filters-content">
            <div class="row grid">

                @forelse ($esimsGrouped as $plan)
                    <x-cart-item :plan="$plan" />
                @empty
                    <div class="col-12 all cart-item text-center" style="background-color:transparent; color:black">
                        <h3>Your cart is empty.</h3>
                    </div>
                @endforelse

                @if ($esimsGrouped->isNotEmpty())
                    <div class="col-12 all cart-item"
                         style="background-color:transparent; color:black; margin-top:20px; padding: 0 75px;">
                        <div class="box d-flex align-items-center py-2 cart-section">
                            <div class="detail-box flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center w-100" style="padding: 10px;">
                                    <h4 class="m-0 checkout-total" data-checkout-total="{{ $totalAmount }}">
                                        Total: ${{ $totalAmount }}
                                    </h4>

                                    <!-- Checkout Form -->
                                    <form action="{{ route('checkout.index') }}" method="GET">
                                        <button type="submit" class="btn btn-primary checkout-btn">Checkout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Cart JS -->
                <script>
                    let checkoutTotal = document.querySelector('.checkout-total');

                    function increaseQty(btn) {
                        const input = btn.closest('.cart-row').querySelector('.qty-input');
                        let qty = parseInt(input.value);
                        const planId = btn.dataset.planId;

                        fetch('/cart/add', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ plan_id: planId, quantity: 1 })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                qty += 1;
                                input.value = qty;
                                updateTotal(btn, qty);
                            } else {
                                alert(data.message || 'Something went wrong!');
                            }
                        })
                        .catch(err => console.error(err));
                    }

                    function decreaseQty(btn) {
                        const input = btn.closest('.cart-row').querySelector('.qty-input');
                        let qty = parseInt(input.value);
                        const planId = btn.dataset.planId;

                        if (qty <= 0) return;

                        fetch(`/cart/remove/${planId}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                if (qty === 1) {
                                    btn.closest('.col-12').remove();
                                    updateTotalcheckout();
                                    return;
                                }
                                qty -= 1;
                                input.value = qty;
                                updateTotal(btn, qty);
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(err => console.error(err));
                    }

                    function updateTotal(btn, qty) {
                        const detailBox = btn.closest('.detail-box');
                        const priceEl = detailBox.querySelector('.cart-price h6');
                        const totalEl = detailBox.querySelector('.cart-total .total-amount');

                        if (!priceEl || !totalEl) return;

                        const price = parseFloat(priceEl.dataset.price);
                        totalEl.dataset.total = price * qty;
                        totalEl.textContent = 'Total: $' + (price * qty);

                        updateTotalcheckout();
                    }

                    function updateTotalcheckout() {
                        let newTotal = 0;
                        document.querySelectorAll('.total-amount').forEach(el => {
                            newTotal += parseFloat(el.dataset.total);
                        });
                        checkoutTotal.dataset.checkoutTotal = newTotal;
                        checkoutTotal.textContent = 'Total: $' + newTotal;
                    }
                </script>

            </div>
        </div>
    </div>
</section>
<!-- End Cart Section -->

@endsection
