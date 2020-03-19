@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))
@section('content')
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">Select Catogery</h3>
        <div class="right-items">
            <button class="btn btn-add btn-blue"><span class="icon-moon icon-Search"></span>Find Category</button>          
        </div>
    </div>
    <div class="card-flex-container">
        <!-- <form action="{{route('product.store')}}" method="POST" class="form-horizontal form-flex">
            @csrf
            <div class="form-fields">
                <div class="container-fluid">
                    
                </div>
            </div>
            <div class="content-card-footer">
                <div class="button-container">
                    <button type="submit" class="btn btn-green btn-form">Submit</button>
                    <a href="{{route('product.index')}}" class="btn btn-gray btn-form">Cancel</a>
                </div>
            </div>
        </form> -->
        <div class="category-list-holder">
            <div class="category-level">
                <ul>
                    <li>
                        <a href="javascript:void(0)" class="active">
                            Appliances
                            <!----------------------------------------------------------
                                Span Display Conditionally (If Sub Category is Available) 
                                ------------------------------------------------------------->
                            <span class="icon-moon icon-Drop-Down-1"></span>
                        </a>
                    </li>
                    <li><a href="javascript:void(0)">Arts, Crafts & Sewing </a></li>
                    <li><a href="javascript:void(0)">Automotive </a></li>
                    <li><a href="javascript:void(0)">Baby Products </a></li>
                    <li><a href="javascript:void(0)">Beauty & Personal Care </a></li>
                    <li><a href="javascript:void(0)">Books</a></li>
                    <li><a href="javascript:void(0)">CDs & Vinyl </a></li>
                    <li><a href="javascript:void(0)">Cell Phones & Accessories<span class="icon-moon icon-Drop-Down-1"></span></a></li>
                    <li><a href="javascript:void(0)">Clothing, Shoes & Jewelry </a></li>
                    <li><a href="javascript:void(0)">Collectibles & Fine Art </a></li>
                    <li><a href="javascript:void(0)">Electronics </a></li>
                    <li><a href="javascript:void(0)">Grocery & Gourmet Food </a></li>
                    <li><a href="javascript:void(0)">CDs & Vinyl </a></li>
                    <li><a href="javascript:void(0)">Cell Phones & Accessories </a></li>
                    <li><a href="javascript:void(0)">Clothing, Shoes & Jewelry </a></li>
                </ul>
            </div>
             <div class="category-level">
                <ul>
                    <li><a href="javascript:void(0)">Option 1</a></li>
                    <li><a href="javascript:void(0)">Option 2</a></li>
                    <li><a href="javascript:void(0)">Option 3</a></li>
                    <li><a href="javascript:void(0)" class="active">Option 4</a></li>
                    <li><a href="javascript:void(0)">Option 5</a></li>
                    <li><a href="javascript:void(0)">Option 6</a></li>
                    <li><a href="javascript:void(0)">Option 7</a></li>
                </ul>
            </div>
             <div class="category-level">
                <ul>
                    <li><a href="javascript:void(0)">Option 1</a></li>
                    <li><a href="javascript:void(0)">Option 2</a></li>
                    <li><a href="javascript:void(0)">Option 3</a></li>
                    <li><a href="javascript:void(0)">Option 4</a></li>
                    <li><a href="javascript:void(0)">Option 5</a></li>
                    <li><a href="javascript:void(0)">Option 6</a></li>
                    <li><a href="javascript:void(0)">Option 7</a></li>
                    <li><a href="javascript:void(0)">Option 8</a></li>
                    <li><a href="javascript:void(0)" class="active">Option 9</a></li>
                    <li><a href="javascript:void(0)">Option 10</a></li>
                    <li><a href="javascript:void(0)">Option 11</a></li>
                    <li><a href="javascript:void(0)">Option 12</a></li>
                </ul>
            </div>
             <div class="category-level">
                <ul>
                    <li><a href="javascript:void(0)">Option 1</a></li>
                    <li><a href="javascript:void(0)">Option 2</a></li>
                    <li><a href="javascript:void(0)">Option 3</a></li>
                    <li><a href="javascript:void(0)" class="active">Option 4</a></li>
                    <li><a href="javascript:void(0)">Option 5</a></li>
                    <li><a href="javascript:void(0)">Option 6</a></li>
                    <li><a href="javascript:void(0)">Option 7</a></li>
                    <li><a href="javascript:void(0)">Option 8</a></li>
                    <li><a href="javascript:void(0)">Option 9</a></li>
                </ul>
            </div>
             <div class="category-level">
                <ul>
                    <li><a href="javascript:void(0)">Option 1</a></li>
                    <li><a href="javascript:void(0)">Option 2</a></li>
                    <li><a href="javascript:void(0)">Option 3</a></li>
                    <li><a href="javascript:void(0)">Option 4</a></li>
                    <li><a href="javascript:void(0)">Option 5</a></li>
                    <li><a href="javascript:void(0)"class="active">Option 6</a></li>
                    <li><a href="javascript:void(0)">Option 7</a></li>
                    <li><a href="javascript:void(0)">Option 8</a></li>
                    <li><a href="javascript:void(0)">Option 9</a></li>
                    <li><a href="javascript:void(0)">Option 10</a></li>
                </ul>
            </div>
             <div class="category-level">
                <ul>
                    <li><a href="javascript:void(0)">Option 1</a></li>
                    <li><a href="javascript:void(0)">Option 2</a></li>
                    <li><a href="javascript:void(0)">Option 3</a></li>
                    <li><a href="javascript:void(0)" class="active">Option 4</a></li>
                    <li><a href="javascript:void(0)">Option 5</a></li>

                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    
@endsection

