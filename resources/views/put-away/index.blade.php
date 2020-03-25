@extends('layouts.app')
@section('content')
@section('title',__("messages.storage.put_away"))
<div class="content-card custom-scroll">
    <input type="hidden" name="active_tab" value="{{ $active_tab }}">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.storage.put_away_dashboard')</h3>
        <div class="center-items">
            <ul class="nav nav-tabs header-tab" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'put-away-dashboard' ? 'active' : ''}}"  href="{{ route('put-away-dashboard') }}">
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
        <form method="post" id="listing-manager-form" enctype="multipart/form-data">
            <div class="right-items">
                @if($active_tab == 'put-away-dashboard')
                <select name="warehouse_id" id="warehouse_id"  class="form-control">
                    @if(!empty($wareHouses))
                    @foreach($wareHouses as $key=>$val)
                    <option value="{{ $val->id }}" {{ $val->is_default ? 'selected="selected"' : '' }}>{{ $val->name }}</option>
                    @endforeach
                    @endif
                </select>
                @endif
                <!-- <div class="center-items">
                    <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="search by title, sku" />
                      <span class="refresh"></span>
                </div> -->
            </div>
        </form>
    </div>
    <div class="card-flex-container">
        <div class="container-fluid">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="put-away-dashboard" role="tabpanel" aria-labelledby="put-away-dashboard-tab" >
                    <!-- @include('put-away.put_away_dashboard_tab') -->
                </div>
                <div class="tab-pane fade" id="put-away" role="tabpanel" aria-labelledby="put-away-tab">
                    <!-- @include('put-away.put_away_tab') -->
                </div>
                <div class="tab-pane fade" id="put-away-job-list" role="tabpanel" aria-labelledby="put-away-job-list-tab">
                    <!-- @include('put-away.put_away_job_list_tab') -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- <script type="text/javascript" src="{{asset('js/put-away/index.js?v='.CSS_JS_VERSION)}}"></script> -->
@endsection