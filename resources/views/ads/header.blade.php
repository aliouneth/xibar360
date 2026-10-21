{{--
    Header zone split into 3 slots (left / center / right), each holding its
    own ad. Side by side on desktop, stacked on mobile. Usage: @include('ads.header')
--}}
<style>
    .header-ads { max-width: 80rem; margin: 0 auto; padding: 0 1rem; }
    .header-ads__row { display: flex; flex-direction: row; gap: 1rem; }
    .header-ads__cell { flex: 1 1 0; min-width: 0; }
    .header-ads .ad-zone { margin: 0 0 20px; }
    @media (max-width: 768px) {
        .header-ads { padding: 0 0.5rem; }
        .header-ads__row { flex-direction: column; gap: 0; }
        .header-ads__cell { flex: 0 0 auto; width: 100%; }
        .header-ads .ad-zone { margin: 4px 0; }
    }
</style>
<div class="header-ads">
    <div class="header-ads__row">
        <div class="header-ads__cell">
            @include('ads.zone', ['zone' => 'header_left', 'ads' => $headerLeftAds ?? collect()])
        </div>
        <div class="header-ads__cell">
            @include('ads.zone', ['zone' => 'header_center', 'ads' => $headerCenterAds ?? collect()])
        </div>
        <div class="header-ads__cell">
            @include('ads.zone', ['zone' => 'header_right', 'ads' => $headerRightAds ?? collect()])
        </div>
    </div>
</div>
