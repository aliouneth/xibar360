{{--
    Full-page clickable background ad (seneweb #clickable-bg-banner style).
    Fixed behind all content; page gaps/margins reveal and click through to it.
--}}
@php($wrapperAd = $wrapperAd ?? null)
@if($wrapperAd)
    <a href="{{ $wrapperAd->target_url ?: '#' }}"
       id="clickable-bg-banner"
       data-ad-id="{{ $wrapperAd->id }}"
       @if($wrapperAd->target_url) target="_blank" rel="noopener noreferrer" @endif>
        @if($wrapperAd->image)
            <img src="{{ asset('storage/' . $wrapperAd->image) }}" alt="{{ $wrapperAd->title }}">
        @elseif($wrapperAd->html_snippet)
            {!! $wrapperAd->html_snippet !!}
        @endif
    </a>
@endif
