@extends('layouts.app')
@section('content')
@section('title',__("messages.storage.put_away_job_list"))
<div class="content-card custom-scroll">
    <input type="hidden" name="active_tab" id="active_tab" value="{{ $active_tab }}">
    <div class="content-card-header">
        <h3 class="page-title putaway-fix-title">@lang('messages.storage.put_away_job_list')</h3>
        <div class="center-items">
            <ul class="nav nav-tabs header-tab" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'put-away-dashboard' ? 'active' : ''}}" id="put-away-dashboard-tab" href="{{ route('put-away-dashboard') }}">
                        @lang('messages.storage.put_away_dashboard')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'put-away' ? 'active' : ''}}" id="put-away-tab" href="{{ route('put-away') }}">
                        @lang('messages.storage.put_away')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  {{ $active_tab == 'put-away-job-list' ? 'active' : ''}}" id="put-away-job-list-tab"  href="{{ route('put-away-job-list') }}">
                        @lang('messages.storage.put_away_job_list')
                    </a>
                </li>
            </ul>
        </div>
        <div class="right-items">
            &nbsp;
        </div>
    </div>
    <div class="card-flex-container d-flex">
        Comming Soon..
    </div>
</div>
@endsection
@section('script')
<!-- <script type="text/javascript" src="{{asset('js/put-away/index.js?v='.CSS_JS_VERSION)}}"></script> -->
@endsection