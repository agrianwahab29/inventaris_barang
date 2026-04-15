@extends('layouts.app')

@section('title', 'Dashboard - Aplikasi Inventaris')

@section('page_title', 'Dashboard')

@section('styles')
<style>
    /* Keyframe Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    @keyframes shimmer {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }
    
    @keyframes countUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes gradientFlow {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }

    /* 5-column grid for stat cards */
    @media (min-width: 1200px) {
        .col-xl-5ths {
            flex: 0 0 20%;
            max-width: 20%;
        }
    }

    /* Equal height row for chart and quick actions */
    .equal-height-row {
        display: flex;
        flex-wrap: wrap;
    }
    .equal-height-row > [class*="col-"] {
        display: flex;
        flex-direction: column;
    }
    .equal-height-row .card-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .animate-fade-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    .animate-fade-left {
        animation: fadeInLeft 0.6s ease-out forwards;
    }
    
    .animate-fade-right {
        animation: fadeInRight 0.6s ease-out forwards;
    }
    
    .animate-scale {
        animation: scaleIn 0.5s ease-out forwards;
    }
    
    .animate-delay-1 { animation-delay: 0.1s; opacity: 0; }
    .animate-delay-2 { animation-delay: 0.2s; opacity: 0; }
    .animate-delay-3 { animation-delay: 0.3s; opacity: 0; }
    .animate-delay-4 { animation-delay: 0.4s; opacity: 0; }
    .animate-delay-5 { animation-delay: 0.5s; opacity: 0; }
    .animate-delay-6 { animation-delay: 0.6s; opacity: 0; }

    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, #1e4d8c 0%, #3b82f6 50%, #60a5fa 100%);
        background-size: 200% 200%;
        animation: gradientFlow 8s ease infinite;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
    }
    
    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }
    
    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -30%;
        width: 80%;
        height: 80%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        animation: float 8s ease-in-out infinite reverse;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        height: 100%;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.15);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--card-gradient);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }
    
    .stat-card:hover::before {
        transform: scaleX(1);
    }
    
    .stat-card-primary {
        --card-gradient: linear-gradient(90deg, #1e4d8c, #3b82f6);
    }
    
    .stat-card-success {
        --card-gradient: linear-gradient(90deg, #10b981, #059669);
    }
    
    .stat-card-warning {
        --card-gradient: linear-gradient(90deg, #f59e0b, #d97706);
    }
    
    .stat-card-danger {
        --card-gradient: linear-gradient(90deg, #ef4444, #dc2626);
        animation: attentionPulse 2s ease-in-out infinite;
    }
    
    @keyframes attentionPulse {
        0%, 100% {
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1);
        }
        50% {
            box-shadow: 0 8px 25px -5px rgba(239, 68, 68, 0.4);
        }
    }
    
    .stat-card-danger:hover {
        animation: none;
        box-shadow: 0 20px 40px -15px rgba(239, 68, 68, 0.3);
    }
    
    .stat-card-danger .stat-icon-danger {
        animation: iconPulse 2s ease-in-out infinite;
    }
    
    @keyframes iconPulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    .stat-card-danger:hover .stat-icon-danger {
        animation: none;
        transform: scale(1.1) rotate(-5deg);
    }
    
    .stat-card-info {
        --card-gradient: linear-gradient(90deg, #3b82f6, #2563eb);
    }
    
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
        position: relative;
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(-5deg);
    }
    
    .stat-icon-primary {
        background: linear-gradient(135deg, #1e4d8c 0%, #3b82f6 100%);
        color: white;
        box-shadow: 0 8px 16px -8px rgba(30, 77, 140, 0.5);
    }
    
    .stat-icon-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 8px 16px -8px rgba(16, 185, 129, 0.5);
    }
    
    .stat-icon-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 8px 16px -8px rgba(245, 158, 11, 0.5);
    }
    
    .stat-icon-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 8px 16px -8px rgba(239, 68, 68, 0.5);
    }
    
    .stat-icon-info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 8px 16px -8px rgba(59, 130, 246, 0.5);
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 4px;
        background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stat-label {
        font-size: 0.8125rem;
        color: #64748b;
        font-weight: 500;
        letter-spacing: 0.025em;
    }
    
    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.6875rem;
        font-weight: 600;
        margin-top: 12px;
        animation: pulse 2s ease-in-out infinite;
    }
    
    .stat-badge-primary {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e4d8c;
    }
    
    .stat-badge-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #059669;
    }
    
    .stat-badge-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #d97706;
    }
    
    .stat-badge-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #dc2626;
    }

    /* Chart Container */
    .chart-container {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        position: relative;
        height: 100%;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title i {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    /* Quick Actions */
    .quick-action {
        display: flex;
        align-items: center;
        padding: 14px 16px;
        background: #f8fafc;
        border-radius: 12px;
        margin-bottom: 10px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        text-decoration: none;
        color: inherit;
        border: 1px solid transparent;
        position: relative;
        overflow: hidden;
    }
    
    .quick-action::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s ease;
    }
    
    .quick-action:hover::before {
        left: 100%;
    }
    
    .quick-action:hover {
        background: white;
        border-color: #e2e8f0;
        transform: translateX(8px);
        box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.1);
    }
    
    .quick-action-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 14px;
        font-size: 16px;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    
    .quick-action:hover .quick-action-icon {
        transform: scale(1.1) rotate(-5deg);
    }

    /* Alert Items */
    .alert-item {
        display: flex;
        align-items: center;
        padding: 12px 14px;
        border-radius: 10px;
        margin-bottom: 10px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .alert-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        border-radius: 4px 0 0 4px;
    }
    
    .alert-item:hover {
        transform: translateX(6px);
    }
    
    .alert-danger-item {
        background: linear-gradient(90deg, #fef2f2 0%, #fff 100%);
    }
    
    .alert-danger-item::before {
        background: linear-gradient(180deg, #ef4444, #dc2626);
    }
    
    .alert-warning-item {
        background: linear-gradient(90deg, #fffbeb 0%, #fff 100%);
    }
    
    .alert-warning-item::before {
        background: linear-gradient(180deg, #f59e0b, #d97706);
    }

    /* Transaction Items */
    .transaction-item {
        display: flex;
        align-items: center;
        padding: 12px 14px;
        border-radius: 10px;
        margin-bottom: 8px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        background: #f8fafc;
        border: 1px solid transparent;
    }
    
    .transaction-item:hover {
        background: white;
        border-color: #e2e8f0;
        transform: translateX(6px);
        box-shadow: 0 2px 8px -2px rgba(0, 0, 0, 0.1);
    }
    
    .transaction-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 14px;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    
    .transaction-item:hover .transaction-icon {
        transform: scale(1.1);
    }
    
    .transaction-masuk {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #059669;
    }
    
    .transaction-keluar {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #d97706;
    }

    /* Scrollbar */
    .custom-scroll::-webkit-scrollbar {
        width: 6px;
    }
    
    .custom-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    
    .custom-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    
    .custom-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 24px;
        text-align: center;
    }
    
    .empty-state-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        animation: pulse 2s ease-in-out infinite;
    }
    
    /* Chart Empty State */
    .chart-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 240px;
        text-align: center;
        padding: 32px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
        border: 2px dashed #e2e8f0;
    }
    
    .chart-empty-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        animation: float 3s ease-in-out infinite;
    }
    
    .chart-empty-icon i {
        font-size: 1.75rem;
        color: #1e4d8c;
    }
    
    .chart-empty-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }
    
    .chart-empty-desc {
        font-size: 0.8125rem;
        color: #64748b;
        margin-bottom: 16px;
        max-width: 320px;
        line-height: 1.5;
    }

    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .chart-empty-state {
            min-height: 200px;
            padding: 24px;
        }
        
        .chart-empty-icon {
            width: 56px;
            height: 56px;
        }
        
        .chart-empty-icon i {
            font-size: 1.5rem;
        }
        
        .chart-empty-title {
            font-size: 0.9375rem;
        }
        
        .chart-empty-desc {
            font-size: 0.75rem;
            max-width: 280px;
        }
        
        .stat-value {
            font-size: 1.5rem;
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            font-size: 20px;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        
        .stat-card {
            padding: 14px;
        }
        
        .welcome-banner {
            padding: 16px !important;
        }
        
        .chart-container {
            padding: 14px;
        }
        
        .section-header {
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .quick-action {
            padding: 10px 12px;
        }
        
        .quick-action-icon {
            width: 36px;
            height: 36px;
            font-size: 14px;
            margin-right: 10px;
        }
        
        .alert-item {
            padding: 10px 12px;
        }
        
        .transaction-item {
            padding: 10px 12px;
        }
    }
    
    @media (max-width: 575.98px) {
        .stat-card {
            padding: 12px;
        }
        
        .stat-value {
            font-size: 1.25rem;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
            margin-bottom: 8px;
        }
        
        .stat-badge {
            font-size: 0.625rem;
            padding: 4px 8px;
            margin-top: 8px;
        }
        
        .stat-label {
            font-size: 0.75rem;
        }
        
        .chart-container {
            height: auto !important;
            min-height: 260px;
        }
        
        .section-title {
            font-size: 0.875rem;
        }
        
        .section-title i {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }
        
        .welcome-banner .btn-lg {
            padding: 8px 16px;
            font-size: 0.8125rem;
        }
        
        .quick-action h6 {
            font-size: 0.8125rem !important;
        }
        
        .quick-action small {
            font-size: 0.6875rem !important;
        }
        
        .alert-item h6 {
            font-size: 0.75rem !important;
        }
        
        .transaction-item h6 {
            font-size: 0.8125rem !important;
        }
        
        .custom-scroll {
            max-height: 240px !important;
        }
    }
    
    @media (max-width: 374.98px) {
        .stat-card {
            padding: 10px;
        }
        
        .stat-value {
            font-size: 1.125rem;
        }
        
        .stat-icon {
            width: 36px;
            height: 36px;
            font-size: 14px;
        }
        
        .chart-container {
            min-height: 220px;
        }
    }
</style>
@endsection

@section('content')
<!-- Welcome Banner -->
<div class="welcome-banner mb-4 animate-fade-up">
    <div class="card-body p-4 position-relative">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="text-white mb-2 fw-bold">
                    <i class="fas fa-hand-sparkles me-2"></i>
                    Selamat Datang, {{ Auth::user()->name }}!
                </h4>
                <p class="text-white-50 mb-0" style="font-size: 0.9375rem;">
                    Kelola inventaris barang dengan mudah dan efisien. Semua yang Anda butuhkan ada di sini.
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('transaksi.create') }}" class="btn btn-light btn-lg rounded-pill px-4 py-2 fw-semibold" style="font-size: 0.875rem;">
                    <i class="fas fa-plus-circle me-2"></i>Barang Masuk/Keluar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-5ths col-md-6 col-6">
        <div class="stat-card stat-card-primary animate-fade-up animate-delay-1">
            <div class="stat-icon stat-icon-primary">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-value" data-count="{{ $totalBarang }}">{{ number_format($totalBarang) }}</div>
            <div class="stat-label">Total Jenis Barang</div>
            <div class="stat-badge stat-badge-primary">
                <i class="fas fa-cube"></i>
                <span>{{ number_format($totalStok) }} total stok</span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-5ths col-md-6 col-6">
        <div class="stat-card stat-card-success animate-fade-up animate-delay-2">
            <div class="stat-icon stat-icon-success">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-value">{{ number_format($dataMasuk[6] ?? 0) }}</div>
            <div class="stat-label">Barang Masuk Hari Ini</div>
            <div class="stat-badge stat-badge-success">
                <i class="fas fa-calendar-day"></i>
                <span>{{ now()->format('d M Y') }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-5ths col-md-6 col-6">
        <div class="stat-card stat-card-warning animate-fade-up animate-delay-3">
            <div class="stat-icon stat-icon-warning">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-value">{{ number_format($dataKeluar[6] ?? 0) }}</div>
            <div class="stat-label">Barang Keluar Hari Ini</div>
            <div class="stat-badge stat-badge-warning">
                <i class="fas fa-exchange-alt"></i>
                <span>{{ $transaksiHariIni }} transaksi</span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-5ths col-md-6 col-6">
        <div class="stat-card stat-card-info animate-fade-up animate-delay-4">
            <div class="stat-icon stat-icon-info">
                <i class="fas fa-file-pdf"></i>
            </div>
            <div class="stat-value" data-count="{{ $totalBerkas }}">{{ number_format($totalBerkas) }}</div>
            <div class="stat-label">Total Berkas Transaksi</div>
            <div class="stat-badge" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e4d8c;">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ $berkasBulanIni }} bulan ini</span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-5ths col-md-6 col-6">
        @php
            $totalAlert = $stokRendah + $stokHabis;
        @endphp
        @if($totalAlert > 0)
        <div class="stat-card stat-card-danger animate-fade-up animate-delay-5" style="box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);">
            <div class="stat-icon stat-icon-danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-value">{{ number_format($totalAlert) }}</div>
            <div class="stat-label">Perlu Perhatian</div>
            <div class="stat-badge stat-badge-danger">
                @if($stokHabis > 0)<span class="badge bg-white text-danger me-1" style="font-size: 0.625rem;">{{ $stokHabis }} habis</span>@endif
                @if($stokRendah > 0)<span class="badge bg-white text-warning" style="font-size: 0.625rem;">{{ $stokRendah }} rendah</span>@endif
            </div>
        </div>
        @else
        <div class="stat-card animate-fade-up animate-delay-5" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac; box-shadow: 0 4px 15px rgba(34, 197, 94, 0.15);">
            <div class="stat-icon" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; box-shadow: 0 8px 16px -8px rgba(34, 197, 94, 0.5);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value" style="background: linear-gradient(135deg, #166534 0%, #15803d 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">0</div>
            <div class="stat-label">Stok Aman</div>
            <div class="stat-badge" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #166534;">
                <i class="fas fa-check me-1"></i>
                <span>Semua stok tersedia</span>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="row g-3 equal-height-row">
    <!-- Chart Section -->
    <div class="col-lg-8">
        <div class="chart-container animate-fade-left animate-delay-2 d-flex flex-column h-100">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-chart-line" style="background: linear-gradient(135deg, #1e4d8c 0%, #3b82f6 100%); color: white;"></i>
                    Grafik Transaksi 7 Hari Terakhir
                </h5>
                <a href="{{ route('transaksi.index') }}" class="btn btn-sm rounded-pill px-3" style="background: linear-gradient(135deg, #1e4d8c 0%, #3b82f6 100%); color: white; font-size: 0.75rem;">
                    <i class="fas fa-arrow-right me-1"></i>Detail
                </a>
            </div>
            <div class="flex-grow-1 position-relative" style="min-height: 250px;">
                @php
                    $hasChartData = !empty(array_filter($dataMasuk)) || !empty(array_filter($dataKeluar));
                @endphp
                
                @if($hasChartData)
                    <canvas id="transaksiChart" style="height: 100%; width: 100%;"></canvas>
                @else
                    <div class="chart-empty-state">
                        <div class="chart-empty-icon">
                            <i class="fas fa-chart-area"></i>
                        </div>
                        <h6 class="chart-empty-title">Belum Ada Transaksi</h6>
                        <p class="chart-empty-desc">Tidak ada transaksi dalam 7 hari terakhir. Data akan muncul setelah ada barang masuk atau keluar.</p>
                        <a href="{{ route('transaksi.create') }}" class="btn btn-sm rounded-pill" style="background: linear-gradient(135deg, #1e4d8c 0%, #3b82f6 100%); color: white; font-size: 0.75rem;">
                            <i class="fas fa-plus me-1"></i>Tambah Transaksi
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="chart-container animate-fade-right animate-delay-3 h-100">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-bolt" style="background: linear-gradient(135deg, #1e4d8c 0%, #3b82f6 100%); color: white;"></i>
                    Aksi Cepat
                </h5>
            </div>
            
            <a href="{{ route('transaksi.create') }}" class="quick-action">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e4d8c;">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;">Barang Masuk/Keluar</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Catat barang masuk atau keluar</small>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>
            
            <a href="{{ route('quarterly-stock.index') }}" class="quick-action">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;">Stok Opname Triwulan</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Lihat laporan stok per triwulan</small>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>
            
            <a href="{{ route('barang.index') }}" class="quick-action">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706;">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;">Kelola Barang</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Lihat dan edit data barang</small>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>
            
            <a href="{{ route('transaksi.index') }}" class="quick-action">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); color: #0369a1;">
                    <i class="fas fa-history"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;">Riwayat Transaksi</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Lihat semua riwayat</small>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>
            
            <a href="{{ route('berkas-transaksi.index') }}" class="quick-action">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626;">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;">Berkas Transaksi</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Kelola arsip dokumen digital</small>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>
            
            @if(Auth::user()->isAdmin())
            <a href="{{ route('users.index') }}" class="quick-action">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e4d8c;">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;">Manajemen User</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Kelola pengguna sistem</small>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>
            @endif
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <!-- Stok Alerts -->
    <div class="col-lg-4">
        <div class="chart-container animate-fade-up animate-delay-4">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-bell" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;"></i>
                    Peringatan Stok
                </h5>
                <a href="{{ route('barang.index', ['status' => 'rendah']) }}" class="btn btn-sm btn-outline-danger rounded-pill" style="font-size: 0.6875rem;">
                    Semua
                </a>
            </div>
            
            <div style="max-height: 320px; overflow-y: auto;" class="custom-scroll">
                @forelse($barangStokRendah as $barang)
                    <a href="{{ route('barang.show', $barang->id) }}" class="alert-item {{ $barang->isStokHabis() ? 'alert-danger-item' : 'alert-warning-item' }} text-decoration-none">
                        <div class="flex-grow-1" style="min-width: 0;">
                            <h6 class="mb-0 fw-semibold text-truncate text-dark" style="font-size: 0.8125rem;">{{ $barang->nama_barang }}</h6>
                            <small class="text-muted" style="font-size: 0.6875rem;">{{ $barang->kategori }}</small>
                        </div>
                        <span class="badge {{ $barang->isStokHabis() ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill ms-2" style="font-size: 0.625rem;">
                            {{ $barang->stok }} {{ $barang->satuan }}
                        </span>
                    </a>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon" style="background: #d1fae5;">
                            <i class="fas fa-check-circle text-success" style="font-size: 1.5rem;"></i>
                        </div>
                        <h6 class="text-muted fw-semibold" style="font-size: 0.875rem;">Semua Stok Aman</h6>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Tidak ada barang dengan stok rendah</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Recent Transactions -->
    <div class="col-lg-8">
        <div class="chart-container animate-fade-up animate-delay-5">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-clock" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;"></i>
                    Transaksi Terakhir
                </h5>
                <a href="{{ route('transaksi.index') }}" class="btn btn-sm rounded-pill px-3" style="background: linear-gradient(135deg, #1e4d8c 0%, #3b82f6 100%); color: white; font-size: 0.75rem;">
                    <i class="fas fa-arrow-right me-1"></i>Semua
                </a>
            </div>
            
            <div style="max-height: 320px; overflow-y: auto;" class="custom-scroll">
                @forelse($transaksiTerakhir as $transaksi)
                    <a href="{{ route('transaksi.show', $transaksi->id) }}" class="transaction-item text-decoration-none">
                        <div class="transaction-icon {{ $transaksi->tipe == 'masuk' ? 'transaction-masuk' : 'transaction-keluar' }}">
                            <i class="fas fa-arrow-{{ $transaksi->tipe == 'masuk' ? 'down' : 'up' }}"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width: 0; overflow: hidden;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div style="min-width: 0; overflow: hidden;">
                                    <h6 class="mb-0 fw-semibold text-truncate text-dark" style="font-size: 0.875rem;">{{ $transaksi->barang->nama_barang }}</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="fas fa-user me-1"></i>{{ $transaksi->user->name }}
                                        @if($transaksi->tipe == 'keluar' && $transaksi->ruangan)
                                            <span class="mx-1">•</span>
                                            <i class="fas fa-door-open me-1"></i>{{ $transaksi->ruangan->nama_ruangan }}
                                        @endif
                                    </small>
                                </div>
                                <div class="text-end" style="white-space: nowrap;">
                                    <span class="badge {{ $transaksi->tipe == 'masuk' ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill mb-1" style="font-size: 0.6875rem;">
                                        {{ $transaksi->tipe == 'masuk' ? '+' : '-' }}{{ $transaksi->jumlah }} {{ $transaksi->barang->satuan }}
                                    </span>
                                    <div class="small text-muted" style="font-size: 0.6875rem;">{{ $transaksi->tanggal->format('d M') }}</div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon" style="background: #f1f5f9;">
                            <i class="fas fa-inbox text-muted" style="font-size: 1.5rem;"></i>
                        </div>
                        <h6 class="text-muted fw-semibold" style="font-size: 0.875rem;">Belum Ada Transaksi</h6>
                        <a href="{{ route('transaksi.create') }}" class="btn rounded-pill mt-2" style="background: linear-gradient(135deg, #1e4d8c 0%, #3b82f6 100%); color: white; font-size: 0.75rem;">
                            <i class="fas fa-plus me-1"></i>Barang Masuk/Keluar
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set active menu state for Dashboard
        const dashboardMenu = document.querySelector('a[href="{{ route('dashboard') }}"]');
        if (dashboardMenu) {
            dashboardMenu.classList.add('active');
            // Add active styling if parent is a list item
            const parentLi = dashboardMenu.closest('li');
            if (parentLi) {
                parentLi.classList.add('active');
            }
        }
        
        // Check if chart has data
        const dataMasuk = @json($dataMasuk);
        const dataKeluar = @json($dataKeluar);
        const hasChartData = dataMasuk.some(v => v > 0) || dataKeluar.some(v => v > 0);
        
        if (hasChartData) {
            // Chart
            const ctx = document.getElementById('transaksiChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($tanggalLabels),
                datasets: [{
                    label: 'Barang Masuk',
                    data: @json($dataMasuk),
                    backgroundColor: 'rgba(16, 185, 129, 0.25)',
                    borderColor: '#10b981',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#10b981',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }, {
                    label: 'Barang Keluar',
                    data: @json($dataKeluar),
                    backgroundColor: 'rgba(245, 158, 11, 0.25)',
                    borderColor: '#f59e0b',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#f59e0b',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 12,
                                family: 'Inter',
                                weight: '600'
                            },
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 41, 59, 0.95)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        titleFont: {
                            size: 13,
                            family: 'Inter',
                            weight: '600'
                        },
                        bodyFont: {
                            size: 12,
                            family: 'Inter'
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                family: 'Inter',
                                size: 11
                            },
                            padding: 10
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                family: 'Inter',
                                size: 11
                            },
                            padding: 10
                        }
                    }
                }
            }
        });
        }
    });
</script>
@endsection
