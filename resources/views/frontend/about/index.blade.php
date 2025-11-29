@extends('frontend.layout.master')

@section('title', 'about')

@section('content')
    <!-- about section -->

    <section class="about_section layout_padding">
        <div class="container  ">

            <div class="row">
                <div class="col-md-6 ">
                    <div class="img-box">
                        <img src="{{ asset('frontend/assets/images/esim-card-line-icon-2.png') }}" alt="">
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
                            Feane provides next-generation eSIM solutions tailored for government and enterprise use. Our
                            eSIMs enable secure, scalable, and flexible mobile connectivity, designed to meet the highest
                            standards of reliability and compliance. From small-scale pilot projects to nationwide
                            deployments, our technology ensures that government agencies can stay connected without the
                            logistical challenges of physical SIM cards.
                        </p>
                        <p>
                            Our solutions simplify device management while maintaining strict security protocols.
                            Governments can provision, update, and manage eSIMs remotely, reducing operational costs and
                            improving efficiency. With Feane, connectivity is instant, secure, and adaptable to the needs of
                            modern public sector operations.
                        </p>
                        <p>
                            We understand the unique requirements of B2G operations: compliance, data privacy, and robust
                            reporting. That’s why every eSIM we deploy is built with government-grade security and auditing
                            capabilities. Agencies can monitor usage, enforce policies, and scale operations with
                            confidence, knowing their communications infrastructure is fully controlled and optimized.
                        </p>
                        <p>
                            Whether your focus is citizen services, internal communication networks, or field operations,
                            Feane’s eSIM solutions empower government agencies with the flexibility and reliability they
                            need. Experience seamless connectivity, simplified management, and unmatched security — all in
                            one platform designed for the unique demands of the public sector.
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- end about section -->
@endsection
