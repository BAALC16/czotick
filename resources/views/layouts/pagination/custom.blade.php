@if ($paginator->hasPages())
    <div class="col-sm-12">
        <nav>
            <ul class="pagination">
                @if ($paginator->onFirstPage()) 
                
                @else
                    <li class="page-item"><a class="next page-link" href="{{ $paginator->previousPageUrl() }}">«</a></li>
                @endif
                @foreach ($elements as $element)
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                               
                            @endif
                        @endforeach
                    @endif
            
                @endforeach
               
                @if ($paginator->hasMorePages())
                    <li class="page-item"><a class="next page-link" href="{{ $paginator->nextPageUrl() }}">»</a></li>
                @endif
            </ul>
        </nav>

    </div>
@endif