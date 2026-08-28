@foreach([1,2,3,4] as $tw)
@php $field = $prefix.$tw; @endphp
<td class="xls-td">
    <input type="text" class="xls-input {{ $fmt ? 'anggaran-fmt ' : '' }}{{ $isOperator ? '' : 'readonly' }}"
        value="{{ $fmt ? number_format($item->$field ?? 0, 0, ',', '.') : ($item->$field ?? '') }}"
        {{ $isOperator ? '' : 'readonly' }}
        data-id="{{ $item->id }}" data-field="{{ $field }}"
        title="{{ strtoupper(str_replace('_',' ',$field)) }}">
</td>
@endforeach

