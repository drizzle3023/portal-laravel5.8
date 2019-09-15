@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{asset('js/plugins/bootstrap-imageupload/css/bootstrap-imageupload.min.css')}}">
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url({{asset('media/photos/Food3.jpg')}});">
        <div class="bg-black-50">
            <div class="content content-full">
                <h1 class="font-size-h2 text-white my-2">
                    <i class="fa fa-pencil-alt text-white-50 mr-1"></i> Edit Category
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

                <form action="/categories/edit" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Vital Info -->
                    <h2 class="content-heading pt-0">Vital Info</h2>
                    <div class="row push">
                        <div class="col-lg-4">
                            <p class="text-muted">
                                Some vital information about category
                            </p>
                        </div>
                        <div class="col-lg-8 col-xl-5">
                            <div class="form-group">
                                <label for="dm-project-new-name">
                                    Category Name (English) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="category-name" placeholder="eg: Pizza" value="{{$category->name}}">
                            </div>
                            <div class="form-group">
                                <label>
                                    Category Name (Other language)
                                </label>
                                <div class="custom-control custom-checkbox custom-control-inline custom-control-primary mb-1">
                                    <input type="checkbox" class="custom-control-input" id="checkbox-name-rtl">
                                    <label class="custom-control-label" for="checkbox-name-rtl">RTL?</label>
                                </div>
                                <input type="text" class="form-control" name="category-name-ar" placeholder="eg: Pizza" value="{{$category->name_second}}">
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-8">
                                    <label for="dm-project-new-category">
                                        Image <span class="text-danger">*</span>
                                    </label>
                                    <!-- bootstrap-imageupload. -->
                                    <div class="imageupload panel panel-default">
                                        <img id="preview" src="{{asset('media/images/categories/original').'/'.$category->picture}}" class="image-preview-edit"/>
                                        <input type="file" id="image" name="image" style="display: none;"/>
                                        <!--<input type="hidden" style="display: none" value="0" name="remove" id="remove">-->
                                        <a href="javascript:changeProfile()" class="btn btn-primary">Change</a>
                                        <a href="javascript:removeImage()" class="btn btn-danger">Original</a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dm-project-new-name">
                                    Tags (English)
                                </label>
                                <input type="text" class="form-control" name="category-tags" placeholder="eg: Tag1, Tag2, Tag3" value="{{$category->tags}}">
                            </div>
                            <div class="form-group">
                                <label for="dm-project-edit-description">Tags (Other language)</label>
                                <div
                                    class="custom-control custom-checkbox custom-control-inline custom-control-primary">
                                    <input type="checkbox" class="custom-control-input" id="checkbox-tags-rtl">
                                    <label class="custom-control-label" for="checkbox-tags-rtl">RTL?</label>
                                </div>
                                <input type="text" class="form-control" name="category-tags-ar" placeholder="eg: Tag1, Tag2, Tag3"  value="{{$category->tags_second}}">
                            </div>
                        </div>
                    </div>
                    <!-- END Vital Info -->
                        <input type="hidden" value="{{$category->id}}" name="id">
                    <!-- Submit -->
                    <div class="row push">
                        <div class="col-lg-8 col-xl-5 offset-lg-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-check-circle mr-1"></i> Update Category
                                </button>
                                <a class="btn btn-warning" href="{{url('/categories').'/'.$category->customer_id}}">
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
        function changeProfile() {
            $('#image').click();
        }
        $('#image').change(function () {
            var imgPath = this.value;
            var ext = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
            if (ext == "gif" || ext == "png" || ext == "jpg" || ext == "jpeg")
                readURL(this);
            else
                alert("Please select image file (jpg, jpeg, png).")
        });
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $('#preview').attr('src', e.target.result);
//              $("#remove").val(0);
                };
            }
        }
        function removeImage() {
            $('#preview').attr('src', "{{asset('media/images/categories/original').'/'.$category->picture}}");
//      $("#remove").val(1);
        }

        $(document).ready(() => {

            $("#checkbox-name-rtl").on("change", () => {
                if ($("#checkbox-name-rtl").prop("checked") == true) {
                    $("[name='category-name-ar']").attr("dir", "rtl");
                } else {
                    $("[name='category-name-ar']").removeAttr("dir");
                }
            });

            $("#checkbox-tags-rtl").on("change", () => {
                if ($("#checkbox-tags-rtl").prop("checked") == true) {
                    $("[name='category-tags-ar']").attr("dir", "rtl");
                } else {
                    $("[name='category-tags-ar']").removeAttr("dir");
                }
            });

        });

    </script>
@endsection
