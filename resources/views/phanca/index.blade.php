@extends('layouts.app')
@section('title', $canManage ? 'Phan cong lich lam' : 'Lich lam cua toi')

@section('content')
    {{-- 1. Phần tiêu đề và nút điều hướng tuần --}}
    @include('phanca.partials.header')

    {{-- 2. Phần hiển thị danh mục ca làm việc --}}
    @include('phanca.partials.shift-legend')

    {{-- 3. Bảng lịch làm việc --}}
    @include('phanca.partials.schedule-table')

    {{-- 4. Modal phân công ca (chỉ tải khi có quyền quản lý) --}}
    @if($canManage)
        @include('phanca.partials.assign-modal')
    @endif
@endsection
