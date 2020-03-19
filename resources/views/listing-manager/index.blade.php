@extends('layouts.app')
@section('content')
@section('title',__("messages.modules.listing_manager"))
<div class="content-card custom-scroll">
        <input type="hidden" name="active_tab" value="{{ $active_tab }}">
        <div class="content-card-header">
            <h3 class="page-title">Magento Listing Manager</h3> 
            <div class="center-items">
                <ul class="nav nav-tabs header-tab" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $active_tab == 'already-listed' ? 'active' : ''}}"  href="{{ route('magento-already-listed') }}">
                            Already Listed
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $active_tab == 'to-be-listed' ? 'active' : ''}}" id="to-be-listed-tab" href="{{ route('magento-to-be-listed') }}">
                            To be Listed
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  {{ $active_tab == 'inprogress' ? 'active' : ''}}" id="inprogress-tab"  href="{{ route('magento-in-progress') }}">
                             In Progress
                        </a>
                    </li>
                </ul>  
            </div>
            <form method="post" id="listing-manager-form" enctype="multipart/form-data">
            <div class="right-items">
                <select name="store_id" id="store_id"  class="form-control" hidden="">
                    @forelse($storeList as $key=>$val)
                    <option value="{{ $val->id }}" {{ $loop->first ? 'selected="selected"' : '' }}>{{ $val->store_name }}</option>
                    @empty
                    @endforelse
                    
                </select>
                <div class="center-items">
                    <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="search by title, sku" />
                      <span class="refresh"></span>
                </div>
            </div>  
             </form>                  
        </div>  
        <div class="card-flex-container">
            <div class="container-fluid">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="already-listed" role="tabpanel" aria-labelledby="already-listed-tab">
                         @include('listing-manager._already_listed_tab')
                    </div>
                    <div class="tab-pane fade" id="to-be-listed" role="tabpanel" aria-labelledby="to-be-listed-tab">   
                        @include('listing-manager._to_be_listed_tab')
                    </div>   
                     <div class="tab-pane fade" id="inprogress" role="tabpanel" aria-labelledby="inprogress-tab">   
                        @include('listing-manager._in_progress_tab')
                    </div>   
                </div>
            </div>
        </div>
   
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/listing-manager/index.js')}}"></script>
@endsection