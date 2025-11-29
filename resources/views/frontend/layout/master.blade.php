<!DOCTYPE html>
<html>

<head>

    @include('frontend.layout.style')

</head>

<body class="{{ trim($__env->yieldContent('title')) !== 'home' ? 'sub_page' : '' }}">

  <div class="hero_area">
    <div class="bg-box">
      <img src="{{asset('frontend/assets/images/hero-bg.jpg')}}" alt="">
    </div>

    @include('frontend.layout.header')

    @if (trim($__env->yieldContent('title')) === 'home')
        @include('frontend.layout.slider')
    @endif

  </div>

    @yield('content')

  @include('frontend.layout.footer')

  @include('frontend.layout.jsshop')

</body>

</html>
