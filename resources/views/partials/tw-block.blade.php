@foreach($twSecs as $sec)
<div class="tw-section" style="{{ $loop->last ? 'margin-bottom:0;':'' }}">
    <div class="tw-section-label">
        <span class="tw-dot" style="background:{{ $sec['dot'] }};"></span>
        {{ $sec['label'] }}
        @isset($sec['note'])<span style="font-weight:400;color:#94a3b8;">{{ $sec['note'] }}</span>@endisset
    </div>
    <div class="form-grid-4">
        @foreach([1,2,3,4] as $tw)
        <div class="tw-card">
            <div class="tw-card-head">TW {{ ['I','II','III','IV'][$tw-1] }}</div>
            <div class="tw-card-body">
                <input type="text" name="{{ $sec['prefix'] }}{{ $tw }}[]"
                       class="tw-card-inp{{ $sec['fmt'] ? ' anggaran-fmt':'' }}"
                       placeholder="{{ $sec['fmt'] ? '0':'—' }}">
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach