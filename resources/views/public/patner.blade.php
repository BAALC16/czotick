@extends('public.layouts_patners')
@section('content')
    <section class="page-title" style="background-image:url(images/background/20.jpg);">
        <div class="container">
            <div class="title-text clearfix">
                <h1>Partenaires</h1>
                <ul class="title-menu">
                    <li><a href="/">Accueil</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
                    <li>Partenaires</li>
                </ul>
            </div>
        </div>
    </section>
   
    <!--couses-section-->
    <section class="couses-section">
        <div class="container">
            <div class="row">
                @forelse($patners as $patner)
                    <div class="image-column col-md-4 col-sm-6 col-xs-12">
                        <div class="image-holder">
                            <div class="image-box">
                                <figure>
                                    <a href="cause-details.html"><img src="/public/storage/{{ $patner->image }}" alt="" width="370" height="240"></a>
                                </figure>                  
                            </div>
                            <div class="image-content" style="height: 250px;">
                                <a><h5>{{ $patner->name }}</h5></a>
                                <h6><span> Contact:</span> {{ $patner->contact }}</h6>
                                <h6><span>Email:</span> {{ $patner->email }}</h6>
                                <h6><span>Site web:</span> {{ $patner->website }}</h6>
                            </div>
                        </div>
                    </div>
                @empty

                @endforelse
            </div>

        </div>
    </section>
    <!--End couses-section-->


@endsection

