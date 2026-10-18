{{--
    Header zone split into 3 slots (left / center / right), each holding its
    own ad. Usage: @include('ads.header')
--}}
<div class="max-w-7xl mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @include('ads.zone', ['zone' => 'header_left', 'ads' => $headerLeftAds ?? collect()])
        @include('ads.zone', ['zone' => 'header_center', 'ads' => $headerCenterAds ?? collect()])
        @include('ads.zone', ['zone' => 'header_right', 'ads' => $headerRightAds ?? collect()])
    </div>
</div>
