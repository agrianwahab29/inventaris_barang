@extends('layouts.app')

@section('title', 'Stok Opname Triwulan - Aplikasi Inventaris')

@section('page_title', 'Stok Opname Triwulan')
@section('breadcrumb')
    <li class="breadcrumb-item active">Stok Opname Triwulan</li>
@endsection

@section('styles')
<style>
    .quarter-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        color: white;
    }
    
    .quarter-selector {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .quarter-btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: 2px solid rgba(255,255,255,0.3);
        background: transparent;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .quarter-btn:hover {
        background: rgba(255,255,255,0.1);
        border-color: white;
    }
    
    .quarter-btn.active {
        background: white;
        color: #667eea;
        border-color: white;
    }
    
    .stat-box {
        border-radius: 10px;
        padding: 16px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .stat-box::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    
    .stat-total { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .stat-positive { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-zero { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    
    .modal-content {
        border-radius: 12px;
        border: none;
    }
    
    .period-label {
        font-size: 0.875rem;
        opacity: 0.9;
    }
    
    @media (max-width: 767.98px) {
        .stat-box {
            padding: 12px;
        }
        .stat-box div[style*="font-size: 1.5rem"] {
            font-size: 1.25rem !important;
        }
        .quarter-btn {
            padding: 8px 14px;
            font-size: 0.8125rem;
        }
        .filter-card .card-body {
            padding: 1rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .stat-box div[style*="font-size: 1.5rem"] {
            font-size: 1.1rem !important;
        }
        .stat-box div[style*="font-size: 1.75rem"] {
            font-size: 1.25rem !important;
        }
        .quarter-selector {
            gap: 6px;
        }
        .quarter-btn {
            padding: 6px 12px;
            font-size: 0.75rem;
        }
        .filter-card .card-body {
            padding: 0.75rem;
        }
    }
</style>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3 col-12 mb-3 mb-md-0">
                        <label class="form-label text-white mb-1" style="font-size: 0.75rem; opacity: 0.8;">Tahun</label>
                        <select name="tahun" id="filterTahun" class="form-select" style="background: rgba(255,255,255,0.9);">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ $selectedTahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-9 col-12">
                        <label class="form-label text-white mb-2" style="font-size: 0.75rem; opacity: 0.8;">Pilih Triwulan</label>
                        <div class="quarter-selector">
                            @foreach($quarters as $q => $label)
                                <a href="{{ route('quarterly-stock.index', ['tahun' => $selectedTahun, 'quarter' => $q]) }}" 
                                   class="quarter-btn {{ $selectedQuarter == $q ? 'active' : '' }}">
                                    {{ $q }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.2);">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <span class="text-white" style="font-size: 0.875rem; font-weight: 600;">
                                <i class="fas fa-calendar-alt me-2"></i>{{ $periodLabel }}
                            </span>
                            @if($actualStart && $actualEnd)
                            <div class="period-label mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                Data transaksi: {{ Carbon\Carbon::parse($actualStart)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($actualEnd)->format('d/m/Y') }}
                            </div>
                            @else
                            <div class="period-label mt-1" style="color: #fbbf24;">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Tidak ada data transaksi pada periode ini
                            </div>
                            @endif
                        </div>
                        <div>
                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal" @if(!$actualStart || !$actualEnd) disabled @endif>
                                <i class="fas fa-file-word me-1"></i>Export DOCX
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Statistics --}}
<div class="row g-2 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-box stat-total">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div style="font-size: 0.6875rem; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">Total Barang</div>
                    <div style="font-size: 1.5rem; font-weight: 700;">{{ $barangData->count() }}</div>
                </div>
                <i class="fas fa-boxes" style="font-size: 1.75rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-box stat-positive">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div style="font-size: 0.6875rem; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">Stok Tersedia</div>
                    <div style="font-size: 1.5rem; font-weight: 700;">{{ $barangData->where('stok_opname', '>', 0)->count() }}</div>
                </div>
                <i class="fas fa-check-circle" style="font-size: 1.75rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-box stat-zero">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div style="font-size: 0.6875rem; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">Stok Habis</div>
                    <div style="font-size: 1.5rem; font-weight: 700;">{{ $barangData->where('stok_opname', '<=', 0)->count() }}</div>
                </div>
                <i class="fas fa-times-circle" style="font-size: 1.75rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
</div>

{{-- Data Table --}}
<div class="row">
    <div class="col-12">
        <div class="card quarter-card">
            <div class="card-header bg-white py-3" style="border-bottom: 1px solid #e2e8f0;">
                <h5 class="mb-0" style="font-size: 0.9375rem; font-weight: 600;">
                    <i class="fas fa-clipboard-list me-2" style="color: #4f46e5;"></i>
                    Data Stok Opname - {{ $selectedQuarter }} {{ $selectedTahun }}
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="min-width: 600px;">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 60px;" class="text-center">No</th>
                                <th>Nama Barang</th>
                                <th style="width: 120px;" class="text-center">Satuan</th>
                                <th style="width: 150px;" class="text-center">JUMLAH STOK TERCATAT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach($barangData as $item)
                                @if($item->stok_opname > 0)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td class="text-center">{{ $item->satuan }}</td>
                                    <td class="text-center">
                                        <span style="font-weight: 600;">{{ number_format($item->stok_opname) }}</span>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                            @if($barangData->where('stok_opname', '>', 0)->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="fas fa-inbox" style="font-size: 2rem; color: #cbd5e1;"></i>
                                        <p class="text-muted mt-2 mb-0">Tidak ada data transaksi pada periode ini</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Export Modal --}}
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('quarterly-stock.export') }}" method="POST" id="exportForm">
                @csrf
                <input type="hidden" name="tahun" value="{{ $selectedTahun }}">
                <input type="hidden" name="quarter" value="{{ $selectedQuarter }}">
                
                <div class="modal-header" style="border-bottom: 1px solid #e2e8f0;">
                    <h5 class="modal-title" style="font-size: 1rem; font-weight: 600;">
                        <i class="fas fa-file-word me-2" style="color: #2563eb;"></i>
                        Export Laporan DOCX
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert" style="background: #e0f2fe; color: #0369a1; border-radius: 8px; padding: 12px; margin-bottom: 16px;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Periode:</strong> {{ $periodLabel }}
                    </div>
                    
                    <h6 class="mb-3" style="font-weight: 600; color: #4f46e5;">Mengetahui:</h6>
                    <div class="row mb-3">
                        <div class="col-12 mb-2">
                            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="mengetahui_jabatan" class="form-control" required 
                                   placeholder="Contoh: Kepala Bagian Umum">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="mengetahui_nama" class="form-control" required 
                                   placeholder="Nama lengkap">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">NIP <span class="text-danger">*</span></label>
                            <input type="text" name="mengetahui_nip" class="form-control" required 
                                   placeholder="NIP">
                        </div>
                    </div>
                    
                    <hr style="border-color: #e2e8f0; margin: 16px 0;">
                    
                    <h6 class="mb-3" style="font-weight: 600; color: #4f46e5;">Penyusun:</h6>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="penyusun_jabatan" class="form-control" 
                                   placeholder="Contoh: Staff Administrasi">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Nama</label>
                            <input type="text" name="penyusun_nama" class="form-control" 
                                   placeholder="Nama lengkap">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">NIP</label>
                            <input type="text" name="penyusun_nip" class="form-control" 
                                   placeholder="NIP">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i>Download DOCX
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('filterTahun').addEventListener('change', function() {
        const tahun = this.value;
        const quarter = '{{ $selectedQuarter }}';
        window.location.href = '{{ route('quarterly-stock.index') }}?tahun=' + tahun + '&quarter=' + quarter;
    });
</script>
@endsection
