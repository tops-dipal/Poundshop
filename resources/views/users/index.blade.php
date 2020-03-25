@extends('layouts.app')
@section('content')
@section('title',__('messages.user_management.users_list'))
  <div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.user_management.users_list')</h3>
        <div class="center-items">
            <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.common.search_user')" />
              <span class="refresh"></span>
        </div>  
        <div class="right-items"> 
            @can('users-create')
            <a class="btn btn-add btn-light-green btn-header" href="{{route('users.create')}}" title="@lang('messages.user_management.user_add')">
                <span class="icon-moon icon-Add"></span>
                <!-- @lang('messages.modules.pallets_add') -->
            </a>
            @endcan     
      <!--   <div class="right-items">
        <button class="btn btn-add btn-blue"><span class="icon-moon icon-user"></span><a href="{{route('users.create')}}">@lang('messages.user_management.user_add')</a></button>
        <button class="btn btn-add btn-red delete-many"><span class="icon-moon icon-Delete"></span>@lang('messages.user_management.user_delete')</button>
      </div>  -->         
        </div>  
    </div>
     <div class="card-flex-container d-flex">                        
        <div class="d-flex-xs-block">
            <div class="table-responsive">  
        <table id="users_table" class="display">
            <thead>
                <tr>
                    <th class="remove_sort">
                        <div class="d-flex">
                            <label class="fancy-checkbox">
                                <input name="ids[]" type="checkbox" class="master">
                                <span><i></i></span>
                            </label>
                            <div class="dropdown bulk-action-dropdown">
                                <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="icon-moon icon-Drop-Down-1"/>
                                </button>
                                
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                                    <h4 class="title">Bulk action</h4>
                                    <button class="btn btn-add delete-many">
                                    <span class="icon-moon red icon-Delete"></span>
                                   @lang('messages.user_management.user_delete')
                                    </button>
                                    <!-- <button class="btn btn-add delete-many">
                                    <span class="icon-moon yellow icon-Delete"></span>
                                    Select All
                                    </button>
                                    <button class="btn btn-add delete-many">
                                    <span class="icon-moon gray icon-Delete"></span>
                                    Deselect All
                                    </button> -->
                                </div>
                            </div>
                      </div>
                    </th>
                    <th class="m-w-80">@lang("messages.user_management.image")</th>
                    <th class="m-w-120">@lang("messages.user_management.name")</th>
                    <th>@lang("messages.user_management.email")</th>
                    <th>Site</th>
                    <th>@lang("messages.user_management.role")</th>
                    <th>@lang("messages.user_management.contact_no")</th>
                    <th>@lang("messages.user_management.date_enroll")</th>
                    <th data-class-name="action action-one">@lang('messages.table_label.action')</th>       
                </tr>
            </thead>
            <tbody>
              
                <!--  -->
              
            </tbody>
        </table> 
        </div>       
      </div>
    </div>
  </div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/users/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection