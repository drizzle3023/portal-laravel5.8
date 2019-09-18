@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{asset('js/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}">
    <link rel="stylesheet" href="{{asset('js/plugins/datatables/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" href="{{asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css')}}">
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Blacklist</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">App</li>
                        <li class="breadcrumb-item active" aria-current="page">Blacklist</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <div class="block block-rounded block-bordered">
            <div class="block-content block-content-full">
                <div style="margin-bottom: 10px;">
                    <a class="btn btn-primary" href="{{url('/blacklist/add')}}">
                        <i class="si si-plus"></i> Add entry</a>
                </div>
                <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">#</th>
                        <th class="d-none d-sm-table-cell" style="">From</th>
                        <th class="d-none d-sm-table-cell" style="">To</th>
                        <th class="d-none d-sm-table-cell" style="width: 150px;">Enable</th>
                        <th class="d-none d-sm-table-cell" style="width: 150px;">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($blacklist_array as $one)
                        <tr>
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td class="d-none d-sm-table-cell">
                                {{$one->from}}
                            </td>
                            <td class="d-none d-sm-table-cell">
                                {{$one->rcpt}}
                            </td>
                            <td class="d-none d-sm-table-cell text-center">
                                <div class="custom-control custom-switch custom-control custom-control-inline mb-2"
                                     align="center">
                                    <input type="checkbox" class="custom-control-input"
                                           id="enable-toggle-{{$one->id}}" name="enable-toggle-{{$one->id}}"
                                           @if($one->is_enabled == 1) checked @endif>
                                    <label class="custom-control-label" for="enable-toggle-{{$one->id}}"></label>
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell text-center">
                                <div class="btn-group">
                                    <a href="{{url('/blacklist/edit').'/'.$one->id}}"
                                       class="btn btn-sm btn-primary" data-toggle="tooltip" title="Edit">
                                        <i class="fa fa-pencil-alt"></i>
                                    </a>
                                    <a href="javascript:deleteWL({{$one->id}})" class="btn btn-sm btn-primary"
                                       data-toggle="tooltip" title="Delete">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            </td>
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
    <script src="{{asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('js/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/plugins/datatables/dataTables.bootstrap4.min.js')}}"></script>

    <!-- Page JS Code -->
    <script src="{{asset('js/pages/be_tables_datatables.min.js')}}"></script>

    <script>jQuery(function(){ Dashmix.helpers(['datepicker']); });</script>

    <script>
        function deleteWL(id) {
            if (confirm("Do you want delete this blacklist?")) {
                $.ajax({
                    url: '{{url('/blacklist/delete')}}',
                    type: "POST",
                    data: {
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
                    url: '{{url('/blacklist/toggle-enable')}}',
                    type: "POST",
                    data: {
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
@endsection
