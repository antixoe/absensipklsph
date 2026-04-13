@if ($paginator->hasPages())
    <ul class="pagination" style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; list-style: none; padding: 0; margin: 0;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li style="list-style: none;"><span style="padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; color: #d1d5db; display: inline-flex; align-items: center; justify-content: center; background: #f9fafb; cursor: not-allowed;">&laquo;</span></li>
        @else
            <li style="list-style: none;"><a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="padding: 10px 14px; border: 1px solid #fed7aa; border-radius: 6px; font-size: 13px; background: white; color: #f97316; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">&laquo;</a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li style="list-style: none;"><span style="padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; color: #d1d5db; display: inline-flex; align-items: center; justify-content: center; background: #f9fafb;">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li style="list-style: none;"><span style="padding: 10px 14px; border: 1px solid #f97316; border-radius: 6px; font-size: 13px; background: #f97316; color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 600;">{{ $page }}</span></li>
                    @else
                        <li style="list-style: none;"><a href="{{ $url }}" style="padding: 10px 14px; border: 1px solid #fed7aa; border-radius: 6px; font-size: 13px; background: white; color: #f97316; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li style="list-style: none;"><a href="{{ $paginator->nextPageUrl() }}" rel="next" style="padding: 10px 14px; border: 1px solid #fed7aa; border-radius: 6px; font-size: 13px; background: white; color: #f97316; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">&raquo;</a></li>
        @else
            <li style="list-style: none;"><span style="padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; color: #d1d5db; display: inline-flex; align-items: center; justify-content: center; background: #f9fafb; cursor: not-allowed;">&raquo;</span></li>
        @endif
    </ul>
@endif

