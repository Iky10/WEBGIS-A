@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>⚙️ Pengaturan Global</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')

        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Pilih Semester Aktif</h3>
                    </div>
                    {!! Form::open(['route' => 'semester_aktif.update']) !!}
                    <div class="card-body">
                        <div class="form-group">
                            <label>Semester yang ditampilkan di Publik:</label>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-primary {{ $semesterAktif == 'ganjil' ? 'active' : '' }}" style="border-radius: 8px 0 0 8px;">
                                    <input type="radio" name="semester_aktif" value="ganjil" autocomplete="off" {{ $semesterAktif == 'ganjil' ? 'checked' : '' }}> 
                                    <i class="fas fa-calendar-day mr-2"></i> Semester Ganjil (1, 3, 5, 7)
                                </label>
                                <label class="btn btn-outline-primary {{ $semesterAktif == 'genap' ? 'active' : '' }}" style="border-radius: 0 8px 8px 0;">
                                    <input type="radio" name="semester_aktif" value="genap" autocomplete="off" {{ $semesterAktif == 'genap' ? 'checked' : '' }}> 
                                    <i class="fas fa-calendar-check mr-2"></i> Semester Genap (2, 4, 6, 8)
                                </label>
                            </div>
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle"></i> Hanya jadwal pada semester yang dipilih ini yang akan ditampilkan pada Detail Gedung di halaman publik peta.
                            </small>
                        </div>
                        
                        <hr>

                        <div class="form-group">
                            <label for="tahun_ajaran_aktif">Tahun Ajaran Aktif:</label>
                            {!! Form::select('tahun_ajaran_aktif', [
                                '2023/2024' => '2023/2024',
                                '2024/2025' => '2024/2025',
                                '2025/2026' => '2025/2026',
                                '2026/2027' => '2026/2027',
                                '2027/2028' => '2027/2028'
                            ], $tahunAjaranAktif, ['class' => 'form-control', 'id' => 'tahun_ajaran_aktif']) !!}
                            <small class="form-text text-muted">Pilih tahun ajaran yang saat ini sedang berlangsung.</small>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Pengaturan
                        </button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-header border-0">
                        <h3 class="card-title text-muted"><i class="fas fa-eye"></i> Summary Tampilan Publik</h3>
                    </div>
                    <div class="card-body">
                        <div class="callout callout-info">
                            <h5>Saat ini publik melihat:</h5>
                            <p class="mb-0" style="font-size: 1.1rem;">
                                Jadwal Semester <strong>{{ strtoupper($semesterAktif) }}</strong> 
                                Tahun Ajaran <strong>{{ $tahunAjaranAktif }}</strong>
                            </p>
                        </div>
                        <p class="text-sm text-muted">
                            Saat pengunjung membuka detail gedung pada peta, mereka tidak perlu lagi memilih Ganjil/Genap secara manual. Sistem akan langsung menampilkan jadwal semester {{ strtoupper($semesterAktif) }}.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
