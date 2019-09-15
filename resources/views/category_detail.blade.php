@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{asset('js/plugins/datatables/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" href="{{asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css')}}">
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url({{asset('media/photos/Food3.jpg')}});">
        <div class="bg-black-50">
            <div class="content content-full">
                <h1 class="font-size-h2 text-white my-2">
                    <i class="si si-list text-white-50 mr-1"></i> Category Detail
                </h1>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <div class="block block-rounded block-bordered">
            <div class="block-content">

                <form action="">
                @csrf
                <!-- Vital Info -->
                    <h2 class="content-heading pt-0">Category Info</h2>
                    <div class="row push">
                        <div class="col-lg-4">
                            <div class="form-group row">
                                <div class="col-lg-8">
                                    <label for="dm-project-new-category">
                                        Image
                                    </label>
                                    <!-- bootstrap-imageupload. -->
                                    <div class="imageupload panel panel-default" style="
                                    width: 100%;
                                    padding-top: 80%;
                                    position: relative; ">
                                        <div style="position: absolute; top: 0; right: 0; left: 0; bottom: 0;">
                                            <img id="preview"
                                                 src="{{asset('media/images/categories/original').'/'.$category->picture}}"
                                                 class="image-preview-detail"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-xl-5">
                            <div class="form-group">
                                <label for="dm-project-new-name">
                                    Category Name
                                </label>
                                <input type="text" class="form-control" name="category-name" placeholder="eg: Pizza"
                                       value="{{$category->name}}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="dm-project-new-name">
                                    اسم التصنيف
                                </label>
                                <input type="text" class="form-control" name="category-name-ar" dir="rtl"
                                       placeholder="eg: Pizza" value="{{$category->name_ar}}" disabled>
                            </div>

                            <div class="form-group">
                                <label for="dm-project-new-name">
                                    Tags
                                </label>
                                <input type="text" class="form-control" name="category-tags"
                                       placeholder="eg: Tag1, Tag2, Tag3" value="{{$category->tags}}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="dm-project-new-name">
                                    بطاقة
                                </label>
                                <input type="text" class="form-control" name="category-tags-ar" dir="rtl"
                                       placeholder="eg: Tag1, Tag2, Tag3" value="{{$category->tags_ar}}" disabled>
                            </div>
                        </div>
                    </div>
                    <!-- END Vital Info -->

                    <h2 class="content-heading pt-0">Products with this category</h2>
                    <div class="row push">
                        <div class="block-content block-content-full">
                            <table
                                class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 80px;">#</th>
                                    <th class="d-none d-sm-table-cell" style="width: 100px;">Picture</th>
                                    <th class="d-none d-sm-table-cell" style="width: 20%;">Product Name</th>
                                    <th class="d-none d-sm-table-cell">Description</th>
                                    <th class="d-none d-sm-table-cell" style="width: 15%;">Price</th>
                                    @if(Session::get('user-type')==1)
                                        <th class="d-none d-sm-table-cell" style="width: 80px;">Show</th>
                                        <th class="d-none d-sm-table-cell" style="width: 80px;">Actions</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td class="text-center">{{$loop->iteration}}</td>
                                        <td class="font-w600">
                                            <div align="center">
                                                <img
                                                    src="{{asset('/media/images/products/thumbnail/').'/'.$product->picture}}"
                                                    style="width: 80px;">
                                            </div>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <a href="{{url('/products/detail/').'/'.$product->id}}">{{$product->name}}</a>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            {{$product->description}}
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <span class="badge badge-success">{{$product->price}}</span>
                                        </td>
                                        @if(Session::get('user-type')==1)
                                            <td class="text-center">
                                                <div
                                                    class="custom-control custom-switch custom-control custom-control-inline mb-2"
                                                    align="center">
                                                    <input type="checkbox" class="custom-control-input"
                                                           id="show-toggle-{{$product->id}}"
                                                           name="show-toggle-{{$product->id}}"
                                                           @if($product->show_flag == 1) checked @endif >
                                                    <label class="custom-control-label"
                                                           for="show-toggle-{{$product->id}}"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="{{url('/products/edit').'/'.$product->id}}"
                                                       class="btn btn-sm btn-primary" data-toggle="tooltip"
                                                       title="Edit">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </a>
                                                    <a href="javascript:delProduct({{$product->id}})"
                                                       class="btn btn-sm btn-primary"
                                                       data-toggle="tooltip" title="Delete">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="row push">
                        <div class="col-lg-8 col-xl-5 offset-lg-4">
                            <div class="form-group">
                                <a class="btn btn-warning" href="{{url('/categories').'/'.$category->customer_id}}">
                                    <i class="far fa-arrow-alt-circle-left"></i> Back
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
    <script src="{{asset('js/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/plugins/datatables/dataTables.bootstrap4.min.js')}}"></script>

    <!-- Page JS Code -->
    <script src="{{asset('js/pages/be_tables_datatables.min.js')}}"></script>
    @if(Session::get('user-type')==1)
        <script>
            function delProduct(id) {
                if (confirm("Do you want delete this product?")) {
                    $.ajax({
                        url: '{{url('/products/del')}}',
                        type: "POST",
                        data: {
                            "_token": Laravel.csrfToken,
                            "id": id,
                        },
                        error: function () {
                        },
                        success: function (data) {
                            if (data.status == true) {
                                window.location.reload();
                            }
                        }
                    });
                }
            }

            $(document).ready(function () {
                $("[name^='show-toggle-']").on('change', function () {
                    var id = this.name.split("show-toggle-")[1];
                    $.ajax({
                        url: '{{url('/products/toggle-visible')}}',
                        type: "POST",
                        data: {
                            "_token": Laravel.csrfToken,
                            "id": id,
                        },
                        error: function () {
                        },
                        success: function (data) {
                            if (data.status == true) {
                                //window.location.reload();
                            }
                        }
                    });
                });
            });
        </script>
    @endif
@endsection
