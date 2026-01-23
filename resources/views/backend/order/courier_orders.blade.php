@extends('backend.layouts.master')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h5 class="m-0">
                            <i class='fas fa-truck'></i>
                            Courier Assigned Orders
                        </h5>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Courier Orders</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <table id="CourierOrderTbl" class="table table-bordered table-striped nowrap dt-responsive" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th width="6%">SN</th>
                                            <th>Order No</th>
                                            <th>Total Amount</th>
                                            <th>Payment Type</th>
                                            <th>Courier</th>
                                            <th>Order Date</th>
                                            <th>Status</th>
                                            <th width="20%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>

    <script>
        $(function () {
            $("#CourierOrderTbl").DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('orders.courier.data') }}",
                columns: [
                    {data: "DT_RowIndex", searchable: false, orderable: false},
                    {data: "order_no", name: "order_no"},
                    {data: "order_total", name: "order_total"},
                    {data: "payment_id", searchable: false, orderable: false},
                    {data: "courier_id", name: "courier_id"},
                    {data: "order_date", searchable: false, orderable: false},
                    {data: "status", searchable: false, orderable: false},
                    {data: "action", searchable: false, orderable: false},
                ]
            });
        });
    </script>
@endsection
