
<nav role="navigation" class="breadcrumb-trail breadcrumbs">
    <div class="breadcrumbs">
        @unless ($breadcrumbs->isEmpty())
            @php
                $i = 0;
            @endphp
            @foreach ($breadcrumbs as $breadcrumb)
                @if (!is_null($breadcrumb->url) && !$loop->last)
                    <span class="trail-item trail-begin">
                        <a href="{{ $breadcrumb->url }}"><span>{{ $breadcrumb->title }}</span></a>
                    </span>
                    <span><i class="fa fa-angle-right"></i></span>
                @else
                    <span class="trail-item trail-end text-theme-colored1">{{ $breadcrumb->title }}</span>
                @endif
                @php
                    $i++
                @endphp
            @endforeach
        @endunless
    </div>
</nav> 