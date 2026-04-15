@extends('layouts.app')

@section('title', 'Berkas Transaksi - Arsip Dokumen')

@section('styles')
<style>
    /* Stats Cards - Modern Gradient Design */
    .stat-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--card-color) 0%, var(--card-color-light) 100%);
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .stat-card.total { --card-color: #6366f1; --card-color-light: #818cf8; }
    .stat-card.month { --card-color: #10b981; --card-color-light: #34d399; }
    .stat-card.size { --card-color: #0ea5e9; --card-color-light: #38bdf8; }
    .stat-card.users { --card-color: #f59e0b; --card-color-light: #fbbf24; }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: linear-gradient(135deg, var(--card-color) 0%, var(--card-color-light) 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }
    
    .stat-label {
        font-size: 0.8125rem;
        color: #64748b;
        font-weight: 500;
    }
    
    /* Filter Card */
    .filter-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 16px;
        overflow: hidden;
    }
    
    .filter-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    
    .filter-header h6 {
        font-weight: 600;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .filter-header i {
        color: #6366f1;
    }
    
    /* File Cards Grid Layout - NEW DESIGN */
    .files-container {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    
    .files-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    
    .files-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 1.25rem;
        padding: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .files-grid {
            grid-template-columns: 1fr;
            padding: 1rem;
            gap: 1rem;
        }
    }
    
    /* File Card */
    .file-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .file-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.12);
        border-color: #cbd5e1;
    }
    
    .file-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #f87171 0%, #ef4444 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .file-card:hover::before {
        opacity: 1;
    }
    
    .file-card.selected {
        border-color: #6366f1;
        background: #eef2ff;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    /* Card Header */
    .card-header-flex {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .card-checkbox {
        margin-top: 0.25rem;
    }
    
    .card-checkbox input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    .card-pdf-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #dc2626;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
    }
    
    .card-main-info {
        flex: 1;
        min-width: 0;
    }
    
    .card-filename {
        font-weight: 600;
        font-size: 0.9375rem;
        color: #1e293b;
        line-height: 1.4;
        margin-bottom: 0.375rem;
        word-break: break-word;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .card-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.75rem;
        color: #64748b;
    }
    
    .card-meta span {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    /* Card Details Grid */
    .card-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding: 0.875rem;
        background: #f8fafc;
        border-radius: 12px;
    }
    
    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .detail-label {
        font-size: 0.6875rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        font-weight: 600;
    }
    
    .detail-value {
        font-size: 0.8125rem;
        color: #334155;
        font-weight: 500;
    }
    
    .detail-value.muted {
        color: #94a3b8;
    }
    
    .detail-flow {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.8125rem;
        color: #334155;
    }
    
    .detail-flow .arrow {
        color: #cbd5e1;
        font-size: 0.625rem;
    }
    
    /* Card Footer */
    .card-footer-flex {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 0.875rem;
        border-top: 1px solid #f1f5f9;
    }
    
    .card-uploader {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .uploader-avatar {
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.6875rem;
        font-weight: 600;
    }
    
    .uploader-name {
        font-size: 0.75rem;
        color: #475569;
        font-weight: 500;
    }
    
    /* Card Actions */
    .card-actions {
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .action-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
    }
    
    .action-btn.view {
        background: #e0f2fe;
        color: #0284c7;
    }
    
    .action-btn.view:hover {
        background: #bae6fd;
        transform: scale(1.05);
    }
    
    .action-btn.download {
        background: #d1fae5;
        color: #059669;
    }
    
    .action-btn.download:hover {
        background: #a7f3d0;
        transform: scale(1.05);
    }
    
    .action-btn.edit {
        background: #e0e7ff;
        color: #6366f1;
    }
    
    .action-btn.edit:hover {
        background: #c7d2fe;
        transform: scale(1.05);
    }
    
    .action-btn.delete {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .action-btn.delete:hover {
        background: #fecaca;
        transform: scale(1.05);
    }
    
    /* Empty State - Modern */
    .empty-state-modern {
        padding: 4rem 2rem;
        text-align: center;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 20px;
        margin: 1.5rem;
    }
    
    .empty-icon-wrapper {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #fff 0%, #f1f5f9 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }
    
    .empty-icon-wrapper i {
        font-size: 2.5rem;
        color: #94a3b8;
    }
    
    .empty-state-modern h4 {
        color: #475569;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .empty-state-modern p {
        color: #94a3b8;
        margin-bottom: 1.5rem;
    }
    
    /* Bulk Action Bar */
    .bulk-action-bar {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border: 1px solid #c7d2fe;
        border-radius: 14px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    }
    
    /* Header Buttons */
    .btn-header {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border-radius: 10px;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .btn-header-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }
    
    .btn-header-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        color: white;
    }
    
    .btn-header-outline {
        background: #fff;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    
    .btn-header-outline:hover {
        background: #fef2f2;
        border-color: #fca5a5;
    }
    
    /* Pagination */
    .pagination-wrapper {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid rgba(0,0,0,0.06);
        background: #f8fafc;
    }
    
    /* Selected Count Badge */
    .selected-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #6366f1;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8125rem;
        font-weight: 500;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .files-grid {
            grid-template-columns: 1fr;
            padding: 1rem;
        }
        
        .card-details {
            grid-template-columns: 1fr;
        }
        
        .stat-card {
            margin-bottom: 1rem;
        }
    }
    
    .table-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    
    .table-responsive-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .custom-table {
        width: 100%;
        min-width: 1100px;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
    }
    
    .custom-table thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.75rem 0.5rem;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .custom-table tbody tr {
        transition: all 0.2s ease;
    }
    
    .custom-table tbody tr:hover {
        background: #f8fafc;
    }
    
    .custom-table tbody td {
        padding: 0.875rem 0.5rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.8125rem;
    }
    
    /* File Cell Styling */
    .file-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .file-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        color: #dc2626;
        flex-shrink: 0;
    }
    
    .file-info {
        min-width: 0;
        flex: 1;
        overflow: hidden;
    }
    
    .file-name {
        font-weight: 500;
        color: #1e293b;
        font-size: 0.8125rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 220px;
    }
    
    .file-date {
        font-size: 0.6875rem;
        color: #64748b;
    }
    
    /* View Toggle Buttons */
    .view-toggle {
        display: flex;
        background: #f1f5f9;
        border-radius: 10px;
        padding: 4px;
        gap: 4px;
    }
    
    .view-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 8px;
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 0.8125rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .view-btn:hover {
        background: rgba(255,255,255,0.5);
        color: #334155;
    }
    
    .view-btn.active {
        background: #fff;
        color: #6366f1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .view-btn i {
        font-size: 0.875rem;
    }
    
    /* List View Styles */
    .files-list {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    @media (max-width: 768px) {
        .files-list {
            padding: 1rem;
        }
    }
    
    .file-list-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    
    .file-list-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transform: translateX(4px);
    }
    
    .file-list-item.selected {
        border-color: #6366f1;
        background: #eef2ff;
    }
    
    .list-checkbox {
        flex-shrink: 0;
    }
    
    .list-checkbox input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    .list-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #dc2626;
        font-size: 1.125rem;
        flex-shrink: 0;
    }
    
    .list-content {
        flex: 1;
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    
    .list-main {
        flex: 1;
        min-width: 0;
    }
    
    .list-filename {
        font-weight: 600;
        font-size: 0.9375rem;
        color: #1e293b;
        margin-bottom: 0.375rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .list-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .list-meta-item {
        font-size: 0.75rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .list-meta-item i {
        font-size: 0.6875rem;
    }
    
    .list-sender-receiver {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
        color: #334155;
        padding: 0.375rem 0.75rem;
        background: #f8fafc;
        border-radius: 20px;
        white-space: nowrap;
    }
    
    .list-sender-receiver .sender {
        color: #059669;
        font-weight: 500;
    }
    
    .list-sender-receiver .receiver {
        color: #dc2626;
        font-weight: 500;
    }
    
    .list-sender-receiver i {
        color: #94a3b8;
        font-size: 0.625rem;
    }
    
    .list-uploader {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 0.75rem;
        background: #f1f5f9;
        border-radius: 20px;
        font-size: 0.75rem;
        color: #475569;
        font-weight: 500;
        white-space: nowrap;
        flex-shrink: 0;
    }
    
    .uploader-badge {
        width: 22px;
        height: 22px;
        background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.625rem;
        font-weight: 700;
    }
    
    .list-actions {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        flex-shrink: 0;
    }
    
    .action-btn-sm {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8125rem;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    
    .action-btn-sm.view {
        background: #e0f2fe;
        color: #0284c7;
    }
    
    .action-btn-sm.view:hover {
        background: #bae6fd;
    }
    
    .action-btn-sm.download {
        background: #d1fae5;
        color: #059669;
    }
    
    .action-btn-sm.download:hover {
        background: #a7f3d0;
    }
    
    .action-btn-sm.edit {
        background: #e0e7ff;
        color: #6366f1;
    }
    
    .action-btn-sm.edit:hover {
        background: #c7d2fe;
    }
    
    .action-btn-sm.delete {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .action-btn-sm.delete:hover {
        background: #fecaca;
    }
    
    /* Responsive List View */
    @media (max-width: 992px) {
        .list-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .list-sender-receiver {
            order: 2;
        }
    }
    
    @media (max-width: 768px) {
        .file-list-item {
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        
        .list-actions {
            width: 100%;
            justify-content: flex-end;
            margin-top: 0.5rem;
            padding-top: 0.75rem;
            border-top: 1px solid #f1f5f9;
        }
    }
    
    /* Action Dropdown */
    .action-dropdown .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }
    
    .action-dropdown .btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #334155;
    }
    
    .action-dropdown .dropdown-menu {
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        padding: 0.5rem;
        min-width: 160px;
    }
    
    .action-dropdown .dropdown-item {
        border-radius: 8px;
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 0.625rem;
        transition: all 0.2s ease;
    }
    
    .action-dropdown .dropdown-item:hover {
        background: #f8fafc;
    }
    
    .action-dropdown .dropdown-item i {
        font-size: 0.875rem;
        width: 16px;
        text-align: center;
    }
    
    .action-dropdown .dropdown-item.text-danger:hover {
        background: #fef2f2;
    }
    

    
    /* Empty State */
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
    }
    
    .empty-state-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 2rem;
        color: #94a3b8;
    }
    
    /* Bulk Action Bar */
    .bulk-action-bar {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #fcd34d;
        border-radius: 12px;
        padding: 0.875rem 1.25rem;
    }
    
    /* Header Buttons */
    .btn-header {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border-radius: 10px;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .btn-header-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }
    
    .btn-header-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        color: white;
    }
    
    .btn-header-outline {
        background: #fff;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    
    .btn-header-outline:hover {
        background: #fef2f2;
        border-color: #fca5a5;
    }
    
    /* Pagination */
    .pagination-wrapper {
        padding: 1rem 1.25rem;
        border-top: 1px solid rgba(0,0,0,0.06);
    }
    
    /* Text Truncation */
    .text-truncate-custom {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 130px;
        display: block;
    }
    
    /* Sender Receiver Cell */
    .sender-receiver {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        white-space: nowrap;
    }
    
    .sender-receiver .arrow {
        color: #cbd5e1;
        font-size: 0.625rem;
    }
    
    /* Compact Badge */
    .badge-soft {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.6875rem;
        font-weight: 500;
        white-space: nowrap;
    }
    
    /* Scrollbar Styling for Table */
    .table-responsive-wrapper::-webkit-scrollbar {
        height: 8px;
    }
    
    .table-responsive-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .table-responsive-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    .table-responsive-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 1200px) {
        .custom-table {
            min-width: 1000px;
        }
        
        .file-name {
            max-width: 180px;
        }
    }
    
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }
        
        .file-name {
            max-width: 150px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Berkas Transaksi</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1 fw-bold">Berkas Transaksi</h1>
            <p class="text-muted mb-0">Arsip dokumen serah terima barang</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn-header btn-header-outline" onclick="showDeleteModal()">
                <i class="fas fa-trash-alt"></i>
                <span>Hapus Massal</span>
            </button>
            <a href="{{ route('berkas-transaksi.create') }}" class="btn-header btn-header-primary text-decoration-none">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Upload Berkas</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card total h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div>
                        <div class="stat-label mb-1">Total Berkas</div>
                        <div class="stat-value">{{ $totalBerkas }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card month h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <div class="stat-label mb-1">Bulan Ini</div>
                        <div class="stat-value">{{ $totalBerkasBulanIni }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card size h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <div>
                        <div class="stat-label mb-1">Total Size</div>
                        <div class="stat-value">
                            @if($totalSize >= 1073741824)
                                {{ number_format($totalSize / 1073741824, 2) }} <small class="fs-6">GB</small>
                            @elseif($totalSize >= 1048576)
                                {{ number_format($totalSize / 1048576, 2) }} <small class="fs-6">MB</small>
                            @elseif($totalSize >= 1024)
                                {{ number_format($totalSize / 1024, 2) }} <small class="fs-6">KB</small>
                            @else
                                {{ $totalSize }} <small class="fs-6">bytes</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card users h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="stat-label mb-1">Uploader</div>
                        <div class="stat-value">{{ $users->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="filter-card mb-4">
        <div class="filter-header">
            <h6><i class="fas fa-sliders-h"></i> Filter & Pencarian</h6>
        </div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('berkas-transaksi.index') }}">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small text-muted">Cari</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" 
                                   placeholder="Nomor surat, perihal..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small text-muted">Dari Tanggal</label>
                        <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small text-muted">Sampai Tanggal</label>
                        <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small text-muted">Tahun</label>
                        <select name="tahun_filter" class="form-select">
                            <option value="">Semua</option>
                            @foreach($availableYears as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun_filter') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small text-muted">Uploader</label>
                        <select name="user_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-1 col-md-12">
                        <label class="form-label small text-muted d-none d-lg-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('berkas-transaksi.index') }}" class="btn btn-secondary" title="Reset">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-action-bar" id="bulkActionBar" style="display: none;">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="selected-badge">
                    <i class="fas fa-check-circle"></i>
                    <span id="selectedCount">0</span> item dipilih
                </span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-light" onclick="selectAll()">
                    <i class="fas fa-check-square me-1"></i> Pilih Semua
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteSelected()">
                    <i class="fas fa-trash-alt me-1"></i> Hapus Terpilih
                </button>
            </div>
        </div>
    </div>

    <!-- Files Container with View Toggle -->
    <div class="files-container">
        <div class="files-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <h6 class="mb-0 fw-bold" style="color: #334155; font-size: 1rem;">Daftar Berkas</h6>
                        <small class="text-muted">{{ $berkas->total() }} dokumen tersedia</small>
                    </div>
                    @if($berkas->count() > 0)
                    <div class="form-check ms-3">
                        <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onclick="toggleSelectAll()">
                        <label class="form-check-label small" for="selectAllCheckbox">Pilih Semua</label>
                    </div>
                    @endif
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <!-- View Toggle Buttons -->
                    <div class="view-toggle">
                        <button type="button" class="view-btn active" id="btnCardView" onclick="switchView('card')" title="Tampilan Kartu">
                            <i class="fas fa-th-large"></i>
                            <span>Kartu</span>
                        </button>
                        <button type="button" class="view-btn" id="btnListView" onclick="switchView('list')" title="Tampilan List">
                            <i class="fas fa-list"></i>
                            <span>List</span>
                        </button>
                    </div>
                    
                    @if($berkas->hasPages())
                        <span class="badge bg-light text-dark border ms-2">
                            {{ $berkas->firstItem() }} - {{ $berkas->lastItem() }} dari {{ $berkas->total() }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        @if($berkas->count() > 0)
        <!-- CARD VIEW -->
        <div id="cardView" class="files-grid">
            @foreach($berkas as $index => $item)
            <div class="file-card" id="card-{{ $item->id }}">
                <div class="card-header-flex">
                    <div class="card-checkbox">
                        <input type="checkbox" class="form-check-input item-checkbox" 
                               value="{{ $item->id }}" onchange="updateSelectedCount()">
                    </div>
                    <div class="card-pdf-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="card-main-info">
                        <div class="card-filename" title="{{ $item->file_name }}">
                            {{ $item->file_name }}
                        </div>
                        <div class="card-meta">
                            <span><i class="far fa-calendar-alt"></i> {{ $item->created_at->format('d M Y') }}</span>
                            <span><i class="fas fa-hdd"></i> {{ $item->file_size_human }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-details">
                    <div class="detail-item">
                        <span class="detail-label">Nomor Surat</span>
                        <span class="detail-value {{ $item->nomor_surat ? '' : 'muted' }}">
                            {{ $item->nomor_surat ?? 'Belum diisi' }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tanggal Surat</span>
                        <span class="detail-value {{ $item->tanggal_surat ? '' : 'muted' }}">
                            {{ $item->tanggal_surat ? $item->tanggal_surat->format('d M Y') : 'Belum diisi' }}
                        </span>
                    </div>
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <span class="detail-label">Perihal</span>
                        <span class="detail-value {{ $item->perihal ? '' : 'muted' }}">
                            {{ $item->perihal ?? 'Belum diisi' }}
                        </span>
                    </div>
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <span class="detail-label">Pengirim → Penerima</span>
                        <div class="detail-flow">
                            <span>{{ $item->pengirim ?? '-' }}</span>
                            <i class="fas fa-arrow-right arrow"></i>
                            <span>{{ $item->penerima ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer-flex">
                    <div class="card-uploader">
                        <div class="uploader-avatar">
                            {{ substr($item->user->name ?? 'U', 0, 1) }}
                        </div>
                        <span class="uploader-name">{{ $item->user->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="card-actions">
                        <a href="{{ route('berkas-transaksi.show', $item) }}" class="action-btn view" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('berkas-transaksi.download', $item) }}" class="action-btn download" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="{{ route('berkas-transaksi.edit', $item) }}" class="action-btn edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="action-btn delete" onclick="deleteItem('{{ $item->id }}')" title="Hapus">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- LIST VIEW (Hidden by default) -->
        <div id="listView" class="files-list" style="display: none;">
            @foreach($berkas as $index => $item)
            <div class="file-list-item" id="list-{{ $item->id }}">
                <div class="list-checkbox">
                    <input type="checkbox" class="form-check-input item-checkbox" 
                           value="{{ $item->id }}" onchange="updateSelectedCount()">
                </div>
                <div class="list-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="list-content">
                    <div class="list-main">
                        <div class="list-filename">{{ $item->file_name }}</div>
                        <div class="list-meta">
                            <span class="list-meta-item"><i class="far fa-calendar"></i> {{ $item->created_at->format('d M Y') }}</span>
                            <span class="list-meta-item"><i class="fas fa-hdd"></i> {{ $item->file_size_human }}</span>
                            <span class="list-meta-item"><i class="fas fa-file-alt"></i> {{ $item->nomor_surat ?? 'No Surat: -' }}</span>
                            <span class="list-meta-item"><i class="far fa-calendar-check"></i> {{ $item->tanggal_surat ? $item->tanggal_surat->format('d M Y') : 'Tgl: -' }}</span>
                            <span class="list-meta-item"><i class="fas fa-info-circle"></i> {{ $item->perihal ? Str::limit($item->perihal, 40) : 'Perihal: -' }}</span>
                        </div>
                    </div>
                    <div class="list-sender-receiver">
                        <span class="sender">{{ $item->pengirim ?? '-' }}</span>
                        <i class="fas fa-arrow-right"></i>
                        <span class="receiver">{{ $item->penerima ?? '-' }}</span>
                    </div>
                </div>
                <div class="list-uploader">
                    <div class="uploader-badge">{{ substr($item->user->name ?? 'U', 0, 1) }}</div>
                    <span>{{ $item->user->name ?? 'Unknown' }}</span>
                </div>
                <div class="list-actions">
                    <a href="{{ route('berkas-transaksi.show', $item) }}" class="action-btn-sm view" title="Detail">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('berkas-transaksi.download', $item) }}" class="action-btn-sm download" title="Download">
                        <i class="fas fa-download"></i>
                    </a>
                    <a href="{{ route('berkas-transaksi.edit', $item) }}" class="action-btn-sm edit" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" class="action-btn-sm delete" onclick="deleteItem('{{ $item->id }}')" title="Hapus">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state-modern">
            <div class="empty-icon-wrapper">
                <i class="fas fa-folder-open"></i>
            </div>
            <h4>Belum ada berkas</h4>
            <p>Mulai upload dokumen untuk mengarsipkannya</p>
            <a href="{{ route('berkas-transaksi.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-cloud-upload-alt me-2"></i>Upload Berkas Pertama
            </a>
        </div>
        @endif
        
        @if($berkas->hasPages())
        <div class="pagination-wrapper">
            {{ $berkas->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Modal (Single) -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Konfirmasi Hapus</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                </div>
                <p class="mb-0 text-muted">Apakah Anda yakin ingin menghapus berkas ini?</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="fas fa-trash-alt me-2 text-danger"></i>Hapus Massal</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small text-muted">Pilih Jenis Penghapusan:</label>
                    <select class="form-select" id="deleteType" onchange="toggleDeleteOptions()">
                        <option value="">-- Pilih --</option>
                        <option value="selected">Hapus yang Dipilih</option>
                        <option value="all">Hapus Semua Berkas</option>
                        <option value="month">Hapus per Bulan</option>
                        <option value="range">Hapus Rentang Bulan</option>
                    </select>
                </div>

                <!-- Delete by Month -->
                <div id="deleteMonthOptions" style="display: none;">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small text-muted">Tahun</label>
                            <select class="form-select" id="deleteTahun">
                                @foreach($availableYears as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">Bulan</label>
                            <select class="form-select" id="deleteBulan">
                                @foreach(range(1, 12) as $bulan)
                                    <option value="{{ $bulan }}">{{ DateTime::createFromFormat('!m', $bulan)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Delete by Range -->
                <div id="deleteRangeOptions" style="display: none;">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small text-muted">Tahun Dari</label>
                            <select class="form-select" id="deleteTahunDari">
                                @foreach($availableYears as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">Bulan Dari</label>
                            <select class="form-select" id="deleteBulanDari">
                                @foreach(range(1, 12) as $bulan)
                                    <option value="{{ $bulan }}">{{ DateTime::createFromFormat('!m', $bulan)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small text-muted">Tahun Sampai</label>
                            <select class="form-select" id="deleteTahunSampai">
                                @foreach($availableYears as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">Bulan Sampai</label>
                            <select class="form-select" id="deleteBulanSampai">
                                @foreach(range(1, 12) as $bulan)
                                    <option value="{{ $bulan }}">{{ DateTime::createFromFormat('!m', $bulan)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning mt-3 mb-0 d-flex align-items-center gap-2" style="border-radius: 10px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <small>Data yang sudah dihapus tidak dapat dikembalikan.</small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="executeBulkDelete()">
                    <i class="fas fa-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let selectedIds = [];
let currentView = localStorage.getItem('berkasView') || 'card';

// Initialize view on page load
document.addEventListener('DOMContentLoaded', function() {
    switchView(currentView, false);
});

function switchView(view, save = true) {
    currentView = view;
    
    const cardView = document.getElementById('cardView');
    const listView = document.getElementById('listView');
    const btnCard = document.getElementById('btnCardView');
    const btnList = document.getElementById('btnListView');
    
    if (view === 'card') {
        cardView.style.display = 'grid';
        listView.style.display = 'none';
        btnCard.classList.add('active');
        btnList.classList.remove('active');
    } else {
        cardView.style.display = 'none';
        listView.style.display = 'flex';
        btnCard.classList.remove('active');
        btnList.classList.add('active');
    }
    
    if (save) {
        localStorage.setItem('berkasView', view);
    }
}

function updateSelectedCount() {
    selectedIds = [];
    document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
        selectedIds.push(cb.value);
        // Highlight the card
        const card = document.getElementById('card-' + cb.value);
        if (card) card.classList.add('selected');
        // Highlight the list item
        const listItem = document.getElementById('list-' + cb.value);
        if (listItem) listItem.classList.add('selected');
    });
    
    // Remove highlight from unchecked items
    document.querySelectorAll('.item-checkbox:not(:checked)').forEach(cb => {
        const card = document.getElementById('card-' + cb.value);
        if (card) card.classList.remove('selected');
        const listItem = document.getElementById('list-' + cb.value);
        if (listItem) listItem.classList.remove('selected');
    });
    
    const count = selectedIds.length;
    document.getElementById('selectedCount').textContent = count;
    
    const bulkBar = document.getElementById('bulkActionBar');
    if (count > 0) {
        bulkBar.style.display = 'block';
    } else {
        bulkBar.style.display = 'none';
    }
}

function toggleSelectAll() {
    const masterCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    
    checkboxes.forEach(cb => {
        cb.checked = masterCheckbox.checked;
        // Handle card
        const card = document.getElementById('card-' + cb.value);
        if (card) {
            if (masterCheckbox.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        }
        // Handle list item
        const listItem = document.getElementById('list-' + cb.value);
        if (listItem) {
            if (masterCheckbox.checked) {
                listItem.classList.add('selected');
            } else {
                listItem.classList.remove('selected');
            }
        }
    });
    
    updateSelectedCount();
}

function selectAll() {
    document.getElementById('selectAllCheckbox').checked = true;
    toggleSelectAll();
}

function deleteItem(id) {
    const form = document.getElementById('deleteForm');
    form.action = '{{ route("berkas-transaksi.destroy", "") }}/' + id;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function deleteSelected() {
    if (selectedIds.length === 0) {
        alert('Pilih minimal 1 item untuk dihapus');
        return;
    }
    
    if (!confirm('Yakin ingin menghapus ' + selectedIds.length + ' berkas terpilih?')) {
        return;
    }
    
    fetch('{{ route("berkas-transaksi.bulk-delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids: selectedIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan: ' + error);
    });
}

function showDeleteModal() {
    const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
    modal.show();
}

function toggleDeleteOptions() {
    const type = document.getElementById('deleteType').value;
    
    document.getElementById('deleteMonthOptions').style.display = 'none';
    document.getElementById('deleteRangeOptions').style.display = 'none';
    
    if (type === 'month') {
        document.getElementById('deleteMonthOptions').style.display = 'block';
    } else if (type === 'range') {
        document.getElementById('deleteRangeOptions').style.display = 'block';
    }
}

function executeBulkDelete() {
    const type = document.getElementById('deleteType').value;
    
    if (!type) {
        alert('Pilih jenis penghapusan');
        return;
    }
    
    let url = '';
    let body = {};
    
    switch(type) {
        case 'selected':
            if (selectedIds.length === 0) {
                alert('Pilih minimal 1 item terlebih dahulu');
                return;
            }
            url = '{{ route("berkas-transaksi.bulk-delete") }}';
            body = { ids: selectedIds };
            break;
            
        case 'all':
            if (!confirm('Yakin ingin menghapus SEMUA berkas? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }
            url = '{{ route("berkas-transaksi.delete-all") }}';
            break;
            
        case 'month':
            url = '{{ route("berkas-transaksi.delete-by-month") }}';
            body = {
                tahun: document.getElementById('deleteTahun').value,
                bulan: document.getElementById('deleteBulan').value
            };
            break;
            
        case 'range':
            url = '{{ route("berkas-transaksi.delete-by-range") }}';
            body = {
                tahun_dari: document.getElementById('deleteTahunDari').value,
                bulan_dari: document.getElementById('deleteBulanDari').value,
                tahun_sampai: document.getElementById('deleteTahunSampai').value,
                bulan_sampai: document.getElementById('deleteBulanSampai').value
            };
            break;
    }
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: Object.keys(body).length > 0 ? JSON.stringify(body) : null
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan: ' + error);
    });
}
</script>
@endsection
