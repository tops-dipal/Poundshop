/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var dataTableId = 'carton_table';
    var poundShopCartons = function ()
    {
        $(document).ready(function ()
        {
           
        });
    };

    //on scan search product details and their locations

     $('#scan_product_barcode').on('input propertychange',function(event)
    {

        $.ajax({
                url: BASE_URL + 'api-product-locations-detail-by-barcode/',
                type: "GET",
                data:{'barcode':$(this).val()},
                headers: {
                   Authorization: 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                success: function (response) {
                     $("#page-loader").hide();
                     if(response.status_code==200)
                     {
                        bindProductDetail(response.data.productLocationsDetails);
                     }
                    console.log(response);return false;
                },
                error: function (xhr, err) {
                   $("#page-loader").hide();
                   PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }

            });
    });

     //location type by scan location

     $('#scan_from_loc').on('input propertychange',function(event)
    {
        $('#from_location_type').html('');
        $.ajax({
            url: BASE_URL + 'api-location-auto-suggest-on-input',
            type: "GET",
            data:{'keyword':$(this).val(),'module':'move-products'},
            headers: {
               Authorization: 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                
            },
            success: function (response) {
                var responseDecode=jQuery.parseJSON(response)
                 if(responseDecode.length>0)
                 {
                    
                    $('#from_location_type').html(responseDecode[0].location_type);
                 }
                 else
                 {
                     PoundShopApp.commonClass._displayErrorMessage('No any location availabe in our system which you scanned');
                 }
                
            },
            error: function (xhr, err) {
               $("#page-loader").hide();
               PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }

        });
    });

     bindProductDetail=function(data){
        var productDetailHtml='';
        var productLocationData='';
        $('.prouctInfo').html('');
        $('.currentProductLocations').html('');
        if(data==null)
        {
            productDetailHtml+='No Record Found';
            $('.qtyMove').hide();
            
        }
        else
        {
            productDetailHtml+=`<table class="table border-less display">
                                  <thead>
                                      <tr>
                                        <th colspan="2"><center>Product Details</center></th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <tr>
                                      <td rowspan="3"><img src="`+data.main_image_internal_thumb+`"  height="200"></td>
                                        <td>Product Name: `+data.title+`</td>
                                      
                                      </tr>
                                      <tr>
                                        <td>Our SKU: `+data.sku+`</td>
                                       
                                      </tr>
                                      <tr>
                                        <td>Supplier SKU: `+data.sku+`</td>
                                       
                                      </tr>
                                    </tbody>
                                </table>`;
            productLocationData=bindProductCurrentLocations(data.location_assign);
            $('.qtyMove').show();
        }
        $('.prouctInfo').html(productDetailHtml);
        $('.currentProductLocations').html(productLocationData);
     }
    


    bindProductCurrentLocations= function(productLocations){
        var tableData='';
        if(productLocations.length>0)
        {
            tableData+=`<table class="table border-less display">
            <thead>
                <tr>
                    <th>Aisle</th>
                    <th>Current Locations</th>
                    <th>Quantity</th>
                    <th>Available Space</th>
                </tr></thead><tbody>`;
            for(i=0;i<productLocations.length;i++){
                tableData+=   `<tr>
                                    <td>`+productLocations[i].aisle+`</td>
                                    <td>`+productLocations[i].location+`</td>  `;
                if(productLocations[i].total_qty==null)
               {
                tableData+= `<td>0</td>`;
               }
               else
               {
                tableData+= `<td>`+productLocations[i].total_qty+`</td>`;
               }
                tableData+= `<td>`+productLocations[i].available_space+`</td>
                </tr>`;
            }

            tableData+=  `</tbody></table>`;
            return tableData;
        }
        else
        {
            return '';
        }
    }
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);


