@extends('public.layouts_index')
@section('content')
<section class="page-title divider layer-overlay overlay-dark-5 section-typo-light bg-img-center" data-tm-bg-img="{{ URL::asset('assets/front/images/ban.png') }}">
    <div class="container pt-90 pb-90">
      <!-- Section Content -->
      <div class="section-content">
        <div class="row">
          <div class="col-md-12 text-center">
            <h2 class="title text-white">{{ $activity->title }}</h2>
            {{ Breadcrumbs::render('public.single-activity', $activity) }} 
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Section: inner-header End -->

  <section class="bg-theme-colored1">
    <div class="container pt-40 pb-40">
      <div class="row text-center">
        <div class="col-md-12">
          <h2 id="basic-coupon-clock" class="text-white"></h2>
          <!-- Final Countdown Timer Script -->
          <script>
                let eventDate = @json($activity->dateStart);
                
                (function($) {
                $('#basic-coupon-clock').countdown(eventDate, function(event) {
                    $(this).html(event.strftime('%D jours %H:%M:%S'));
                });
                })(jQuery);
          </script>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <ul class="list-style-none">
            @if ($activity->location != NULL)
                <li>
                    <h5>Location:</h5>
                    {{ $activity->location }}
                </li>     
            @endif
            <li>
                <h5>Date de début:</h5>
                {{ Carbon::parse($activity->dateStart)->isoFormat('D MMMM YYYY') }}
              
            </li>
            <li>
                <h5>Date de fin:</h5>
                {{ Carbon::parse($activity->dateEnd)->isoFormat('D MMMM YYYY') }}
                
            </li>
            <li>
                <h5>Description:</h5>
                <p>{!! $activity->content !!}</p>
                
            </li>
          </ul>
        </div>
        <div class="col-md-8">
          <img src="{{ url('public/storage/'.$activity->image) }}" alt="images" width="700">
        </div>
      </div>
     {{--  <div class="row mt-60">
        <div class="col-md-6">
          <h4 class="mt-0">Description</h4>
          <p>{!! $activity->content !!}</p>
        </div> 
      </div> --}}
    
    </div>
  </section>
     
@endsection
