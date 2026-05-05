@php
    $daysList = [
        'Senin'  => 'Senin',
        'Selasa' => 'Selasa',
        'Rabu'   => 'Rabu',
        'Kamis'  => 'Kamis',
        'Jumat'  => 'Jumat',
        'Sabtu'  => 'Sabtu',
        'Minggu' => 'Minggu',
    ];
    $selectedDays = old('hari', []);
    $overrideEnabled = old('override_enabled', []);
    $overrideMulai = old('override_jam_mulai', []);
    $overrideSelesai = old('override_jam_selesai', []);
@endphp

<!-- Ruangan Field -->
<div class="form-group col-sm-6">
    {!! Form::label('gedung_fasilitas_id', 'Ruangan / Fasilitas:') !!}
    {!! Form::select('gedung_fasilitas_id', $fasilitas, null, ['class' => 'form-control custom-select', 'placeholder' => 'Pilih Ruangan']) !!}
</div>

<!-- Nama Kegiatan Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nama_kegiatan', 'Nama Kegiatan / Kuliah:') !!}
    {!! Form::text('nama_kegiatan', null, ['class' => 'form-control', 'maxlength' => 255, 'placeholder' => 'Contoh: Perkuliahan, Rapat, Praktikum']) !!}
</div>

<!-- Default Jam -->
<div class="form-group col-12">
    <div class="callout callout-info mb-0" style="background: #eaf6ff; border-left: 4px solid #17a2b8;">
        <h6 class="mb-2"><i class="fas fa-clock mr-1"></i> Jam Default</h6>
        <small class="text-muted d-block mb-2">Jam ini akan dipakai untuk semua hari yang dipilih, kecuali kamu set override khusus per hari.</small>
        <div class="row">
            <div class="col-sm-6">
                {!! Form::label('jam_mulai', 'Jam Mulai Default:', ['class' => 'font-weight-bold']) !!}
                {!! Form::input('time', 'jam_mulai', old('jam_mulai', '07:30'), ['class' => 'form-control', 'id' => 'default_jam_mulai']) !!}
            </div>
            <div class="col-sm-6">
                {!! Form::label('jam_selesai', 'Jam Selesai Default:', ['class' => 'font-weight-bold']) !!}
                {!! Form::input('time', 'jam_selesai', old('jam_selesai', '16:00'), ['class' => 'form-control', 'id' => 'default_jam_selesai']) !!}
            </div>
        </div>
    </div>
</div>

<!-- Preset Buttons -->
<div class="form-group col-12">
    {!! Form::label(null, 'Preset Cepat:', ['class' => 'font-weight-bold']) !!}
    <div class="btn-group-toggle d-flex flex-wrap" style="gap: 8px;">
        <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-preset="weekday">
            <i class="fas fa-briefcase mr-1"></i> Senin - Jumat
        </button>
        <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-preset="weekend">
            <i class="fas fa-coffee mr-1"></i> Weekend (Sab-Min)
        </button>
        <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-preset="all">
            <i class="fas fa-calendar-check mr-1"></i> Setiap Hari
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm preset-btn" data-preset="clear">
            <i class="fas fa-times mr-1"></i> Kosongkan
        </button>
    </div>
</div>

<!-- Hari + Override per hari -->
<div class="form-group col-12">
    {!! Form::label(null, 'Pilih Hari Aktif:', ['class' => 'font-weight-bold']) !!}
    <small class="text-muted d-block mb-2">Centang hari yang ingin dijadwalkan. Kamu bisa set jam berbeda untuk hari tertentu dengan klik tombol "Override".</small>

    <div id="daysContainer" class="border rounded p-2" style="background: #f8f9fa;">
        @foreach($daysList as $key => $label)
            @php
                $isChecked = in_array($key, (array) $selectedDays);
                $hasOverride = !empty($overrideEnabled[$key]);
            @endphp
            <div class="day-row border rounded mb-2 p-2 bg-white">
                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input day-check"
                               id="hari_{{ $key }}" name="hari[]" value="{{ $key }}"
                               data-day="{{ $key }}" {{ $isChecked ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold" for="hari_{{ $key }}">
                            {{ $label }}
                        </label>
                    </div>
                    <span class="day-time-label text-muted small flex-grow-1" id="label_{{ $key }}">
                        <i class="far fa-clock"></i>
                        <span class="time-display">Pakai default</span>
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-secondary toggle-override-btn"
                            data-day="{{ $key }}" {{ $isChecked ? '' : 'disabled' }}>
                        <i class="fas fa-cog"></i>
                        <span class="btn-text">{{ $hasOverride ? 'Hapus Override' : 'Override Jam' }}</span>
                    </button>
                </div>

                <div class="override-panel mt-2" data-day="{{ $key }}" style="display: {{ $hasOverride ? 'block' : 'none' }};">
                    <input type="hidden" name="override_enabled[{{ $key }}]" value="{{ $hasOverride ? '1' : '0' }}"
                           class="override-flag" data-day="{{ $key }}">
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="small mb-1">Jam Mulai ({{ $label }}):</label>
                            <input type="time" name="override_jam_mulai[{{ $key }}]"
                                   value="{{ $overrideMulai[$key] ?? '' }}"
                                   class="form-control form-control-sm override-mulai"
                                   data-day="{{ $key }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="small mb-1">Jam Selesai ({{ $label }}):</label>
                            <input type="time" name="override_jam_selesai[{{ $key }}]"
                                   value="{{ $overrideSelesai[$key] ?? '' }}"
                                   class="form-control form-control-sm override-selesai"
                                   data-day="{{ $key }}">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Keterangan -->
<div class="form-group col-sm-12">
    {!! Form::label('keterangan', 'Keterangan (Opsional):') !!}
    {!! Form::textarea('keterangan', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Catatan tambahan, mis. dosen pengampu, kelompok, dll']) !!}
</div>

<!-- Summary -->
<div class="form-group col-12">
    <div id="summaryBox" class="alert alert-light border" style="display: none;">
        <strong><i class="fas fa-info-circle mr-1"></i> Ringkasan:</strong>
        <span id="summaryText">Belum ada hari dipilih</span>
    </div>
</div>

@push('page_scripts')
<script>
(function() {
    const dayLabels = {
        Senin: 'Senin', Selasa: 'Selasa', Rabu: 'Rabu',
        Kamis: 'Kamis', Jumat: 'Jumat', Sabtu: 'Sabtu', Minggu: 'Minggu'
    };
    const presets = {
        weekday: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
        weekend: ['Sabtu', 'Minggu'],
        all: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
        clear: []
    };

    function $$(sel, ctx) { return Array.from((ctx || document).querySelectorAll(sel)); }

    function updateDayLabel(day) {
        const checkbox = document.getElementById('hari_' + day);
        const labelSpan = document.querySelector('#label_' + day + ' .time-display');
        const flag = document.querySelector('.override-flag[data-day="' + day + '"]');
        const mulai = document.querySelector('.override-mulai[data-day="' + day + '"]');
        const selesai = document.querySelector('.override-selesai[data-day="' + day + '"]');
        const toggleBtn = document.querySelector('.toggle-override-btn[data-day="' + day + '"]');
        const btnText = toggleBtn.querySelector('.btn-text');

        if (!checkbox.checked) {
            labelSpan.textContent = 'Tidak aktif';
            labelSpan.parentElement.classList.add('text-muted');
            toggleBtn.disabled = true;
            return;
        }

        toggleBtn.disabled = false;

        if (flag.value === '1' && mulai.value && selesai.value) {
            labelSpan.innerHTML = '<span class="badge badge-warning">Custom</span> ' + mulai.value + ' - ' + selesai.value;
            btnText.textContent = 'Hapus Override';
        } else {
            const defMulai = document.getElementById('default_jam_mulai').value || '--:--';
            const defSelesai = document.getElementById('default_jam_selesai').value || '--:--';
            labelSpan.innerHTML = '<span class="badge badge-secondary">Default</span> ' + defMulai + ' - ' + defSelesai;
            btnText.textContent = 'Override Jam';
        }
    }

    function updateSummary() {
        const checked = $$('.day-check:checked');
        const summaryBox = document.getElementById('summaryBox');
        const summaryText = document.getElementById('summaryText');

        if (checked.length === 0) {
            summaryBox.style.display = 'none';
            return;
        }

        summaryBox.style.display = 'block';
        const names = checked.map(c => dayLabels[c.dataset.day]);
        summaryText.textContent = 'Akan dibuat ' + checked.length + ' jadwal untuk hari: ' + names.join(', ');
    }

    function refreshAll() {
        Object.keys(dayLabels).forEach(updateDayLabel);
        updateSummary();
    }

    // Preset buttons
    $$('.preset-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const preset = this.dataset.preset;
            const days = presets[preset];
            $$('.day-check').forEach(cb => {
                cb.checked = days.includes(cb.dataset.day);
            });
            refreshAll();
        });
    });

    // Day checkbox change
    $$('.day-check').forEach(cb => {
        cb.addEventListener('change', function() {
            updateDayLabel(this.dataset.day);
            updateSummary();
        });
    });

    // Toggle override panel
    $$('.toggle-override-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const day = this.dataset.day;
            const panel = document.querySelector('.override-panel[data-day="' + day + '"]');
            const flag = document.querySelector('.override-flag[data-day="' + day + '"]');

            if (flag.value === '1') {
                // Hapus override
                flag.value = '0';
                panel.style.display = 'none';
                document.querySelector('.override-mulai[data-day="' + day + '"]').value = '';
                document.querySelector('.override-selesai[data-day="' + day + '"]').value = '';
            } else {
                // Aktifkan override — prefill dengan default supaya user tinggal ubah
                flag.value = '1';
                panel.style.display = 'block';
                const defMulai = document.getElementById('default_jam_mulai').value;
                const defSelesai = document.getElementById('default_jam_selesai').value;
                const mulaiInput = document.querySelector('.override-mulai[data-day="' + day + '"]');
                const selesaiInput = document.querySelector('.override-selesai[data-day="' + day + '"]');
                if (!mulaiInput.value) mulaiInput.value = defMulai;
                if (!selesaiInput.value) selesaiInput.value = defSelesai;
            }
            updateDayLabel(day);
        });
    });

    // Override time change
    $$('.override-mulai, .override-selesai').forEach(inp => {
        inp.addEventListener('change', function() {
            updateDayLabel(this.dataset.day);
        });
    });

    // Default time change
    ['default_jam_mulai', 'default_jam_selesai'].forEach(id => {
        document.getElementById(id).addEventListener('change', refreshAll);
    });

    // Initial render
    refreshAll();
})();
</script>
@endpush
