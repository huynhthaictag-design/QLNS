@extends('layouts.app')
@section('title', $canManage ? 'Bảng phân ca làm việc' : 'Lịch làm của tôi')

@section('styles')
<style>
    .week-nav-pill {
        display: inline-flex;
        align-items: center;
        background: #fff;
        border: 1px solid #eae2f8;
        border-radius: 999px;
        padding: 4px;
        box-shadow: 0 4px 14px rgba(90, 60, 140, .06);
    }
    .week-nav-pill a {
        text-decoration: none;
        color: #4a435f;
        font-weight: 600;
        font-size: .84rem;
        padding: 6px 12px;
        border-radius: 999px;
        transition: all .15s ease;
    }
    .week-nav-pill a:hover {
        background: #f3eefc;
        color: #7c3aed;
    }
    .week-nav-pill .week-range {
        padding: 6px 14px;
        font-weight: 700;
        font-size: .86rem;
        color: #1b1233;
        border-left: 1px solid #f1ecf8;
        border-right: 1px solid #f1ecf8;
    }
    .shift-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1px solid #ece4f8;
        border-radius: 12px;
        padding: 7px 12px;
        font-size: .8rem;
        font-weight: 600;
        color: #2b2440;
        text-decoration: none;
        transition: all .15s ease;
    }
    .shift-chip:hover {
        border-color: #c4b5fd;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(124, 58, 237, .08);
    }
    .shift-chip.active {
        background: #7c3aed;
        border-color: #7c3aed;
        color: #fff;
    }
    .shift-chip .chip-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .shift-chip.active .chip-time {
        color: rgba(255, 255, 255, .85) !important;
    }

    /* Schedule Matrix Table */
    .schedule-matrix-wrap {
        overflow-x: auto;
        border-radius: 22px;
    }
    .schedule-matrix {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }
    .schedule-matrix thead th {
        background: #fcfaff;
        border-bottom: 1px solid #ede7f8;
        border-right: 1px solid #f3eefc;
        padding: 14px 10px;
        vertical-align: middle;
        white-space: nowrap;
    }
    .schedule-matrix thead th:last-child,
    .schedule-matrix tbody td:last-child,
    .schedule-matrix tfoot td:last-child {
        border-right: none;
    }
    .schedule-matrix .sticky-col {
        position: sticky;
        left: 0;
        z-index: 3;
        background: #fff;
        min-width: 240px;
        max-width: 260px;
        border-right: 2px solid #f0e9fa !important;
    }
    .schedule-matrix thead .sticky-col {
        background: #fcfaff;
        z-index: 4;
    }
    .schedule-matrix tfoot .sticky-col {
        background: #faf7ff;
    }
    .day-header-col {
        min-width: 148px;
        text-align: center;
    }
    .day-header-col .day-name {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #8b8499;
        font-weight: 700;
    }
    .day-header-col .day-num {
        font-size: 1.15rem;
        font-weight: 800;
        color: #2b2440;
        line-height: 1.25;
        margin-top: 2px;
    }
    .day-header-col.is-today {
        background: #f5f0ff !important;
        box-shadow: inset 0 3px 0 #7c3aed;
    }
    .day-header-col.is-today .day-name,
    .day-header-col.is-today .day-num {
        color: #7c3aed;
    }
    .day-header-col.is-weekend {
        background: #faf9fc;
    }

    .schedule-matrix tbody td {
        border-bottom: 1px solid #f2ecf9;
        border-right: 1px solid #f5f0fc;
        padding: 10px 8px;
        vertical-align: top;
        transition: background .15s ease;
    }
    .schedule-matrix tbody tr:hover td {
        background: #fcfaff;
    }
    .schedule-matrix tbody tr:hover td.sticky-col {
        background: #fcfaff;
    }
    .schedule-matrix tbody td.is-today {
        background: #faf7ff;
    }
    .schedule-matrix tbody td.is-weekend {
        background: #fcfcfd;
    }

    .emp-hours-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f3eefc;
        color: #6d28d9;
        border-radius: 999px;
        padding: 2px 9px;
        font-size: .72rem;
        font-weight: 700;
        margin-top: 5px;
    }

    /* Shift Card inside Day Cell */
    .shift-card {
        position: relative;
        border-radius: 10px;
        padding: 7px 9px;
        margin-bottom: 6px;
        text-align: left;
        border: 1px solid rgba(0, 0, 0, .05);
        border-left-width: 4px;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .shift-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(43, 36, 64, .09);
    }
    .shift-card-title {
        font-weight: 700;
        font-size: .79rem;
        line-height: 1.25;
        color: #1f1933;
        padding-right: 18px;
    }
    .shift-card-time {
        font-size: .71rem;
        color: #645c78;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .shift-card-note {
        font-size: .69rem;
        color: #7c7394;
        margin-top: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 128px;
    }
    .shift-card-remove {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: none;
        background: rgba(255, 255, 255, .85);
        color: #e11d48;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .75rem;
        opacity: .25;
        transition: opacity .15s ease, background .15s ease;
        cursor: pointer;
    }
    .shift-card:hover .shift-card-remove {
        opacity: 1;
    }
    .shift-card-remove:hover {
        background: #ffe4ea;
    }
    .shift-checked-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: .66rem;
        font-weight: 700;
        color: #16a34a;
        background: rgba(22, 163, 74, .12);
        border-radius: 999px;
        padding: 1px 6px;
        margin-top: 4px;
    }

    .cell-add-btn {
        width: 100%;
        border: 1.5px dashed #e2d9f3;
        background: transparent;
        color: #8b5cf6;
        border-radius: 10px;
        padding: 5px 8px;
        font-size: .75rem;
        font-weight: 600;
        transition: all .15s ease;
        opacity: .65;
    }
    .schedule-matrix tbody td:hover .cell-add-btn {
        opacity: 1;
        border-color: #8b5cf6;
        background: #f5f0ff;
    }
    .cell-empty-off {
        text-align: center;
        padding: 10px 4px;
        color: #b7b0c8;
        font-size: .76rem;
        font-weight: 500;
    }
    .schedule-matrix tfoot td {
        background: #faf7ff;
        border-top: 2px solid #ede7f8;
        border-right: 1px solid #f0e9fa;
        padding: 10px 8px;
        font-size: .78rem;
        font-weight: 700;
        color: #4a435f;
    }

    /* Visual Shift Radio Cards in Modal */
    .shift-option-card {
        display: block;
        border: 1.5px solid #ece4f8;
        border-radius: 12px;
        padding: 10px 12px;
        cursor: pointer;
        transition: all .15s ease;
        background: #fff;
    }
    .shift-option-card:hover {
        border-color: #a78bfa;
        background: #fcfaff;
    }
    .shift-option-input:checked + .shift-option-card {
        border-color: #7c3aed;
        background: #f5f0ff;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, .12);
    }
    .day-toggle-pill {
        display: inline-block;
        cursor: pointer;
    }
    .day-toggle-pill input { display: none; }
    .day-toggle-pill span {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        min-width: 54px;
        padding: 6px 8px;
        border-radius: 12px;
        border: 1.5px solid #e7dff5;
        background: #fff;
        font-size: .74rem;
        font-weight: 700;
        color: #5b5470;
        transition: all .15s ease;
    }
    .day-toggle-pill input:checked + span {
        background: #7c3aed;
        border-color: #7c3aed;
        color: #fff;
    }
</style>
@endsection

@section('content')
    {{-- 1. Header, điều hướng tuần và thanh công cụ --}}
    @include('phanca.partials.header')

    {{-- 2. Thanh KPI thống kê nhanh + Bộ lọc & Danh mục ca --}}
    @include('phanca.partials.shift-legend')

    {{-- 3. Bảng Ma trận Phân ca hiện đại --}}
    @include('phanca.partials.schedule-table')

    {{-- 4. Modal phân ca nâng cao (chỉ tải khi có quyền quản lý) --}}
    @if($canManage)
        @include('phanca.partials.assign-modal')
    @endif
@endsection
