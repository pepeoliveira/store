@extends('layouts.adminLayout.admin_design')
@section('content')

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="{{ url('admin/dashboard') }}" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#">Products</a> <a href="#" class="current">Add Attributes</a> </div>
            <h1>Products Attributes</h1>
            @if(Session::has('flash_message_error'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>   {!! session('flash_message_error') !!}</strong>
                </div>
            @endif
            @if(Session::has('flash_message_success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>   {!! session('flash_message_success') !!}</strong>
                </div>
            @endif
        </div>
        <div class="container-fluid"><hr>
            <div class="row-fluid">
                <div class="span12">
                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
                            <h5>Add Product Attributes</h5>
                        </div>
                        <div class="widget-content nopadding">
                            <form enctype="multipart/form-data" class="form-horizontal" method="post" action="{{ url('/admin/add-attributes/'.$productDetails->id) }}" name="add_attribute" id="add_attribute" novalidate="novalidate">{{ csrf_field() }}
                                <label type="hidden" value="{{$productDetails->id}}" name="product_id"></label>
                                <div class="control-group">
                                    <label class="control-label" ><span style="float: left">Product Name</span></label>
                                    <label class="control-label ml-5"><strong style="float: left; margin-left: 5px;">{{ $productDetails->product_name }}</strong></label>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Product Code</label>
                                    <label class="control-label"><strong style="float: left">{{ $productDetails->product_code }}</strong></label>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Product Color</label>
                                    <label class="control-label"><strong style="float: left">{{ $productDetails->product_color }}</strong></label>
                                </div>
                                <div class="control-group">
                                    <label class="control-label"></label>
                                    <div class="field_wrapper">
                                        <div>
                                            <input type="text" name="sku[]" id="sku" placeholder="SKU" style="width: 120px;"/>
                                            <input type="text" name="size[]" id="size" placeholder="size" style="width: 120px;"/>
                                            <input type="text" name="price[]" id="price" placeholder="price" style="width: 120px;"/>
                                            <input type="text" name="stock[]" id="stock" placeholder="stock" style="width: 120px;"/>
                                            <a href="javascript:void(0);" class="add_button" title="Add field">Add</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <input type="submit" value="Add Attributes" class="btn btn-success">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
                            <h5>View Attributes</h5>
                        </div>
                        <div class="widget-content nopadding">
                            <table class="table table-bordered data-table">
                                <thead>
                                <tr>
                                    <th style="font-size: 12px">ATTRIBUTE ID</th>
                                    <th style="font-size: 12px">SKU</th>
                                    <th style="font-size: 12px">SIZE</th>
                                    <th style="font-size: 12px">PRICE</th>
                                    <th style="font-size: 12px">STOCK</th>
                                    <th style="font-size: 12px">ACTIONS </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($productDetails['attributes'] as $attribute)
                                    <tr class="gradeX">
                                        <td>{{ $attribute->id }}</td>
                                        <td>{{ $attribute->sku }}</td>
                                        <td>{{ $attribute->size }}</td>
                                        <td>{{ $attribute->price }} €</td>
                                        <td>{{ $attribute->stock }}</td>
                                        <td class="text-center">
{{--                                        <a id="delAtt" href="{{ url('admin/delete-product/'.$attribute->id) }}" class="btn btn-danger btn-mini">Delete</a>--}}
                                            <a href="{{url('admin/delete-attribute/'.$attribute->id)}}:" rel="{{$attribute->id}}" rel1="delete-attribute" class="deleteAtt btn btn-danger btn-mini">Delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
