{{--
    Header zone split into 3 slots (left / center / right), each holding its
    own ad. Usage: @include('ads.header')
--}}
<div style="max-width: 80rem; margin: 0 auto; padding: 0 1rem; display: table; width: 100%;">
    <div style="display: table-row;">
        <div style="display: table-cell; width: 33.33%; vertical-align: top; padding: 0 0.5rem;">
            @include('ads.zone', ['zone' => 'header_left', 'ads' => $headerLeftAds ?? collect()])
        </div>
        <div style="display: table-cell; width: 33.33%; vertical-align: top; padding: 0 0.5rem;">
            @include('ads.zone', ['zone' => 'header_center', 'ads' => $headerCenterAds ?? collect()])
        </div>
        <div style="display: table-cell; width: 33.33%; vertical-align: top; padding: 0 0.5rem;">
            @include('ads.zone', ['zone' => 'header_right', 'ads' => $headerRightAds ?? collect()])
        </div>
    </div>
</div>
