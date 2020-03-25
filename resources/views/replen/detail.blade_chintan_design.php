@extends('layouts.app')
@section('content')
@section('title',__('messages.totes.totes_add'))

<div class="content-card custom-scroll">
   <div class="content-card-header">
        <h3 class="page-title">ACAB - Quarantine Location</h3>		
        <div class="right-items">
            <a href="{{route('totes.index')}}" class="btn btn-blue btn-header px-4" title="@lang('messages.modules.button_cancel')">
                View Job List
            </a>
            <button class="btn btn-danger btn-header px-4"  title="@lang('messages.modules.button_save')" form="create-totes-form">Finish</button>
           
        </div>					
    </div>	
    <div class="card-flex-container">
        <div class="container-fluid">
        <!-- <table class="table custom-table">
            <thead>
                <tr>
                    <th width="28%" rowspan="2" align="left" valign="middle">Product Information</th>
                    <th width="12%" rowspan="2" align="left" valign="middle">Priority</th>
                    <th width="12%" rowspan="2" align="left" valign="middle">Replen Pending Quality</th>
                    <th colspan="4" align="center">Current Stronge Details</th>
                  </tr>
                  <tr>
                    <th width="15%">Locations</th>
                    <th width="10%">Quantity</th>
                    <th width="10%">Availible Space Qty.</th>
                    <th width="155">Best Before Date</th>
                  </tr>
            </thead>
          
          <tr>
            <td colspan="3" align="left" valign="middle">Product Title : Cricket Balls</td>
            <td colspan="4">&nbsp;</td>
          </tr>
          <tr>
            <td rowspan="3" align="left" valign="middle">
                <p>Barcode: 1133565656</p>
                <p>SKU: PS1234</p>
            </td>
            <td align="left" valign="middle">Priority 1</td>
            <td align="left" valign="middle">
                <span class="font-14-dark bold d-inline-block p-2 alert alert-warning">280</span>
            </td>
            <td><span class="font-14-dark bold d-inline-block p-2 alert alert-warning">A03G02</span></td>
            <td>200</td>
            <td>&nbsp;</td>
            <td>10-Jan-2010</td>
          </tr>
        </table> -->

        <div class="d-flex mb-3" style="border: 1px solid #dee2e6">
                <table class="table tbl-replane-product mb-0">
                    <thead>
                        <tr>
                            <th style="height: 56px;">Product Information</th>
                            <th>Priority</th>
                            <th>Replen Pending Quantity</th>
                        </tr>                       
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3">Product Title: <span class="bold">Cricket Balls</span></td>
                        </tr>
                        <tr>                            
                            <td rowspan="3" class="cell-border-right cell-border-bottom">
                                <div class="d-flex align-items-center">
                                    <img src="https://methodhome.com/wp-content/uploads/laundry_p-v2-500x500.png" width="60" alt="">
                                    <div class="pl-2">
                                        <p class="mb-3">Barcode: <span>12345678911</span></p>
                                        <p>SKU: <span>12345678911</span></p>
                                    </div>    
                                </div>
                                
                                
                            </td>
                            <td>Priority 1</td>
                            <td class="highlighted-text">280</td>
                        </tr>
                        <tr>                            
                            
                            <td class="bold">Pick</td>
                            <td>50</td>
                        </tr>
                        <tr>                            
                            
                            <td class="bold cell-border-bottom">Dropship</td>
                            <td class="cell-border-bottom">100</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table tbl-replane-storage mb-0">
                    <thead>
                        <tr>
                            <th colspan="4">Current Storage Details</th>
                        </tr>
                        <tr>
                            <th>Locations</th>
                            <th>Quantity</th>
                            <th>Available Space Qty.</th>
                            <th>Best Before Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="highlighted-text">A03 G02 - Bulk</td>
                            <td>200</span></td>
                            <td></td>
                            <td>10-Jan-2020</td>
                        </tr>
                        <tr>
                            <td>A03 G02 - Bulk</td>
                            <td>200</span></td>
                            <td>62</td>
                            <td>10-Jan-2020</td>
                        </tr>
                        <tr>
                            <td>A03 G02 - Bulk</td>
                            <td>200</span></td>
                            <td>62</td>
                            <td>10-Jan-2020</td>
                        </tr>
                        <tr>
                            <td>A03 G02 - Bulk</td>
                            <td>200</span></td>
                            <td></td>
                            <td>10-Jan-2020</td>
                        </tr>
                    </tbody>
                </table>            
        </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">                    
                    <label class="font-14-dark mb-2">Scan Bulk Location</label>
                    <input type="text" class="form-control" name="">
                </div>                            
            </div>
            <div class="col-lg-4">
                 <div class="form-group">                    
                    <label class="font-14-dark mb-2">Scan Product/Case Barcode</label>
                    <input type="text" class="form-control" name="">
                </div>    
             </div>
        </div>
        
        <table class="table custom-table bordered-tbl">
            <thead>
                <tr>
                    <th></th>
                    <th>Barcode</th>
                    <th>Case/Loose</th>
                    <th>Qty/Box</th>
                    <th>No. of Box</th>
                    <th>Total Quantity</th>
                    <th>Best Before Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <label class="fancy-radio">
                            <input type="radio" name="barcode1" />
                            <span><i></i></span>
                        </label>
                    </td>
                    <td>12345678911</td>
                    <td>Singles</td>
                    <td>100</td>
                    <td>1</td>
                    <td>100</td>
                    <td>25-Jan-2020</td>
                </tr>
                <tr>
                    <td>
                        <label class="fancy-radio">
                            <input type="radio" name="barcode1" />
                            <span><i></i></span>
                        </label>
                    </td>
                    <td>12345678911</td>
                    <td>Singles</td>
                    <td>100</td>
                    <td>1</td>
                    <td>100</td>
                    <td>25-Jan-2020</td>
                </tr>
            </tbody>
        </table>
        <div class="row mt-5">
            <div class="col-lg-2">
                <div class="form-group">                    
                    <label class="font-14-dark mb-2">Boxes Picked</label>
                    <input type="text" class="form-control" name="">
                </div>                            
            </div>
            <div class="col-lg-2">
                 <div class="form-group">                    
                    <label class="font-14-dark mb-2">Total Quantity</label>
                    <p class="font-14-dark bold mt-3">50</p>
                </div>    
             </div>
             <div class="col-lg-12">
                 <button class="btn btn-blue mr-2 px-4">Moved</button>
                 <button class="btn btn-green">Stock Check Required</button>
             </div>
        </div>
        </div>
	</div>
</div>
@endsection