@php($ads = $ads ?? collect())
@if($ads->isNotEmpty())
    <div data-ad-zone="{{ $zone }}" class="ad-zone" style="text-align: center; height: 100%; display: flex; flex-direction: column; justify-content: center;">
        @foreach($ads as $ad)
            <a href="{{ $ad->target_url ?: '#' }}"
               style="display: block; background: white; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 1rem; text-align: center; width: 100%;"
               @if($ad->target_url) target="_blank" rel="noopener noreferrer" @endif
               data-ad-id="{{ $ad->id }}">
                @if($ad->html_snippet)
                    {!! $ad->html_snippet !!}
                @elseif($ad->image)
                    <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" style="width: 100%; height: auto; max-height: 8rem; object-fit: contain; margin: 0 auto 0.5rem; display: block;" loading="lazy">
                    @if($ad->description)
                        <p style="font-size: 0.875rem; color: #4b5563;">{{ $ad->description }}</p>
                    @endif
                @else
                    <span style="color: #DC143C; font-weight: bold;">{{ $ad->title }}</span>
                    @if($ad->description)
                        <p style="font-size: 0.875rem; color: #4b5563;">{{ $ad->description }}</p>
                    @endif
                @endif
            </a>
        @endforeach
    </div>
@endif