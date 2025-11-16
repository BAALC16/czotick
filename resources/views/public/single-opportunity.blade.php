@extends('public.layouts_index')
@section('content')
    <section class="page-title" style="background-image:url(images/background/20.jpg);">
        <div class="container">
            <div class="title-text clearfix">
                <h1>Opportunité</h1>
                <ul class="title-menu">
                    <li><a href="/">Accueil</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
                    <li>Opportunité</li>
                </ul>
            </div>
        </div>
    </section>
    <!--news-section-->
    <section class="couses-section style-two style-three style-four">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-sm-12 col-xs-12">
                    <div class="left-side">
                        <div class="image-holder">
                            <div class="image-box">
                                <figure class="activity">
                                    <a><img src="/public/{{ $opportunity->image }}" alt=""></a>
                                </figure>
                            </div>
                            <div class="image-content">
                                @php
                                    //Carbon::setLocale('fr');
                                @endphp
                                {{-- <h5>{{ Carbon::parse( $opportunity->created_at)->isoFormat("DD") }}<br><span>{{ $opportunity->created_at->translatedFormat('M') }}</span></h5> --}}
                                <div>
                                    <a><h4>{{  $opportunity->title }}</h4></a>
                                </div>
                                <!-- <ul class="blog-info">
                                    <li><i class="fa fa-user" aria-hidden="true"></i>admin</li>
                                    <li><i class="fa fa-comments-o" aria-hidden="true"></i>29 comments</li>
                                </ul> -->
                                <div class="content-text">
                                    {!! $opportunity->content !!}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="col-md-4 col-sm-12 col-xs-12">
                    @if(count($newOpportunities) > 0)
                        <div class="right-side">
                            <div class="section-title">
                                <h5>Opportunités récentes</h5>
                            </div>
                            <div class="news-list">
                                @forelse ($newOpportunities as $newOpportunity)
                                    <ul class="item-list">
                                        <li class="item" style="height: 80px;">
                                            <div class="image-box">
                                                <figure>
                                                    <a href="/opportunity/{{ $newOpportunity->slug }}"><img src="/public/{{ $newOpportunity->image }}" alt="" width="80" height="75"></a>
                                                </figure>
                                            </div>
                                            <div class="item-content">
                                                <a href="/opportunity/{{ $newOpportunity->slug }}"><h6>{{  Str::words($newOpportunity->title, 34,'...') }}</h6></a>
                                            </div>
                                        </li>
                                    </ul>
                                @empty

                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!--End news-section-->

@endsection

