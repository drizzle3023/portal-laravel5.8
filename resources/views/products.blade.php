@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{asset('js/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('js/plugins/datatables/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" href="{{asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css')}}">
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Products</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">App</li>
                        <li class="breadcrumb-item active" aria-current="page">Products</li>
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
                <h3 class="block-title">Product List</h3>
            </div>
            <div class="block-content block-content-full">
                <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">#</th>
                        <th class="d-none d-sm-table-cell" style="width: 30%;">Name</th>
                        <th class="d-none d-sm-table-cell" style="width: 30%;">Domain</th>
                        <th class="d-none d-sm-table-cell" style="width: 180px;">Allowed user</th>
                        <th class="d-none d-sm-table-cell" style="width: 180px;">Currently Used</th>
                        <th class="d-none d-sm-table-cell" style="width: 180px;">Free</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($product_array as $product)
                        <tr>
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td class="d-none d-sm-table-cell">
                                {{$product->name}}
                            </td>
                            <td class="d-none d-sm-table-cell">
                                {{$product->domain->domain}}
                            </td>
                            <td class="d-none d-sm-table-cell">
                                {{$product->alloweduser}}
                            </td>
                            <td class="d-none d-sm-table-cell">
                                @if($product->used > $product->alloweduser)
                                    <span class="badge badge-danger">{{$product->used}}</span>
                                @else
                                    <span class="badge badge-success">{{$product->used}}</span>
                                @endif

                            </td>
                            <td class="d-none d-sm-table-cell">
                                {{$product->free}}
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
    <script src="{{asset('js/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/plugins/datatables/dataTables.bootstrap4.min.js')}}"></script>

    <!-- Page JS Code -->
    <script src="{{asset('js/pages/be_tables_datatables.min.js')}}"></script>

@endsection
