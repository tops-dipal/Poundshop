<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <!-- utf-8 works for most cases -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Forcing initial-scale shouldn't be necessary -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
        
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{ asset('css/lightcase.css')}}">

        <link rel="shortcut icon" type="image/png" href="{{ asset('img/fevicon.png')}}"/>
        <!-- Use the latest (edge) version of IE rendering engine -->
        <title>{{$page_title}}</title>
        <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
        <style type="text/css">
            .bg-light { background-color: transparent; border-bottom: 2px solid #e5e5e5; }
            .navbar { height: 60px; }
            .navbar .title { font-size: 18px; }
            main { padding-top: 60px; }
            .custom-table { font-size: 14px; }
            .mr-product-detail { padding-left: 20px; position: relative; }
            .mr-product-detail .product-tags { position: absolute; top: 0; left: 0; }
            .mr-product-detail .product-tags .tag { cursor: pointer; font-size: 10px; font-weight: 700; background: #222; color: #fff; margin-bottom: 5px; padding: 2px 3px; letter-spacing: 1px; height: 14px; max-width: 14px; white-space: nowrap; overflow: hidden; line-height: 1; }
            .mr-product-detail .product-tags .tag.new { background: #009759; }
            .mr-product-detail .product-tags .tag.master { background: #e42328; }
            .mr-product-detail .product-tags .tag:hover, .mr-product-detail .product-tags .tag:focus { max-width: 500px; letter-spacing: 1px; }
            .mr-product-detail .product-tags .tag::first-letter { margin-right: 3px; }
            .mr-product-detail .product-tags .tag:hover::first-letter, .mr-product-detail .product-tags .tag:focus::first-letter { margin-right: 0px; }
            .table { table-layout: fixed; }
            .table thead th { vertical-align: top; background: #f2f2f2; }
            .table td, .table th { padding: 10px; }
            .table.small-table td, .table.small-table th { padding: 5px; border: none; }
            .table.small-table th{ background: transparent; }
            .w-30 { width: 30% }
            .w-25 { width: 25% }
            .w-10 { width: 10% }
            .w-12 { width: 12% }
            .w-13 { width: 13% }
            .w-15 { width: 15% }
            .product-title { font-weight: 600; }
            .bold { font-weight: 700; }
            .font-16 { font-size: 16px; }
            .diff-minus { color: red; }
            .diff-plus { color: green; }
            .border-none { border: none !important; }
            @media only screen and (max-width: 1024px) {
                .w-30 { width: 240px }              
                .w-10 { width: 100px }
                .w-12 { width: 120px }
                .w-13 { width: 130px }
                .w-15 { width: 160px }
            }
        </style>
    </head>
    <body>
        <nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="{{asset('img/logo-new.png')}}" />
                </a>
                
                <div class="col-md-3">
                    <select class="form-control" name="filter_by_po" onchange="page_update()">
                        <option value="">@lang('messages.material_receipt.filter_select_all_po')</option>
                        @if(!$booking_pos->isEmpty())
                            @foreach($booking_pos as $booking_po)
                                <option value="{{ $booking_po->purchaseOrder->id }}" {{ ($params['filter_by_po'] == $booking_po->purchaseOrder->id) ? 'selected="selected"' : "" }}>{{ $booking_po->purchaseOrder->po_number }}</option>
                            @endforeach
                        @endif
                    </select>  
                </div>

                <h4 class="title m-0">{{$page_title}}</h4>

            </div>
        </nav>
        <main>
            <div class="container">
                <div class="row py-2">
                    <div class="col-md-6">
                        <p class="mb-2">Goods In Ref. No: <strong>{{ !empty($booking_details->booking_ref_id)?$booking_details->booking_ref_id:''}}</strong></p>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-6 col-form-label">Receiving Start Date & Time</label>
                            <div class="col-sm-6">
                                <input type="text" readonly class="form-control" id="" value="{{ !empty($booking_details->start_date)?date('d/m/Y H:i',strtotime($booking_details->start_date)):''}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">Receiving Warehouse: <strong>{{ !empty($booking_details->wareHouseDetails) ? ucwords($booking_details->wareHouseDetails->name) : '-' }}</strong></p>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-6 col-form-label">Receiving Completion Date & Time</label>
                            <div class="col-sm-6">
                                <input type="text" readonly class="form-control" id="" value="{{ !empty($booking_details->completed_date)?date('d/m/Y H:i',strtotime($booking_details->completed_date)):''}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table custom-table">
                                <thead>
                                    <tr>
                                        <th class="w-30">Product <small class="d-block">Information</small></th>
                                        <th class="w-10">Quantity <small class="d-block">Ordered</small></th>
                                        <th class="w-10">Delivery Note<small class="d-block"> Quantity</small></th>
                                        <th class="w-10">Quantity <small class="d-block">Received</small></th>
                                        <th class="w-10">Differences</th>
                                        <th class="w-15">Discrepancy</th>
                                    </tr>
                                </thead>                                
                                @if(!empty($result)) 
                                @foreach($result as $row)
                                <tbody>
                                    <tr>
                                        <td>
                                            <p class="product-title mb-3">{{ !empty($row->title)?$row->title:''}}</p>
                                            <div class="d-flex mr-product-detail">
                                                <div class="product-tags">
                                                    @if($row->is_listed_on_magento == '0')
                                                    <p class="tag new">New Product</p>
                                                    @endif      
                                                    <!-- <p class="tag master">Master Product</p> -->
                                                </div>                
                                                @php 
                                                if(empty($row->main_image_internal) || !file_exists($row->main_image_internal))
                                                {
                                                    $main_image=$row->main_image_internal_thumb;
                                                }
                                                else
                                                {
                                                    $main_image=$row->main_image_internal;
                                                }

                                                @endphp


                                                <a href="{{ $main_image }}" data-rel="lightcase">
                                                    <img src="{{ url('/img/img-loading.gif') }}" data-original="{{ $row->main_image_internal_thumb }}" width="80" height="80" alt="">
                                                </a>
                                                @php
                                                    $barcode = "";

                                                    if(!empty($row->booking_barcode))
                                                    {
                                                        $barcode = $row->booking_barcode;
                                                    }
                                                    elseif(!empty($row->barcode))
                                                    {
                                                        $barcode = $row->barcode;
                                                    }
                                                @endphp
                                                <div class="ml-2">
                                                    <div class="group-item mt-2">
                                                        <p class="title mb-2">Barcode: {{ $barcode }}</p>
                                                        <p class="font-16 bold mb-2">Supplier SKU: {{ !empty($row->supplier_sku)?$row->supplier_sku:'' }}</p>
                                                        <p class="font-16 bold mb-2">@lang('messages.material_receipt.po_number'): {{ !empty($row->po_number)?$row->po_number:'' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="bold">{{ !empty($row->booking_total_quantity)?$row->booking_total_quantity:'' }}</p>
                                        </td>
                                        <td>{{ !empty($row->delivery_note_qty) ? $row->delivery_note_qty : $row->booking_total_quantity }}</td>
                                        <td>{{ !empty($row->qty_received) ? $row->qty_received : '' }}</td>
                                        @php
                                            $diff_class = "";

                                            if(!empty($row->difference))
                                            {
                                                if($row->difference > 0)
                                                {
                                                    $diff_class = "diff-plus";
                                                }

                                                if($row->difference < 0)
                                                {
                                                    $diff_class = "diff-minus";
                                                }
                                            }
                                        @endphp
                                        <td>
                                            <span class=" bold {{ $diff_class }}">{{ !empty($row->difference) ? $row->difference : 0 }}</span>
                                        </td>
                                        @php
                                        $discrepancy_type=config('params.discrepancy_type');
                                        @endphp
                                        @if(isset($final_desc[$row->booking_po_product_id]) && !empty($final_desc[$row->booking_po_product_id]) && !empty($discrepancy_type))
                                        <td>
                                            @foreach($final_desc[$row->booking_po_product_id] as $desc)
                                                @php
                                                
                                                $disc_status_type='';
                                                if(!empty($desc['status']))
                                                {
                                                    $disc_status_type=discrepancy_status_type($desc['status']);
                                                }

                                                @endphp
                                                <p class="m-0"><span class="bold">{{ $desc['qty'] }} {{ $discrepancy_type[$desc['discrepancy_type']] }}</span></p>

                                                @php
                                                $desc_image_url=explode('||',$desc->desc_image_url);
                                                @endphp
                                                @if(!empty($desc_image_url))                    
                                                <div class="d-flex">
                                                    @foreach($desc_image_url as $row1)
                                                    @php
                                                    $link_array = explode('/',$row1);
                                                    $last_part = end($link_array);
                                                    $prelink=str_replace($last_part,'',$row1);
                                                    $thumb_link=$prelink.'thumbnail/'.$last_part;
                                                    $parent_link='';
                                                    $child_link='';                            
                                                    if(!is_null($thumb_link) && file_exists(Storage::path($thumb_link)))
                                                    {
                                                        $child_link=$thumb_link;
                                                        $parent_link=$row1;
                                                    }
                                                    else
                                                    {
                                                        $child_link=$row1;
                                                        $parent_link=$row1;
                                                    }
                                                    @endphp
                                                    @if(!is_null($parent_link) && file_exists(Storage::path($parent_link)))
                                                    <a href="{{ asset('storage/uploads/'.$parent_link) }}" data-rel="lightcase" data-rel="lightcase:myCollection_{{$desc->id}}">
                                                    <img class="mr-2" src="{{ asset('storage/uploads/'.$child_link) }}" width="40" height="40" alt=""></a>
                                                    @endif
                                                    @endforeach
                                                </div>
                                                @endif
                                            @endforeach
                                        </td>
                                        @endif  
                                    </tr>
                                    <!-- <tr>
                                        <td colspan="3" class="p-0 border-none">
                                            <table class="table small-table" border="0">
                                                <thead>
                                                    <tr>
                                                        <th width="25%">Barcode</th>
                                                        <th>Case</th>
                                                        <th>Qty/Box</th>
                                                        <th>No. of Boxes</th>
                                                        <th class="text-right">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1225544544444</td>
                                                        <td>Outer Case</td>
                                                        <td>40</td>
                                                        <td>9</td>
                                                        <td align="right">360</td>
                                                    </tr>
                                                    <tr>                                                        
                                                        <td align="right" colspan="5"><span class="bold">360</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td class="p-0 border-none"></td>
                                        <td colspan="2" class="p-0 border-none">
                                            <table class="table small-table" border="0">
                                                <tr>
                                                    <td><span class="bold">Variant:</span> Color - Red | Size - Small</td>
                                                </tr>
                                            </table>                                            
                                        </td>
                                    </tr> -->
                                </tbody>                 
                                @endforeach               
                                @endif
                                

                                <!-- <tbody>
                                    <tr>
                                        <td>
                                            <p class="product-title mb-3">Mens Novelty Christmas Socks All I Want For Christmas</p>
                                            <div class="d-flex mr-product-detail">
                                                <div class="product-tags">
                                                    <p class="tag new">New Product</p>
                                                    <p class="tag master">Master Product</p>
                                                </div>
                                                <img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" width="80" height="80" alt="">
                                                <div class="ml-2">
                                                    <div class="group-item mt-2">
                                                        <p class="title mb-2">Barcode: BRC00221211</p>
                                                        <p class="font-16 bold mb-2">Supplier SKU: SKU0001454</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="bold">600</p>
                                        </td>
                                        <td>500</td>
                                        <td>496</td>
                                        <td>
                                            <span class="bold diff-minus">-55</span>
                                        </td>
                                        <td>
                                            <p class="m-0"><span class="bold">32 Damaged</span></p>
                                            <div class="d-flex">
                                                <img class="mr-2" src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" width="40" height="40" alt="">
                                                <img class="mr-2" src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" width="40" height="40" alt="">
                                                <img class="mr-2" src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" width="40" height="40" alt="">
                                            </div>
                                            <p class="m-0 mt-2"><span class="bold">23 Shortage</span></p>
                                        </td>
                                    </tr>
                                </tbody> -->
                                
                                <!-- <tbody>
                                    <tr>
                                        <td>
                                            <p class="product-title mb-3">Mens Novelty Christmas Socks All I Want For Christmas</p>
                                            <div class="d-flex mr-product-detail">
                                                <div class="product-tags">
                                                    <p class="tag new">New Product</p>
                                                    <p class="tag master">Master Product</p>
                                                </div>
                                                <img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" width="80" height="80" alt="">
                                                <div class="ml-2">
                                                    <div class="group-item mt-2">
                                                        <p class="title mb-2">Barcode: BRC00221211</p>
                                                        <p class="font-16 bold mb-2">Supplier SKU: SKU0001454</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="bold">600</p>
                                        </td>
                                        <td>500</td>
                                        <td>496</td>
                                        <td>
                                            <span class=" bold diff-minus">-55</span>
                                        </td>
                                        <td>
                                            <p class="m-0"><span class="bold">32 Damaged</span></p>
                                            <p class="m-0 mt-2"><span class="bold">23 Shortage</span></p>
                                            <p class="m-0 mt-2"><span class="bold">Lorem ipsum.</span></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="border-none"><span class="bold">Variant:</span> Color - Red | Size - Small</td>
                                    </tr>
                                </tbody> -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script> -->
    
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> -->
    <script src="{{ asset('js/jquery-2.2.4.min.js')}}" ></script>
    <script src="{{ asset('js/popper.js')}}"></script>
    <script src="{{ asset('js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('js/lightcase.js') }}"></script>    
    <script src="{{ asset('js/lazyload.js')}}"></script>
    <script type="text/javascript">
    $(document).ready(function ()
    { 
        $('a[data-rel^=lightcase]').lightcase();
        $("img[data-original]").lazyload();
    });

    function page_update()
    {
        set_query_para("filter_by_po", $('select[name="filter_by_po"]').val());
        location.reload(true);
    }

    function set_query_para($key,$data)
    {
        var url_string = window.location.protocol+"://"+window.location.hostname+window.location.port+window.location.pathname;
        var url_string = "";
        var search = ltrim(window.location.search,"?")
        var search_join = [];
        var $target_found = false;
        search_split = search.split("&");
        if(search!="")
        {
            $.each(search_split,function($index,$value)
            {
                $value_split = $value.split("=");
                if($value_split.length=2)
                {
                    if($value_split[0]==$key)
                    {
                        $value_split[1] = $data
                        $target_found = true;
                    }
                }
                
                $value_join = $value_split.join("=");
                
                search_join.push($value_join);
          });
        }
        
        if($target_found==false)
        {
          search_join.push($key+"="+$data)
        }
        
        url_string  +=("?"+(search_join.join("&")));
        
        history.pushState(null,null,url_string);
    }

    function ltrim(str, characters) {
        var nativeTrimLeft = String.prototype.trimLeft;
        str = makeString(str);
        if (!characters && nativeTrimLeft) return nativeTrimLeft.call(str);
        characters = defaultToWhiteSpace(characters);
        return str.replace(new RegExp('^' + characters + '+'), '');
    }

    function makeString(object)
    {
        if (object == null) return '';
        return String(object);
    }

    function defaultToWhiteSpace(characters) {
        if (characters == null)
        return '\\s';
        else if (characters.source)
        return characters.source;
        else
        return '[' + escapeRegExp(characters) + ']';
    }  
    function escapeRegExp(str) {
        return makeString(str).replace(/([.*+?^=!:${}()|[\]\/\\])/g, '\\$1');
    }


</script>
</html>
