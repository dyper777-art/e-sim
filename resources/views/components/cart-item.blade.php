@props(['plan'])

<div class="col-12 all cart-item">
    <div class="box d-flex align-items-center py-2 cart-section">
        <!-- Image -->
        <div class="img-box me-3">
            <img src="{{ asset('frontend/assets/images/f1.png') }}" alt="">
        </div>

        <!-- Details -->
        <div class="detail-box flex-grow-1">
            <div class="cart-row d-flex align-items-center flex-wrap">
                <!-- Plan Name -->
                <div class="cart-col cart-name flex-grow-1">
                    <h5 class="m-0">{{ $plan->plan_name }}</h5>
                </div>

                <!-- Price -->
                <div class="cart-col cart-price">
                    <h6 class="m-0 price" data-price="{{ $plan->price }}">${{ $plan->price }}</h6>
                </div>

                <!-- Quantity -->
                <div class="cart-col cart-qty d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary me-1" data-plan-id="{{ $plan->plan_id }}"
                        onclick="decreaseQty(this)">-</button>
                    <input type="text" class="form-control form-control-sm text-center qty-input"
                        value="{{ $plan->quantity }}" data-cart-ids="{{ implode(',', $plan->cart_ids) }}">
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-1" data-plan-id="{{ $plan->plan_id }}"
                        onclick="increaseQty(this)">+</button>
                </div>

                <!-- Total -->
                <div class="cart-col cart-total">
                    <h6 class="m-0 total-amount" data-total="{{ $plan->total }}">Total: ${{ $plan->total }}</h6>
                </div>

                <!-- Trash -->
                <div class="cart-col cart-trash">
                    <form action="{{ route('cart.destroy', $plan->plan_id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger trash-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0V6H6v6.5a.5.5 0 0 1-1 0v-7z"/>
                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9.5A1.5 1.5 0 0 1
                                     11.5 15h-7A1.5 1.5 0 0 1 3 13.5V4h-.5a1 1 0 1 1
                                     0-2h3.5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1
                                     0 0 1 1 1zM4.118 4 4 4.059V13.5a.5.5 0 0 0 .5.5h7a.5.5
                                     0 0 0 .5-.5V4.059L11.882 4H4.118z"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
