@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{asset('js/plugins/bootstrap-imageupload/css/bootstrap-imageupload.min.css')}}">
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url({{asset('media/photos/Food4.jpg')}});">
        <div class="bg-black-50">
            <div class="content content-full">
                <h1 class="font-size-h2 text-white my-2">
                    <i class="fa fa-plus text-white-50 mr-1"></i> New Product
                </h1>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <div class="block block-rounded block-bordered">
            <div class="block-content">

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif

                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{url('products/'.$customer_id.'/add')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Vital Info -->
                    <h2 class="content-heading pt-0">Vital Info</h2>
                    <div class="row push">
                        <div class="col-lg-4">
                            <p class="text-muted">
                                Some vital information about product
                            </p>
                        </div>
                        <div class="col-lg-8 col-xl-5">
                            <div class="form-group">
                                <label>
                                    Product Name (English) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="product-name" placeholder="eg: Pizza">
                            </div>
                            <div class="form-group">
                                <label>
                                    Product Name (Other language)
                                </label>
                                <div class="custom-control custom-checkbox custom-control-inline custom-control-primary mb-1">
                                    <input type="checkbox" class="custom-control-input" id="checkbox-name-rtl" >
                                    <label class="custom-control-label" for="checkbox-name-rtl">RTL?</label>
                                </div>
                                <input type="text" class="form-control" name="product-name-ar" placeholder="eg: Pizza">
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-8">
                                    <label for="dm-project-new-category">
                                        Image <span class="text-danger">*</span>
                                    </label>
                                    <!-- bootstrap-imageupload. -->
                                    <div class="imageupload panel panel-default">
                                        <div class="file-tab panel-body">
                                            <label class="btn btn-primary btn-file" style="margin-bottom: 0px;">
                                                <span>Browse</span>
                                                <!-- The file is stored here. -->
                                                <input type="file" name="image">
                                            </label>
                                            <button type="button" class="btn btn-danger">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>
                                    Youtube video URL
                                </label>
                                <input type="text" class="form-control" name="video-url" placeholder="https://www.youtube.com/watch?v=GLSG_Wh_YWc">
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-8">
                                    <label for="dm-project-new-name">
                                        Category <span class="text-danger">*</span>
                                    </label>
                                    <select class="custom-select" name="category">
                                        <option value="0" disabled="disabled" selected>Select a category</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-8">
                                    <label for="dm-project-new-name">
                                        Price <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control text-center" name="product-price" placeholder="15.233">
                                        <div class="input-group-append">
                                            <select class="custom-select" name="currency" style="border-radius: 0px 4px 4px 0px;">
                                                <option value="0" disabled="disabled" selected>Currency</option>
                                                @foreach($currency_list as $currency)
                                                    <option value="{{$currency->id}}">{{$currency->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- END Vital Info -->

                    <h2 class="content-heading pt-0">Optional Info</h2>
                    <div class="row push">
                        <div class="col-lg-4">
                            <p class="text-muted">
                                You can add more details if you like but it is not required
                            </p>
                        </div>
                        <div class="col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="dm-project-edit-description">Description (English)</label>
                                <textarea class="form-control" name="product-description" rows="6"
                                          placeholder="Product Description Here..."></textarea>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="dm-project-edit-description">Description (Other language)</label>
                                <div class="custom-control custom-checkbox custom-control-inline custom-control-primary">
                                    <input type="checkbox" class="custom-control-input" id="checkbox-description-rtl">
                                    <label class="custom-control-label" for="checkbox-description-rtl">RTL?</label>
                                </div>
                                <textarea class="form-control" name="product-description-ar" rows="6"
                                          placeholder="Product Description Here..."></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Submit -->
                    <div class="row push">
                        <div class="col-lg-8 col-xl-5 offset-lg-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-check-circle mr-1"></i> Add New Product
                                </button>
                                <a class="btn btn-warning" href="{{url('/products').'/'.$customer_id}}">
                                    <i class="fa fa-times-circle mr-1"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- END Submit -->
                </form>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{asset('js/plugins/bootstrap-imageupload/js/bootstrap-imageupload.min.js')}}"></script>

    <!-- Page JS Code -->
    <script>
        var $imageupload = $('.imageupload');
        $imageupload.imageupload();

        $(document).ready(()=>{

            $("#checkbox-name-rtl").on("change", () => {
                if ($("#checkbox-name-rtl").prop("checked") == true) {
                    $("[name='product-name-ar']").attr("dir", "rtl");
                } else {
                    $("[name='product-name-ar']").removeAttr("dir");
                }
            });

            $("#checkbox-description-rtl").on("change", () => {
                if ($("#checkbox-description-rtl").prop("checked") == true) {
                    $("[name='product-description-ar']").attr("dir", "rtl");
                } else {
                    $("[name='product-description-ar']").removeAttr("dir");
                }
            });

        });
    </script>
@endsection
