@extends('layouts.app')

@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))

@section('content')
    <div class="content-card custom-scroll">
        <div class="content-card-header">
            <h3 class="page-title">{{$page_title}}</h3>
        </div>
        
        <div class="card-flex-container">
            <form action="{{route('roles.store')}}" method="post" class="form-horizontal form-flex">
                {{ csrf_field() }}
                <div class="form-fields">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Role Name <span class="asterisk">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Role Name" value="{{old('name')}}"/>
                                        @error('name')
                                            <p class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Permission <span class="asterisk">*</span></label>
                                    <div class="col-md-12">
                                        Grant All Permissions
                                        <input type="checkbox" onchange="grant_all_permissions(this)" id="grand_all_permissions">
                                         @error('permission')
                                            <p class="invalid-feedback dply_blck" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </p>
                                        @enderror
                                        <br>
                                    </div>            
                                    @php
                                    if(!empty($permissions)){
                                        foreach($permissions as $module){ $master_id =$module->id @endphp
                                            <div class="col-md-12">
                                                @if(!empty($module->parent_caption))
                                                    {{$module->parent_caption}}
                                                    <input class="grant_all" type="checkbox" attr-module-master="{{$master_id}}" onchange="set_module_permission(this)">
                                                    <br>
                                                @endif
                                                <div class="col-md-3">
                                                    <input class="grant_all" type="checkbox" name="permission[]" value="{{$module->id}}" attr-par="{{$master_id}}" />
                                                    {{$module->name}}
                                                </div>
                                                
                                                @php
                                                    if(!empty($module->children)){
                                                        $sub_permissions = $module->children;
                                                    }

                                                    recursion:
                                                    if(!empty($sub_permissions)){
                                                        foreach($sub_permissions as $module_key => $sub_module){ 
                                                @endphp
                                                        <div class="col-md-3">
                                                            <input class="grant_all" type="checkbox" name="permission[]" value="{{$sub_module->id}}" attr-par="{{$master_id}}">
                                                            {{$sub_module->name}}
                                                        </div>
                                                        @php
                                                            if(!empty($sub_module->children))
                                                            {
                                                                $sub_permissions = $sub_module->children;
                                                                goto recursion;
                                                            }
                                                    }
                                                } 
                                                @endphp
                                            </div>
                                        @php
                                        }
                                    }
                                    @endphp
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                  
                <div class="content-card-footer">
                    <div class="button-container">
                        <button type="submit" class="btn btn-green btn-form">Submit</button>
                        <a href="{{route('roles.index')}}" class="btn btn-gray btn-form">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var page= {};

        $(document).ready(function(){
            page.initialize();
        })

        page.initialize = function ()
        {
            this.formValidate();
        }    

        function grant_all_permissions(me)
        {
            $('.grant_all').prop('checked', $(me).prop('checked'));
        }

        function set_module_permission(me)
        {
            if(typeof $(me).attr('attr-module-master') != 'undefined')
            {
                par_id = $(me).attr('attr-module-master');
                $('input[attr-par='+par_id+']').prop('checked', $(me).prop('checked'));
            }    
        }

        page.formValidate = function () {
            $("form").validate({
                focusInvalid: true, // do not focus the last invalid input
                errorElement: 'span',
                errorClass: 'invalid-feedback', // default input error message class
                ignore: [],
                rules: {
                    "name": {
                        required: true,
                    },
                    "permission[]": {
                        required: true,
                    },
                },
                messages: {
                    "name": {
                        required: 'Group Name is required.',
                    
                    },
                    "permission[]": {
                        required: 'Atleast select one permission.',
                    }, 
                },
                errorPlacement: function (error, element) {
                  if(element.attr('name') == 'permission[]')
                  {
                    if(error.text().length > 0)
                    {  
                        $('#grand_all_permissions').parent().find('.invalid-feedback').remove();
                        $('#grand_all_permissions').after('<p class="invalid-feedback" role="alert" style="display: block;">'+error.text()+'</p>');
                    }    
                  }  
                  else
                  {
                    error.insertAfter(element);
                  }
                },
                highlight: function (element) { // hightlight error inputs
                    $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
                },
                success: function (label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },
                submitHandler: function (form) {
                    $("#page-loader").show();
                    $('#login-btn').attr('disabled', 'disabled');
                    form.submit();
                }
            });
            $.validator.addMethod("customemail", 
                function(value, element) {
                    return /^[_a-zA-Z0-9-+.]+(\.[_a-zA-Z0-9-+.]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/.test(value);
                }, 
                "Please enter valid email address"
            );
        };

    </script>
@endsection

@section('css')
<style type="text/css">
    .dply_blck{display: block !important;}
</style>
@endsection