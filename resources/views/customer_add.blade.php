@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}">
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Add Client</h1>
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

                @if (isset($warning))
                    <div class="alert alert-warning alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>There is no client yet. <br>You have to add client before add products.</strong>
                    </div>
                @endif

                <form action="/customers/add" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row push">
                        <div class="col-md-8">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>
                                        First Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="first-name" placeholder="First Name">
                                </div>
                                <div class="col-md-6">
                                    <label>
                                        Last Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="last-name" placeholder="Last Name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" name="email" placeholder="Email">
                            </div>
                            <div class="form-group">
                                <label>
                                    Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control" name="password" placeholder="Password">
                            </div>
                            <div class="form-group">
                                <label>
                                    Birthday
                                </label>
                                <input type="text" class="js-datepicker form-control" name="birthday"
                                       data-week-start="0" data-autoclose="true" data-today-highlight="true"
                                       data-date-format="mm/dd/yyyy" placeholder="mm/dd/yyyy">
                            </div>
                            <div class="form-group">
                                <label class="d-block">Gender</label>
                                <div class="custom-control custom-radio custom-control-inline custom-control-primary">
                                    <input type="radio" class="custom-control-input" id="example-radio-custom-inline1"
                                           name="gender" value="Male">
                                    <label class="custom-control-label" for="example-radio-custom-inline1">Male</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline custom-control-primary">
                                    <input type="radio" class="custom-control-input" id="example-radio-custom-inline2"
                                           name="gender" value="Female">
                                    <label class="custom-control-label"
                                           for="example-radio-custom-inline2">Female</label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="dm-project-new-name">
                                        PhoneNumber <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="phone-number"
                                           placeholder="+18003030203">
                                </div>
                                <div class="col-md-6">
                                    <label for="dm-project-new-name">
                                        Company
                                    </label>
                                    <input type="text" class="form-control" name="company" placeholder="Company">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dm-project-new-name">
                                    Address
                                </label>
                                <input type="text" class="form-control" name="address" placeholder="Address">
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="dm-project-new-name">
                                        City
                                    </label>
                                    <input type="text" class="form-control" name="city" placeholder="City">
                                </div>
                                <div class="col-md-4">
                                    <label for="dm-project-new-name">
                                        State
                                    </label>
                                    <input type="text" class="form-control" name="state" placeholder="State">
                                </div>
                                <div class="col-md-4">
                                    <label for="dm-project-new-name">
                                        Zip Code
                                    </label>
                                    <input type="text" class="form-control" name="zip-code" placeholder="Zip Code">
                                </div>
                            </div>
                            <h2 class="content-heading pt-0"></h2>
                            <div class="form-group row">
                                <div class="col-md-8">
                                    <label>
                                        Start Date ~ Expire Date <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-daterange input-group" data-date-format="mm/dd/yyyy"
                                         data-week-start="0" data-autoclose="true" data-today-highlight="true">
                                        <input type="text" class="form-control" name="start-date" placeholder="From"
                                               data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                        <div class="input-group-prepend input-group-append">
                                            <span class="input-group-text font-w600">
                                                <i class="fa fa-fw fa-arrow-right"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" name="expire-date" placeholder="To"
                                               data-week-start="0" data-autoclose="true" data-today-highlight="true">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>
                                        Price <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                KWD
                                            </span>
                                        </div>
                                        <input type="text" class="form-control text-center" name="price"
                                               placeholder="00.000">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="row push">
                        <div class="col-lg-8 col-xl-5 offset-lg-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check-circle mr-1"></i> Submit
                                </button>
                                <a class="btn btn-danger" href="{{url('/customers')}}">
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
    <script src="{{asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>

    <script>jQuery(function () {
            Dashmix.helpers(['datepicker']);
        });</script>
    <!-- Page JS Code -->
    <script>

    </script>
@endsection
