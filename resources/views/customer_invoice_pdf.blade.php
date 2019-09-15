@extends('layouts.simple')

@section('content')

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
                    {{ $invoice->price.'USD' }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div style="margin-top: 10px; border-top: 1px solid;">
        <span style="font-weight: bold">Total: </span> {{$total.'USD'}}
    </div>

@endsection
