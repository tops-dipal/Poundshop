@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))
@section('content')

<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">{{$page_title}}</h3>
        <div class="right-items">
            @can('role-create')
            <a class="btn btn-add btn-blue" href="{{ route('roles.create') }}">
                <span class="icon-moon icon-Supplier"></span>
                Add Role
            </a>
            @endcan
        </div>
    </div>  


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
   
    <div class="card-flex-container d-flex">                        
        <div class="d-flex-xs-block">
            <table class="display table data-table">    
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th width="280px">Action</th>
                    </tr>
                </thead>

                <tbody>
                </tbody>
               
            </table>
        </div>    
    </div>    
</div>   


@endsection

@section('css')
    
    <style type="text/css">
        .disp_none{display: none}
    </style>
@endsection

@section('script')
    
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            searching: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            language: {
                search: "_INPUT_",
                processing: '<div><img href="{{asset('img/loader.gif')}}"></div>',
                searchPlaceholder: "Search By Role",
                "paginate": {
                  "previous": "<span class='icon-moon icon-Drop-Down-1'></span>",
                  "next": "<span class='icon-moon icon-Drop-Down-1'></span>"
                }
            },
            "dom": '<"custom-table-header"lf<"refresh reset_search">><"custom-table-body"rt><"custom-table-footer"ip>',
            ajax: "{{ route('roles.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', class:"index"},
                {data: 'name', name: 'name'},
                {data: 'action', name: 'action', class:"action", orderable: false, searchable: false},
            ]
        });

        function delete_record(me)
        {
            if(confirm('Are you sure, you want to delete this record?'))
            {    
                $(me).parent('form').submit();
            }
        }

        $('.reset_search').click(function(){
            $('.data-table').DataTable().search('').draw();
        });
    </script>    
@endsection