@extends('layouts.app')
@section('title','Utility')
@section('content')
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">Utility</h3>
        <div class="right-items">
        </div>
    </div>
    
    <div class="card-flex-container">
        <div class="container-fluid">
            <div class="text-right">
                <h3 class="p-title mb-2">More Option Variation</h3>
                <div class="dropdown more-action-dropdown">
                    <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="">
                    <span class="icon-moon icon-Drop-Down-1"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                        <h4 class="title">More Actions</h4>
                        
                        <div class="item-actions">
                            <button title="Add Product" class="btn btn-add" id="show-modal-btn">
                            <span class="icon-moon yellow icon-Add"></span>Add
                            </button>
                            
                            <button title="Revise P.O" class="btn btn-add" id="revise-po-btn">
                            <span class="icon-moon red icon-Reverse-Purchse-Order"></span>Revise P.O
                            </button>
                            <button title="Download PO" class="btn btn-add delete-many">
                            <span class="icon-moon yellow icon-Download"></span>Download
                            </button>
                            <button title="Send Mail" class="btn btn-add delete-many">
                            <span class="icon-moon gray icon-Mail"></span>Send Mail
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <h3 class="p-title mb-2">Header Button Variation</h3>
            <div class="right-items">
                <a class="btn btn-add btn-light-green btn-header" href="#" title="Add Supplier">
                    <span class="icon-moon icon-Add"></span>
                </a>
                <a href="#" class="btn btn-gray btn-header px-4">Cancel</a>
                <button type="submit" form="" class="btn btn-blue btn-header px-4">Save</button>
                <a href="#" class="btn btn-red btn-header px-4">Delete</a>
            </div>
            <h3 class="p-title mb-2 mt-4">Bulk Option Variation</h3>
            <div class="dropdown bulk-action-dropdown">
                <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="icon-moon icon-Drop-Down-1"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                    <h4 class="title">Bulk action</h4>
                    <button class="btn btn-add delete-many">
                    <span class="icon-moon red icon-Delete"></span>
                    Delete Cartons
                    </button>
                </div>
            </div>
            <h3 class="p-title mb-2 mt-4">Check Box without text</h3>
            <label class="fancy-checkbox">
                <input type="checkbox" name="" />
                <span><i></i></span>
            </label>
            <h3 class="p-title mb-2 mt-4">Check Box with text</h3>
            <label class="fancy-checkbox">
                <input type="checkbox" name="" />
                <span><i></i> I Agree.</span>
            </label>
            <h3 class="p-title mb-2 mt-4">Radio</h3>
            <label class="fancy-radio">
                <input type="radio" name="bool" />
                <span><i></i>Yes</span>
            </label>
            <label class="fancy-radio">
                <input type="radio" name="bool" />
                <span><i></i>No</span>
            </label>
            <h3 class="p-title mb-2 mt-4">Input File</h3>
            <div class="row">
                <div class="col-lg-6">
                    <div class="fancy-file">
                        <input type="file" name="file-7[]" id="file-7" class="inputfile-custom" data-multiple-caption="{count} files selected" multiple />
                        <label for="file-7"><span></span> <strong>Choose a file</strong></label>
                    </div>
                </div>
            </div>
            <h3 class="p-title mb-2 mt-4">Confirm & Alert Box</h3>
            <button class="btn btn-blue btn-multi">Confirm</button>
            <button class="btn btn-green btn-single">Alert</button>
            <h3 class="p-title mb-2 mt-4">How to use Icons </h3>
            <p class="mb-4">Copy icon name from this <a href="http://192.168.4.97/poundshop/Icons/demo.html" target="_blank">URL</a></p>
            
            &lt;span class=&quot;icon-moon size-sm icon-Active&quot;&gt;&lt;/span&gt;
            
            <p class="mb-3 mt-4">in Above example icon name is "icon-Active" and it will look like below icon:</p>
            <p class="mb-3 mt-4">Icon size class:</p>
            <ul class="mb-4">
                <li>size-sm</li>
                <li>size-md</li>
                <li>size-lg</li>
            </ul>
            <span class="icon-moon size-sm icon-Active"></span>
            <span class="icon-moon size-md icon-Active"></span>
            <span class="icon-moon size-lg icon-Active"></span>
            
            <h3 class="p-title mb-2 mt-4">Form Input</h3>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Input Text<span class="asterisk">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="" placeholder="" name="name">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Select<span class="asterisk">*</span></label>
                        <div class="col-lg-9">
                            <select class="form-control control-loading" disabled>
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group row">
                        <label class="col-lg-12 col-form-label">Notes<span class="asterisk">*</span></label>
                        <div class="col-lg-12">
                            <textarea class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <h3 class="p-title mb-2 mt-4">Search Select</h3>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Select<span class="asterisk">*</span></label>
                        <div class="col-lg-9">
                            <select class="form-control custom-select-search">
                                <option>India</option>
                                <option>Africa</option>
                                <option>USA</option>
                                <option>UAE</option>
                                <option>Canada</option>
                                <option>London</option>
                                <option>India</option>
                                <option>Africa</option>
                                <option>USA</option>
                                <option>UAE</option>
                                <option>Canada</option>
                                <option>London</option>
                                <option>India</option>
                                <option>Africa</option>
                                <option>USA</option>
                                <option>UAE</option>
                                <option>Canada</option>
                                <option>London</option>
                                <option>India</option>
                                <option>Africa</option>
                                <option>USA</option>
                                <option>UAE</option>
                                <option>Canada</option>
                                <option>London</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Tags<span class="asterisk">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" id="tag1" value="Amsterdam,Washington"  placeholder="Product Tags" data-role="tagsinput" />
                        </div>
                    </div>
                </div>
            </div>


            <!-- <h3 class="p-title mb-2 mt-4">Content Loader</h3>
            <div class="p-3 content-loader" style="width: 400px; border: 1px solid #888;">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate minima numquam, quidem quia illum! Itaque ducimus nisi adipisci delectus. Ipsam!</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate minima numquam, quidem quia illum! Itaque ducimus nisi adipisci delectus. Ipsam!</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate minima numquam, quidem quia illum! Itaque ducimus nisi adipisci delectus. Ipsam!</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate minima numquam, quidem quia illum! Itaque ducimus nisi adipisci delectus. Ipsam!</p>
            </div> -->

        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/utility/index.js')}}"></script>
@endsection