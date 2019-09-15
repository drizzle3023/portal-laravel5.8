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
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Clients</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">App</li>
                        <li class="breadcrumb-item active" aria-current="page">Clients</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <div class="block block-rounded block-bordered">
            <div class="block-header block-header-default">
                <h3 class="block-title">Client List</h3>
            </div>
            <div class="block-content block-content-full">
                @if(Session::get('user-type')==1)
                    <div style="margin-bottom: 10px;">
                        <a class="btn btn-primary" href="{{url('/customers/add')}}"><i class="si si-user-follow"></i>
                            Add
                            Client</a>
                    </div>
                @endif
                <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">No</th>
                        <th class="d-none d-sm-table-cell">Name</th>
                        <th class="d-none d-sm-table-cell">Company</th>
                        <th class="d-none d-sm-table-cell">StartDate</th>
                        <th class="d-none d-sm-table-cell">ExpireDate</th>
                        @if(Session::get('user-type') === 1)
                            <th class="d-none d-sm-table-cell" style="width: 80px;">Enable</th>
                        @endif
                        <th class="d-none d-sm-table-cell" style="width: 80px;">Print</th>
                        @if(Session::get('user-type') === 1)
                            <th class="d-none d-sm-table-cell" style="width: 80px;">Action</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customers as $customer)
                        <tr>
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td class="font-w600">
                                <a href="{{url('/customers/edit').'/'.$customer->id}}">{{$customer->first_name.' '.$customer->last_name}}</a>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                {{ $customer->company }}
                            </td>
                            <td class="d-none d-sm-table-cell">
                                {{ date('d M Y', strtotime($customer->start_date)) }}
                            </td>
                            <td class="d-none d-sm-table-cell">
                                {{ date('d M Y', strtotime($customer->expire_date)) }}
                            </td>
                            @if(Session::get('user-type')==1)
                                <td class="text-center">
                                    <div class="custom-control custom-switch custom-control custom-control-inline mb-2"
                                         align="center">
                                        <input type="checkbox" class="custom-control-input"
                                               id="enable-toggle-{{$customer->id}}"
                                               name="enable-toggle-{{$customer->id}}"
                                               @if($customer->enable_flag == 1) checked @endif >
                                        <label class="custom-control-label"
                                               for="enable-toggle-{{$customer->id}}"></label>
                                    </div>
                                </td>
                            @endif
                            <td class="text-center">
                                <a href="{{url('/customers/print-invoice').'/'.$customer->id}}"><i
                                        class="si si-printer"></i></a>
                            </td>
                            @if(Session::get('user-type')==1)
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{url('/customers/edit').'/'.$customer->id}}"
                                           class="btn btn-sm btn-primary" data-toggle="tooltip" title="Edit">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>
                                        <a href="javascript:delCustomer({{$customer->id}})"
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
            function delCustomer(id) {
                if (confirm("Do you want delete this customer?")) {
                    $.ajax({
                        url: '{{url('/customers/del')}}',
                        type: "POST",
                        data: {
                            "_token": Laravel.csrfToken,
                            "id": id,
                        },
                        error: function () {
                        },
                        success: function (data) {
                            if (data.message.length == 0) {
                                window.location.reload();
                            }
                        }
                    });
                }
            }

            $(document).ready(function () {
                $("[name^='enable-toggle-']").on('change', function () {
                    var id = this.name.split("enable-toggle-")[1];
                    $.ajax({
                        url: '{{url('/customers/toggle-enable')}}',
                        type: "POST",
                        data: {
                            "_token": Laravel.csrfToken,
                            "id": id,
                        },
                        error: function () {
                        },
                        success: function (data) {
                            if (data.message.length == 0) {
                                //window.location.reload();
                            }
                        }
                    });
                });
            });
        </script>
    @endif
@endsection
