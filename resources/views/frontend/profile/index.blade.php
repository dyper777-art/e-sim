@extends('frontend.layout.master')

@section('title', 'profile')


@section('content')

    <!-- book section -->
    <section class="book_section profile_section layout_padding">
        <div class="container">
            <div class="heading_container">
                <h2>
                    User Profile
                </h2>
            </div>
            <div class="container d-flex justify-content-center align-items-center">
                <div class="row w-100 align-items-center">
                    <!-- Profile Info -->
                    <div class="col-md-6 d-flex flex-column justify-content-center">
                        <div class="form_container">
                            <div class="mb-2">
                                <input type="text" class="form-control" value="User Name: {{ Auth::user()->name }}"
                                    readonly />
                            </div>
                            <div class="mb-2">
                                <input type="text" class="form-control" value="Email: {{ Auth::user()->email }}"
                                    readonly />
                            </div>
                            <div class="mb-2">
                                <input type="text" class="form-control"
                                    value="Created At: {{ Auth::user()->created_at->format('d M Y, H:i') }}" readonly />
                            </div>
                            <div class="btn_box mt-3">
                                <button class="btn btn-primary" onclick="logout()">Logout</button>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Image -->
                    <div class="col-md-6 d-flex justify-content-center align-items-center">
                        <img src="{{ asset('frontend/assets/images/profile.jpg') }}" alt="Profile Image"
                            class="profile-img img-fluid" style="max-height: 500px;">
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- end book section -->


    <script>
        function logout() {
            fetch("{{ route('logout') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    }
                })
                .then(data => {

                    window.location.href = "/";
                })
                .catch(error => {
                    console.error("Logout failed:", error);
                });
        }
    </script>

@endsection
