 @extends('frontend.layout.master')

 @section('title', 'cart')

 @section('content')

     <!-- cart section -->

     <section class="cart_table cart_section layout_padding">
         <div class="container">
             <div class="heading_container heading_center">
                 <h2>
                     Cart Items
                 </h2>
             </div>

             <div class="filters-content">
                 <div class="row grid">

                     @forelse ($esimsGrouped as $plan)
                         <x-cart-item :plan="$plan" />
                     @empty
                         <div class="col-12 all cart-item" style="background-color:transparent; color:black">
                             <div class="box d-flex align-items-center py-2 cart-section">
                                 <div class="detail-box flex-grow-1">
                                     <div class="cart-row d-flex align-items-center flex-wrap">
                                         <div class="cart-col justify-content-center w-100">

                                             <h1>Your cart is empty.</h1>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     @endforelse

                     @if ($esimsGrouped->isNotEmpty())
                         <div class="col-12 all cart-item"
                             style="background-color:transparent; color:black; margin-top:20px; margin-bottom:-50px; padding: 0px 75px;">
                             <div class="box d-flex align-items-center py-2 cart-section">
                                 <div class="detail-box flex-grow-1">
                                     <div class="d-flex justify-content-between align-items-center w-100"
                                         style="padding: 10px;">
                                         <h1 class="m-0 checkout-total" data-checkout-total="{{ $totalAmount }}">Total :
                                             ${{ $totalAmount }}</h1>
                                         <button type="submit" class="btn btn-primary checkout-btn">Checkout</button>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     @endif


                     <script>
                         let checkoutTotal = document.querySelector('.checkout-total');

                         function increaseQty(btn) {
                             const input = btn.parentElement.querySelector('.qty-input');
                             let qty = parseInt(input.value);

                             const planId = btn.dataset.planId;

                             // Send request to cart.add API
                             fetch('/cart/add', {
                                     method: 'POST',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'Accept': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                     },
                                     body: JSON.stringify({
                                         plan_id: planId,
                                         quantity: 1 // always add ONE more sim
                                     })
                                 })
                                 .then(res => res.json())
                                 .then(data => {
                                     if (data.success) {

                                         // Increase local quantity
                                         qty += 1;
                                         input.value = qty;

                                         // update total
                                         updateTotal(btn, qty);

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
                                     alert("Error adding to cart.");
                                 });
                         }


                         function decreaseQty(btn) {
                             const qtyInput = btn.parentElement.querySelector('.qty-input');
                             let qty = parseInt(qtyInput.value);

                             const planId = btn.dataset.planId;

                             // If only 1 left → remove from cart via API
                             if (qty >= 1) {
                                 fetch(`cart/remove/${planId}`, {
                                         method: "DELETE",
                                         headers: {
                                             "Accept": "application/json",
                                             "Content-Type": "application/json",
                                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                         }
                                     })
                                     .then(res => res.json())
                                     .then(data => {
                                         if (data.success) {

                                             if (qty === 1) {
                                                 btn.closest('.col-12').remove();
                                                 location.reload();
                                             }

                                             qty -= 1;
                                             qtyInput.value = qty;
                                             updateTotal(btn, qty);
                                         } else {
                                             alert(data.message);
                                         }
                                     })
                                     .catch(err => console.error(err));

                                 return;
                             }

                         }



                         function updateTotal(btn, qty) {
                             const detailBox = btn.closest('.detail-box');
                             const priceEl = detailBox.querySelector('.cart-price h6');
                             const totalEl = detailBox.querySelector('.cart-total .total-amount');

                             console.log(priceEl, totalEl);

                             if (!priceEl || !totalEl) return; // safety check

                             const price = parseFloat(priceEl.dataset.price);
                             totalEl.dataset.total = price * qty;
                             totalEl.textContent = 'Total : $' + (price * qty);

                             updateTotalcheckout();
                         }

                         updateTotalcheckout = () => {
                             let newTotal = 0;
                             document.querySelectorAll('.total-amount').forEach(el => {
                                 newTotal += parseFloat(el.dataset.total);
                             });
                             checkoutTotal.dataset.checkoutTotal = newTotal;
                             checkoutTotal.textContent = 'Total : $' + newTotal;
                         }




                         function removeItem(btn) {
                             const col = btn.closest('.col-12');
                             if (col) col.remove();
                         }
                     </script>



                 </div>
             </div>
         </div>
     </section>

     <!-- end cart section -->


 @endsection
