@extends('layouts.faya_app')

@section('title')
    <title>Wallet</title>
@stop

@section('content')

    <!-- main container -->
    <div class="main_contain">
        <!-- heaser -->
        @include('partials.new-frontend.agency.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Wallet</h2>
            </div>

            <div class="column col_6 align_right">
                <a href="#fund_wallet" class="btn modal_click small_btn"><span class="_plus"></span>Fund Wallet</a>
            </div>
        </div>


        <!-- main stats -->
        <div class="the_stats the_frame clearfix mb4">
            <div class="column col_4 no_border">
                <span class="small_faint uppercased weight_medium">Current Balance</span>
                <h3>&#8358; {{ number_format($wallet[0]->balance, 2) }}</h3>
            </div>
        </div>

        <div class="the_frame client_dets mb4">

            <div class="filters border_bottom clearfix">
                <div class="column col_8 p-t">
                    <p class="uppercased weight_medium">All Transactions</p>
                </div>

                <div class="column col_4 clearfix">
                    <div class="col_5 column">
                        <input type="text" name="start_date" class="flatpickr" placeholder="Start Date">
                    </div>

                    <div class="col_5 column">
                        <input type="text" name="stop_date" class="flatpickr" placeholder="Stop Date">
                    </div>

                    <div class="col_1 column">
                        <button type="button" id="filter_wallet" class="btn small_btn">Filter</button>
                    </div>
                </div>
            </div>


            <div class="tab_contain">
                <div class="tab_content" id="history">

                    <table class="wallet_hitory">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Transaction Ref.</th>
                                <th>Type</th>
                            </tr>
                        </thead>

                    </table>

                </div>
                <!-- end -->

            </div>
        </div>

    </div>

    <!-- fund wallet modal -->
    <div class="modal_contain" id="fund_wallet">

        <h2 class="sub_header mb4 align_center">Fund Wallet</h2>

        <form id="fund-form" role='form' action="{{ route('pay') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <script src="https://js.paystack.co/v1/inline.js"></script>
            <div class="input_wrap">
                <label class="small_faint uppercased weight_medium">Amount</label>
                <input id="amount" type="number" name="amount" placeholder="Enter Amount">
            </div>
            <input type="hidden" name="email" id="email" value="{{ $user_det[0]->email }}">
            <input type="hidden" name="name" id="name" value="{{ $user_det[0]->firstname .' '.$user_det[0]->lastname }}">
            <input type="hidden" name="phone_number" id="phone_number" value="{{ $user_det[0]->phone_number }}">
            <input type="hidden" name="reference" id="reference" value="" />
            <input type="hidden" name="user_id" value="{{ $user_id }}" />
        </form>

        <div class="mb4">
            <input type="button" value="Fund Wallet" id="fund" onclick="payWithPaystack()" class="full btn uppercased">
        </div>

    </div>

@stop

@section('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    {{--<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>--}}
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    <script>
        function payWithPaystack(){
            $(".modal_contain").css({
                opacity: 0.5
            });
            var handler = PaystackPop.setup({
                key: 'pk_test_9945d2a543e97e34d0401f1d926e79dc1716ccc7',
                email: "<?php echo $user_det[0]->email; ?>",
                amount: parseFloat(document.getElementById('amount').value * 100),
                metadata: {
                    custom_fields: [
                        {
                            display_name: "<?php echo $user_det[0]->firstname .' '.$user_det[0]->lastname; ?>",
                            value: "<?php echo $user_det[0]->phone_number; ?>"
                        }
                    ]
                },
                callback: function(response){
                    document.getElementById('reference').value = response.reference;
                    document.getElementById('fund-form').submit();
                },
                onClose: function(){
                    alert('window closed');
                }
            });
            handler.openIframe();
        }

    //    datatables
        $(document).ready(function () {
            flatpickr(".flatpickr", {
                altInput: true,
            });
            var Datefilter =  $('.wallet_hitory').DataTable({
                dom: 'Bfrtip',
                paging: true,
                serverSide: true,
                processing: true,
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                ajax: {
                    url: '/wallets/get-wallet/data',
                    data: function (d) {
                        d.start_date = $('input[name=start_date]').val();
                        d.stop_date = $('input[name=stop_date]').val();
                    }
                },
                columns: [
                    {data: 'date', name: 'date'},
                    {data: 'amount', name: 'amount'},
                    {data: 'reference', name: 'reference'},
                    {data: 'type', name: 'type'},

                ],

            });

            $('#filter_wallet').on('click', function() {
                Datefilter.draw();
            });
        })
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
@stop

