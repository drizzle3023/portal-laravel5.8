@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{asset('js/plugins/datatables/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" href="{{asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css')}}">
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Client Detail</h1>
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
                    <h2 class="content-heading pt-0">Customer Info</h2>

                    <div class="alert alert-info" role="alert">
                        <p class="mb-0">You can change your account information on <a class="alert-link" href="{{url('profile')}}">Profile</a> page!</p>
                    </div>

                    <div class="row push">
                        <div class="col-md-8">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>
                                        Name
                                    </label>
                                    <input type="text" class="form-control" name="first-name" placeholder="First Name"
                                           value="{{$customer->first_name.' '.$customer->last_name}}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label>
                                        Email
                                    </label>
                                    <input type="email" class="form-control" name="email" placeholder="Email"
                                           value="{{$customer->email}}" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>
                                        Birthday
                                    </label>
                                    <input type="text" class="form-control" value="{{ date('m/d/Y', strtotime($customer->birthday)) }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="d-block">Gender</label>
                                    <div class="custom-control custom-radio custom-control-inline custom-control-primary">
                                        <input type="radio" class="custom-control-input" disabled id="example-radio-custom-inline1"
                                               name="gender" value="Male" @if($customer->gender == 'Male') checked @endif >
                                        <label class="custom-control-label" for="example-radio-custom-inline1">Male</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline custom-control-primary">
                                        <input type="radio" class="custom-control-input" disabled id="example-radio-custom-inline2"
                                               name="gender" value="Female"
                                               @if($customer->gender == 'Female') checked @endif>
                                        <label class="custom-control-label"
                                               for="example-radio-custom-inline2">Female</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="dm-project-new-name">
                                        PhoneNumber
                                    </label>
                                    <input type="text" class="form-control" name="phone-number"
                                           placeholder="+18003030203" value="{{$customer->phonenumber}}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="dm-project-new-name">
                                        Company
                                    </label>
                                    <input type="text" class="form-control" name="company" placeholder="Company"
                                           value="{{$customer->company}}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dm-project-new-name">
                                    Address
                                </label>
                                <input type="text" class="form-control" name="address" placeholder="Address"
                                       value="{{$customer->address}}" disabled>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="dm-project-new-name">
                                        City
                                    </label>
                                    <input type="text" class="form-control" name="city" placeholder="City"
                                           value="{{$customer->city}}" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label for="dm-project-new-name">
                                        State
                                    </label>
                                    <input type="text" class="form-control" name="state" placeholder="State"
                                           value="{{$customer->state}}" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label for="dm-project-new-name">
                                        Zip Code
                                    </label>
                                    <input type="text" class="form-control" name="zip-code" placeholder="Zip Code"
                                           value="{{$customer->zipcode}}" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>
                                        Start Date ~ Expire Date
                                    </label>
                                    <div class="input-daterange input-group" data-date-format="mm/dd/yyyy"
                                         data-week-start="0" data-autoclose="true" data-today-highlight="true">
                                        <input type="text" class="form-control" name="start-date" placeholder="From"
                                               data-week-start="1" data-autoclose="true" data-today-highlight="true"
                                               value="{{ date('m/d/Y', strtotime($customer->start_date)) }}" disabled="disabled">
                                        <div class="input-group-prepend input-group-append">
                                            <span class="input-group-text font-w600">
                                                <i class="fa fa-fw fa-arrow-right"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" name="expire-date" placeholder="To"
                                               data-week-start="0" data-autoclose="true" data-today-highlight="true"
                                               value="{{ date('m/d/Y', strtotime($customer->expire_date)) }}" disabled="disabled">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label>
                                        Price
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                $
                                            </span>
                                        </div>
                                        <input type="text" class="form-control text-center" name="price"
                                               placeholder="00" value="{{$customer->price}}" disabled="disabled">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

{{--                    <h2 class="content-heading pt-0">Products</h2>--}}
{{--                    <div class="row push">--}}
{{--                        <div class="block-content block-content-full">--}}
{{--                            <table--}}
{{--                                class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">--}}
{{--                                <thead>--}}
{{--                                <tr>--}}
{{--                                    <th class="text-center" style="width: 80px;">#</th>--}}
{{--                                    <th class="d-none d-sm-table-cell" style="width: 100px;">Picture</th>--}}
{{--                                    <th class="d-none d-sm-table-cell" style="width: 20%;">Product Name</th>--}}
{{--                                    <th class="d-none d-sm-table-cell">Description</th>--}}
{{--                                    <th class="d-none d-sm-table-cell" style="width: 15%;">Price</th>--}}
{{--                                    <th class="d-none d-sm-table-cell" style="width: 80px;">Add</th>--}}
{{--                                </tr>--}}
{{--                                </thead>--}}
{{--                                <tbody>--}}
{{--                                @foreach($products as $product)--}}
{{--                                    <tr>--}}
{{--                                        <td class="text-center">{{$loop->iteration}}</td>--}}
{{--                                        <td class="font-w600">--}}
{{--                                            <div align="center">--}}
{{--                                                <img--}}
{{--                                                    src="{{asset('/media/images/products/thumbnail/').'/'.$product->picture}}"--}}
{{--                                                    style="width: 80px;">--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td class="d-none d-sm-table-cell">--}}
{{--                                            <a href="{{url('/products/detail/').'/'.$product->id}}">{{$product->name}}</a>--}}
{{--                                        </td>--}}
{{--                                        <td class="d-none d-sm-table-cell">--}}
{{--                                            {{$product->description}}--}}
{{--                                        </td>--}}
{{--                                        <td class="d-none d-sm-table-cell">--}}
{{--                                            <span class="badge badge-success">{{$product->price}}</span>--}}
{{--                                        </td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <div--}}
{{--                                                class="custom-control custom-switch custom-control custom-control-inline mb-2"--}}
{{--                                                align="center">--}}
{{--                                                <input type="checkbox" class="custom-control-input"--}}
{{--                                                       id="show-toggle-{{$product->id}}"--}}
{{--                                                       name="show-toggle-{{$product->id}}"--}}
{{--                                                       @if($product->has_flag == 1) checked @endif >--}}
{{--                                                <label class="custom-control-label"--}}
{{--                                                       for="show-toggle-{{$product->id}}"></label>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                @endforeach--}}
{{--                                </tbody>--}}
{{--                            </table>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <!-- Submit -->
                    @if(Session::get('user-type')!=3)
                    <div class="row push">
                        <div class="col-lg-8 col-xl-5 offset-lg-4">
                            <div class="form-group">
                                <a class="btn btn-warning" href="{{url('/customers')}}">
                                    <i class="far fa-arrow-alt-circle-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
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
    <script>
        $(document).ready(function () {
            $("[name^='show-toggle-']").on('change', function () {
                var id = this.name.split("show-toggle-")[1];
                $.ajax({
                    url: '{{url('/customers/toggle-add-product')}}',
                    type: "POST",
                    data: {
                        "_token": Laravel.csrfToken,
                        "product_id": id,
                        "customer_id": "{{$customer->id}}",

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
@endsection
