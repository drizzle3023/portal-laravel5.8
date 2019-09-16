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
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Statistics</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">App</li>
                        <li class="breadcrumb-item active" aria-current="page">Statistics</li>
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
                <form action="{{url('/statistics')}}" method="get">
                    <h2 class="content-heading pt-0">Criteria</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="example-text-input">Time Range</label>
                                <div class="input-daterange input-group" data-date-format="yyyy-mm-dd" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="form-control" name="date_from" placeholder="From" data-week-start="1" data-autoclose="true" data-today-highlight="true" value="{{$date_from}}">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" name="date_to" placeholder="To" data-week-start="1" data-autoclose="true" data-today-highlight="true" value="{{$date_to}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="d-block">Stats Type</label>
                                <div class="custom-control custom-radio custom-control-inline custom-control-primary">
                                    <input type="radio" class="custom-control-input" id="example-radio-custom-inline1" name="stats_type" value="1" @if(isset($stats_type) && $stats_type == 1) checked @endif>
                                    <label class="custom-control-label" for="example-radio-custom-inline1">Day</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline custom-control-primary">
                                    <input type="radio" class="custom-control-input" id="example-radio-custom-inline2" name="stats_type" value="2" @if(isset($stats_type) && $stats_type == 2) checked @endif>
                                    <label class="custom-control-label" for="example-radio-custom-inline2">Month</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline custom-control-primary">
                                    <input type="radio" class="custom-control-input" id="example-radio-custom-inline3" name="stats_type" value="3" @if(isset($stats_type) && $stats_type == 3) checked @endif>
                                    <label class="custom-control-label" for="example-radio-custom-inline3">Year</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 row">
                            <div style="position: absolute; bottom: 0; margin-bottom: 1rem;">
                                <button type="submit"  class="btn btn-primary">Stats</button>
                                <a href="{{url('/statistics')}}" class="btn btn-success">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="block-content block-content-full">
                <h2 class="content-heading pt-0">Stats</h2>
                <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">#</th>
                        <th class="d-none d-sm-table-cell" style="width: 250px;">Time</th>
                        <th class="d-none d-sm-table-cell" style="width: 250px;">Sent</th>
                        <th class="d-none d-sm-table-cell" style="width: 250px;">Spam</th>
                        <th class="d-none d-sm-table-cell" style="width: 250px;">Attachment Blocked</th>
                        <th class="d-none d-sm-table-cell" style="width: 250px;">Virus/Trojan</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($result as $one)
                        <tr>
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td class="d-none d-sm-table-cell">
                                {{$one['time']}}
                            </td>
                            <td class="d-none d-sm-table-cell">
                                @if(isset($one['sent']))
                                    {{$one['sent']}}
                                @else
                                    0
                                @endif

                            </td>
                            <td class="d-none d-sm-table-cell">
                                @if(isset($one['spam']))
                                    {{$one['spam']}}
                                @else
                                    0
                                @endif
                            </td>
                            <td class="d-none d-sm-table-cell">
                                @if(isset($one['attachment']))
                                    {{$one['attachment']}}
                                @else
                                    0
                                @endif
                            </td>
                            <td class="d-none d-sm-table-cell">
                                @if(isset($one['virus']))
                                    {{$one['virus']}}
                                @else
                                    0
                                @endif
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
@endsection
