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


{{--
  Partial tabel nilai LKE — dipakai dua kali:
  1) mode='operator' → Lembar Kerja Operator (nilai asli)
  2) mode='evaluator' → Lembar Kerja Evaluator (nilai resmi, dipakai Rekap)

  Parameter wajib dari pemanggil:
  $mode              : 'operator' | 'evaluator'
  $editable          : bool — apakah user saat ini boleh mengedit tabel ini
  $judulLembar       : string judul header
  $nilaiPerSubX       : array nilai per sub-komponen (sesuai mode)
  $nilaiKomponenUtamaX: array total per komponen utama (sesuai mode)
  $nilaiAkhirX        : float total akhir
--}}
@php
  $idPrefix     = $mode; // 'operator' atau 'evaluator'
  $fieldJawaban = $mode === 'operator' ? 'jawaban_operator' : 'jawaban';
  $warnaMapLocal = ['AA'=>'#059669','A'=>'#2563eb','BB'=>'#7c3aed','B'=>'#d4982e','CC'=>'#f59e0b','C'=>'#dc2626','D'=>'#991b1b','E'=>'#6b7280'];
  $accentColor  = $mode === 'operator' ? '#3b82f6' : '#10b981';
@endphp

<div class="lke-card" style="margin-top:24px;">
  <div class="lke-card-header" style="border-left:4px solid {{ $accentColor }};">
    <div style="font-size:14px;font-weight:800;color:var(--t1);">
      {{ $mode === 'operator' ? '🟦' : '🟩' }} {{ $judulLembar }}
    </div>
    @if(!$editable)
      <span class="badge badge-dim">👁️ Mode Lihat Saja</span>
    @endif
  </div>
</div>

<div class="lke-table-wrap">
  <table class="lke-table">
    <thead>
      <tr>
        <th style="width:44px;">No</th>
        <th style="text-align:left;min-width:260px;">Komponen / Sub / Kriteria</th>
        <th style="width:68px;">Bobot</th>
        <th style="width:110px;">Nilai</th>
        <th style="width:240px;">Jawaban (Predikat)</th>
        @if($mode === 'evaluator')
          <th style="width:160px;">Catatan Evaluasi</th>
          <th style="width:160px;">Komentar Verifikator</th>
        @else
          <th style="width:160px;">Catatan Operator</th>
          <th style="width:200px;">Evidence &amp; Dokumen</th>
        @endif
      </tr>
    </thead>
    <tbody>

    @foreach($komponenUtama as $k)
    @php $nilaiK = $nilaiKomponenUtamaX[$k->id] ?? 0; @endphp

    <tr class="row-komponen">
      <td style="text-align:center;"><span style="font-size:16px;font-weight:800;color:#93c5fd;">{{ $k->urutan }}</span></td>
      <td>
        <div style="font-size:14px;font-weight:800;color:var(--t1);">{{ $k->nama }}</div>
        <div style="font-size:10px;color:#93c5fd;margin-top:3px;">Kode: {{ $k->kode }}</div>
      </td>
      <td style="text-align:center;"><span style="font-size:15px;font-weight:800;color:#93c5fd;">{{ $k->bobot }}</span></td>
      <td style="text-align:center;">
        <span id="{{ $idPrefix }}_total_komponen_{{ $k->id }}" style="font-size:18px;font-weight:900;color:#60a5fa;">
          {{ number_format($nilaiK, 2) }}
        </span>
      </td>
      <td colspan="3" style="color:rgba(255,255,255,.25);text-align:center;font-size:11px;">—</td>
    </tr>

    @foreach($subKomponen->where('parent_id', $k->id) as $s)
    @php
      $penSub   = $nilaiSubKomponen[$s->id] ?? null;
      $nilaiS   = $nilaiPerSubX[$s->id] ?? 0;
      $krList   = $kriteriaPerKomponen[$s->id] ?? collect();
      $jawabanS = $penSub?->{$fieldJawaban} ?? '';
      $warnaJwb = $jawabanS ? ($warnaMapLocal[$jawabanS] ?? '#71717a') : '#71717a';
      $subKode  = preg_replace('/[^a-zA-Z]/', '', $s->kode);
    @endphp

    <tr class="row-sub">
      <td style="text-align:center;"><div class="sub-nomor">{{ $k->urutan }}{{ $subKode }}</div></td>
      <td>
        <div style="font-size:13px;font-weight:700;color:var(--t1);">{{ $s->nama }}</div>
        <div style="font-size:10px;color:var(--t3);margin-top:3px;">{{ $krList->count() }} kriteria</div>
      </td>
      <td style="text-align:center;font-weight:700;color:#a5b4fc;">{{ $s->bobot }}</td>

      {{-- NILAI --}}
      <td style="text-align:center;">
        @if($editable)
          <input type="number"
                 name="{{ $mode === 'operator' ? 'nilai_operator' : 'nilai' }}[{{ $s->id }}]"
                 id="{{ $idPrefix }}_nilai_{{ $s->id }}"
                 value="{{ $nilaiS > 0 ? number_format($nilaiS, 2) : '' }}"
                 readonly
                 style="background:rgba(255,255,255,.08);border:1px solid rgba(99,102,241,.3);border-radius:8px;padding:8px 6px;width:90px;text-align:center;color:#34d399;font-size:14px;font-weight:800;">
          <div style="font-size:10px;color:#6ee7b7;margin-top:3px;">Max: {{ $s->bobot }}</div>
        @else
          @if($nilaiS > 0)
            <div class="nilai-display">{{ number_format($nilaiS, 2) }}</div>
          @else
            <span class="nilai-dim">Belum Dinilai</span>
          @endif
        @endif
      </td>

      {{-- JAWABAN --}}
      <td style="text-align:center;">
        @if($editable)
          <select name="{{ $fieldJawaban }}[{{ $s->id }}]"
                  id="{{ $idPrefix }}_jawaban_{{ $s->id }}"
                  class="jawaban-select"
                  data-prefix="{{ $idPrefix }}"
                  data-sub="{{ $s->id }}"
                  data-komponen="{{ $k->id }}"
                  data-bobot="{{ $s->bobot }}"
                  onchange="updateNilaiDariJawaban(this)">
            <option value="">-- Pilih Predikat --</option>
            @foreach(['AA','A','BB','B','CC','C','D','E'] as $opt)
              <option value="{{ $opt }}" {{ $jawabanS == $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
          </select>
          <div style="font-size:10px;color:#94a3b8;margin-top:4px;">Bobot: {{ $s->bobot }}</div>
        @else
          <span style="font-size:14px;font-weight:800;color:{{ $warnaJwb }};">{{ $jawabanS ?: '—' }}</span>
        @endif
      </td>

      @if($mode === 'evaluator')
        <td>
          @if($editable)
            <textarea name="catatan_sub[{{ $s->id }}]" rows="2" class="dk-textarea"
                      placeholder="Catatan evaluasi...">{{ $penSub?->catatan ?? '' }}</textarea>
          @else
            @if($penSub?->catatan)
              <div class="catatan-admin">📝 {{ $penSub->catatan }}</div>
            @else
              <span style="font-size:11px;color:var(--t4);font-style:italic;">—</span>
            @endif
          @endif
        </td>
        <td style="text-align:center;font-size:10px;color:var(--t4);">(per kriteria)</td>
      @else
        <td style="text-align:center;font-size:10px;color:var(--t4);">(per kriteria)</td>
        <td style="text-align:center;font-size:10px;color:var(--t4);">(per kriteria)</td>
      @endif
    </tr>

    @foreach($krList as $kr)
    @php
      $cat  = $catatanPerKriteria[$kr->id] ?? null;
      $docs = $dokumenPerKriteria[$kr->id] ?? [];
    @endphp

    <tr class="row-kriteria">
      <td style="text-align:center;vertical-align:top;padding-top:13px;"><div class="kr-nomor">{{ $kr->nomor }}</div></td>
      <td style="vertical-align:top;"><div style="font-size:12px;color:var(--t2);line-height:1.55;">{{ $kr->uraian }}</div></td>
      <td colspan="3" style="text-align:center;color:var(--t4);font-size:11px;">—</td>

      @if($mode === 'evaluator')
        <td style="vertical-align:top;">
          @if($cat?->catatan_operator)
            <div class="catatan-opd">{{ $cat->catatan_operator }}</div>
          @else
            <span style="font-size:11px;color:var(--t4);font-style:italic;">Belum diisi operator</span>
          @endif
        </td>
        <td style="vertical-align:top;">
          @if($editable)
            <textarea name="komentar_admin[{{ $kr->id }}]" rows="2" class="dk-textarea"
                      placeholder="Komentar Verifikator...">{{ $cat?->komentar_admin ?? '' }}</textarea>
          @else
            @if($cat?->komentar_admin)
              <div class="catatan-admin">💬 {{ $cat->komentar_admin }}</div>
            @else
              <span style="font-size:11px;color:var(--t4);font-style:italic;">Belum ada komentar</span>
            @endif
          @endif
        </td>
      @else
        <td style="vertical-align:top;">
          @if($editable)
            <textarea name="catatan_operator[{{ $kr->id }}]" rows="2" class="dk-textarea"
                      placeholder="Catatan unit/OPD...">{{ $cat?->catatan_operator ?? '' }}</textarea>
          @else
            @if($cat?->catatan_operator)
              <div class="catatan-opd">{{ $cat->catatan_operator }}</div>
            @else
              <span style="font-size:11px;color:var(--t4);font-style:italic;">—</span>
            @endif
          @endif
        </td>
        <td style="vertical-align:top;">
          <div id="evidence_{{ $kr->id }}_display" style="{{ $cat?->daftar_evidence ? '' : 'display:none;' }}">
            <div style="font-size:11px;line-height:1.6;color:var(--t2);background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:8px;padding:8px 10px;margin-bottom:6px;white-space:pre-line;">
              {!! \App\Helpers\TextHelper::linkify($cat->daftar_evidence ?? '') !!}
            </div>
            @if($editable)
              <button type="button" class="btn btn-ghost" style="font-size:10px;padding:3px 8px;margin-bottom:6px;" onclick="toggleEvidenceEdit({{ $kr->id }})">✏️ Edit</button>
            @endif
          </div>
          @if($editable)
          <div id="evidence_{{ $kr->id }}_edit" style="{{ $cat?->daftar_evidence ? 'display:none;' : '' }}">
            <textarea name="daftar_evidence[{{ $kr->id }}]" rows="2" class="dk-textarea" style="margin-bottom:6px;"
                      placeholder="- RPJMD 2021-2026&#10;- Renstra Dinas">{{ $cat?->daftar_evidence ?? '' }}</textarea>
          </div>
          <div class="upload-row">
            <input type="file" id="file_kr_{{ $kr->id }}" accept=".pdf,.docx,.xlsx,.doc,.xls,.jpg,.jpeg,.png">
            <button type="button" class="btn btn-upload" onclick="ajaxUpload({{ $kr->id }}, {{ $tahun }}, {{ $opdId }}, this)">📤</button>
          </div>
          @endif

          <div id="docs_kr_{{ $kr->id }}">
            @if(count($docs) > 0)
              @foreach($docs as $doc)
              <div class="doc-item" id="doc_item_{{ $doc->id }}">
                <span style="font-size:12px;">📄</span>
                <span class="doc-name" title="{{ $doc->nama_file }}">{{ $doc->nama_file }}</span>
                <a href="{{ route('evaluasi.lke.lihat.dokumen', $doc->id) }}" target="_blank" class="btn btn-lihat">👁</a>
                @if($editable)
                <button type="button" class="btn btn-del" onclick="ajaxHapus({{ $doc->id }}, this)">🗑</button>
                @endif
              </div>
              @endforeach
            @else
              <div style="padding:5px 8px;font-size:10.5px;color:var(--t4);font-style:italic;background:rgba(255,255,255,.02);border-radius:6px;margin-top:5px;text-align:center;">
                Belum ada dokumen
              </div>
            @endif
          </div>
        </td>
      @endif
    </tr>
    @endforeach

    <tr class="row-total">
      <td colspan="3" style="text-align:right;padding-right:16px;">Total — {{ $k->nama }}</td>
      <td style="text-align:center;">
        <span id="{{ $idPrefix }}_total_bawah_{{ $k->id }}" style="font-size:18px;font-weight:900;">{{ number_format($nilaiK, 2) }}</span>
      </td>
      <td colspan="3" style="color:var(--t4);">—</td>
    </tr>
    <tr class="row-spacer"><td colspan="7"></td></tr>
    @endforeach

    <tr class="row-grand">
      <td colspan="3" style="text-align:right;padding-right:16px;font-size:14px;font-weight:800;color:var(--t1);">
        {{ $mode === 'operator' ? 'TOTAL NILAI OPERATOR (ASLI)' : 'NILAI AKHIR RESMI (EVALUATOR)' }}
      </td>
      <td style="text-align:center;">
        <span id="{{ $idPrefix }}_total_akhir" style="font-size:26px;font-weight:900;color:#fff;">
          {{ number_format($nilaiAkhirX, 2) }}
        </span>
      </td>
      <td colspan="3"></td>
    </tr>

    </tbody>
  </table>
</div>