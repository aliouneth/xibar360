@php($ads = $ads ?? collect())
@if($ads->isNotEmpty())
    <div data-ad-zone="{{ $zone }}" style="margin: 20px 0; text-align: center;">
        @foreach($ads as $ad)
            <a href="{{ $ad->target_url ?: '#' }}"
               style="display: inline-block; background: white; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 1rem; text-align: center; margin: 0 0.5rem; vertical-align: top; max-width: 30%;"
               @if($ad->target_url) target="_blank" rel="noopener noreferrer" @endif
               data-ad-id="{{ $ad->id }}">
                @if($ad->html_snippet)
                    {!! $ad->html_snippet !!}
                @elseif($ad->image)
                    <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" style="max-height: 12rem; margin: 0 auto 0.5rem;" loading="lazy">
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