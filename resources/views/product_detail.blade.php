@extends('layouts.backend')

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url({{asset('media/photos/Food5.jpg')}});">
        <div class="bg-black-50">
            <div class="content content-full">
                <h1 class="font-size-h2 text-white my-2">
                    <i class="si si-list text-white-50 mr-1"></i> Product Detail
                </h1>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <div class="block block-rounded block-bordered">
            <div class="block-content">

                <form action="" method="POST">
                @csrf
                <!-- Vital Info -->
                    <h2 class="content-heading pt-0">Product Info</h2>
                    <div class="row push">
                        <div class="col-lg-4">
                            <div class="form-group row">
                                <div class="col-lg-8">
                                    <label for="dm-project-new-category">
                                        Image
                                    </label>
                                    <!-- bootstrap-imageupload. -->
                                    <div class="imageupload panel panel-default">
                                        <img id="preview"
                                             src="{{asset('media/images/products/original').'/'.$product->picture}}"
                                             class="image-preview-edit"/>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-4">
                            <div class="form-group row">
                                <div class="col-lg-9">
                                    <label>
                                        Product Name
                                    </label>
                                    <input type="text" class="form-control" name="product-name" placeholder="eg: Pizza"
                                           value="{{$product->name}}" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-9">
                                    <label>
                                        اسم المنتج
                                    </label>
                                    <input type="text" class="form-control" name="product-name-ar" placeholder="مثال: بيتزا"
                                           dir="rtl" value="{{$product->name_ar}}" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-9">
                                    <label for="dm-project-new-name">
                                        Category
                                    </label>
                                    <select class="custom-select" name="category">
                                        @foreach($categories as $category)
                                            @if($category->id == $product->category_id)
                                            <option value="{{$category->id}}" disabled selected >
                                                {{$category->name}}
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-9">
                                    <label for="dm-project-new-name">
                                        Price
                                    </label>
                                    <input type="text" class="form-control" name="product-price" placeholder="eg: 15USD"
                                           value="{{$product->price}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-4">
                            <div class="form-group row">
                                <div class="col-lg-10">
                                    <label for="dm-project-edit-description">Description</label>
                                    <textarea class="form-control" name="product-description" rows="5"
                                              placeholder="Product Description Here..." disabled>{{$product->description}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-10">
                                    <label for="dm-project-edit-description">وصف</label>
                                    <textarea class="form-control" name="product-description-ar" disabled rows="5"
                                              placeholder="وصف المنتج هنا ..."
                                              dir="rtl">{{$product->description_ar}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Vital Info -->
                    <!-- Submit -->
                    <div class="row push">
                        <div class="col-lg-8 col-xl-5 offset-lg-4">
                            <div class="form-group">
                                <a class="btn btn-warning" href="{{url('/products').'/'.$product->customer_id}}">
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
