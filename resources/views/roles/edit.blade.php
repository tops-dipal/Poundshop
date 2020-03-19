@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))
@section('content')
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">{{$page_title}}</h3>
    </div>
    <div class="card-flex-container">
        <form action="{{route('roles.update', $role->id)}}" method="POST" class="form-horizontal form-flex">
            @csrf
            @method('PATCH')
            <div class="form-fields">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Role Name <span class="asterisk">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{!empty(old('name')) ? old('name') : $role->name}}" placeholder="Role Name"/>
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
                                <label class="col-lg-3 col-form-label">Permission <span class="asterisk">*</span></label>
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="roles-card">
                                                <label class="fancy-checkbox parent-role">
                                                    <input type="checkbox" onchange="grant_all_permissions(this)" id="grand_all_permissions">
                                                    <span><i></i>Grant All Permissions</span>
                                                </label>
                                                
                                                
                                                @error('permission')
                                                <p class="invalid-feedback dply_blck" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </p>
                                                @enderror
                                            </div>
                                        </div>
                                        @php
                                        if(!empty($permissions)){
                                        foreach($permissions as $module){ $master_id =$module->id @endphp
                                        <div class="col-md-12">
                                            <div class="roles-card parent-card">
                                                @if(!empty($module->parent_caption))
                                                    <label class="fancy-checkbox parent-role">
                                                        <input class="grant_all" type="checkbox" attr-module-master="{{$master_id}}" onchange="set_module_permission(this)">
                                                        <span><i></i>{{$module->parent_caption}}</span>
                                                    </label>
                                                @endif
                                                <div class="child-role">
                                                    <div>
                                                        <label class="fancy-checkbox">
                                                            <input class="grant_all" type="checkbox" name="permission[]" value="{{$module->id}}" attr-par="{{$master_id}}" {{in_array($module->id, $rolePermissions) ? 'checked="checked"' : ""}} />
                                                            <span><i></i>{{$module->name}}</span>
                                                        </label>
                                                    </div>
                                                    
                                                    @php
                                                    if(!empty($module->children)){
                                                    $sub_permissions = $module->children;
                                                    }
                                                    recursion:
                                                    if(!empty($sub_permissions)){
                                                    foreach($sub_permissions as $module_key => $sub_module){
                                                    @endphp
                                                    <div>
                                                        <label class="fancy-checkbox">
                                                            <input class="grant_all" type="checkbox" name="permission[]" value="{{$sub_module->id}}" attr-par="{{$master_id}}" {{in_array($sub_module->id, $rolePermissions) ? 'checked="checked"' : ""}} />
                                                            <span><i></i>{{$sub_module->name}}</span>
                                                        </label>
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
                                            </div>
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
    <script type="text/javascript" src="{{asset('js/jQuery-validation-plugin/jquery.validate.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            page.initialize();
        });

        var page= {};
        
        page.initialize = function ()
        {
            this.formValidate();
            this.doc_read_check_masters();
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

        page.doc_read_check_masters = function(){
            $('input[attr-module-master]').each(function(){
                master_id = $(this).attr('attr-module-master');
                if($('input[attr-par='+master_id+']').length == $('input[attr-par='+master_id+']:checked').length)
                {
                    $(this).prop('checked', true);
                }
            });
            
            if($('.grant_all').length == $('.grant_all:checked').length)
            {
                $('#grand_all_permissions').prop('checked', true);
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