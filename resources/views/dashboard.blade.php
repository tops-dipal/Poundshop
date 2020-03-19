@extends('layouts.app')
@section('content')
@section('title',__('messages.pages.dashboard.title'))
<div class="content-card custom-scroll">
        <div class="content-card-header">
            <h3 class="page-title">{{$welcomeMessage}}</h3>	
        </div>	
</div>
@endsection