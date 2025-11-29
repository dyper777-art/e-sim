 @extends('frontend.layout.master')

 @section('title', 'pricing')

 @section('content')

     <!-- food section -->

     <section class="food_section layout_padding">
         <div class="container">
             <div class="heading_container heading_center">
                 <h2>
                     Our Services
                 </h2>
             </div>

             <ul class="filters_menu">
                 <li class="active" data-filter="*">All</li>
                 @foreach ($categories as $category)
                    <li data-filter=".{{ $category->name }}">{{ $category->name }}</li>
                 @endforeach
             </ul>

             <div class="filters-content">
                 <div class="row grid">

                     @foreach ($plans as $plan)
                        <x-plan-card :plan="$plan" />
                    @endforeach

                 </div>
             </div>
         </div>
     </section>

     <!-- end food section -->


 @endsection
