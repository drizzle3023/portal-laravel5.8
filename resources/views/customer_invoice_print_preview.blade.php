@extends('layouts.backend')

@section('content')

    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Client Invoice Print Preview</h1>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <div class="block block-rounded block-bordered">
            <div class="block-content">
                <div align="right">
                    <a href="{{url('/customers/print-invoice').'/'.$customer->id.'/print'}}" class="btn btn-primary"><i class="si si-printer"></i> Export to PDF</a>
                    <a href="{{url('/customers')}}" class="btn btn-warning"><i class="far fa-arrow-alt-circle-left"></i> Back</a>
                </div>

                <div align="center">
                    <h1>Invoice</h1>
                </div>
                <div style="margin-top: 10px; margin-bottom: 10px;">
                    <span>Name: </span> {{$customer->first_name.' '.$customer->last_name}} <br>
                    <span>Email: </span> {{$customer->email}} <br>
                    <span>Phone: </span> {{$customer->phonenumber}} <br>
                    <span>Company: </span> {{$customer->company}} <br>
                </div>

                <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">No</th>
                        <th class="d-none d-sm-table-cell" style="width: 120px;">StartDate</th>
                        <th class="d-none d-sm-table-cell" style="width: 120px;">ExpireDate</th>
                        <th class="d-none d-sm-table-cell" style="width: 80px;">Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td class="d-none d-sm-table-cell">
                                {{ date('d M Y', strtotime($invoice->start_date)) }}
                            </td>
                            <td class="d-none d-sm-table-cell">
                                {{ date('d M Y', strtotime($invoice->expire_date)) }}
                            </td>
                            <td class="d-none d-sm-table-cell">
                                {{ $invoice->price.' KWD' }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div style="margin-top: 10px; border-top: 1px solid; margin-bottom: 30px;">
                    <span style="font-weight: bold">Total: </span> {{$total.' KWD'}}
                </div>
            </div>
        </div>
    </div>
@endsection
