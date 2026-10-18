{{--
    Unified ad zone renderer (seneweb-style slot wrapper).
    Usage: @include('ads.zone', ['zone' => 'header', 'ads' => $headerAds])
--}}
@php($ads = $ads ?? collect())
@if($ads->isNotEmpty())
    <!-- Ad Zone: {{ $zone }} -->
    <div class="ad-zone space-y-4" data-ad-zone="{{ $zone }}" style="margin: 20px 0; text-align: center;">
        @foreach($ads as $ad)
            <a href="{{ $ad->target_url ?: '#' }}"
               class="ad-zone__item block bg-white rounded-xl shadow-md p-4 text-center"
               @if($ad->target_url) target="_blank" rel="noopener noreferrer" @endif
               data-ad-id="{{ $ad->id }}">
                @if($ad->html_snippet)
                    {!! $ad->html_snippet !!}
                @elseif($ad->image)
                    <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" class="max-h-48 mx-auto mb-2" loading="lazy">
                    @if($ad->description)
                        <p class="text-sm text-gray-600">{{ $ad->description }}</p>
                    @endif
                @else
                    <span class="text-senegal-red font-bold">{{ $ad->title }}</span>
                    @if($ad->description)
                        <p class="text-sm text-gray-600">{{ $ad->description }}</p>
                    @endif
                @endif
            </a>
        @endforeach
    </div>
    <!--end Ad Zone: {{ $zone }} -->
@endif
