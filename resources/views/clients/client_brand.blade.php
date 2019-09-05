@extends('dsp_layouts.faya_app')

@section('title')
    <title> Vantage | Client's Brand-Campaigns </title>
@stop

@section('content')
    <!-- main container -->
    <div class="main_contain">
        <!-- subheader -->
        <div class="sub_header clearfix mb">
            <div class="column col_6">
                <h2 class="sub_header">Clients</h2>
                <p class="bread small_font"><a href="{{ route('client.show', ['id' => $client_id]) }}">{{ $client[0]->company_name }}</a> &raquo; <span class="weight_medium">{{ $this_brand[0]->name }}</span></p>
            </div>
        </div>


        <div class="the_frame client_dets mb4">


            <div class="tab_contain">
                <div class="tab_content" id="history">
                    @if(count($campaigns) === 0)

                    @else
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>Amount Spent</th>
                            <th>MPO Status</th>
                            <th>Campaign Status</th>
                        </tr>

                        @foreach($campaigns as $campaign)
                            <tr>
                                <td>{{ $campaign['id'] }}</td>
                                <td>{{ $campaign['name'] }}</td>
                                <td>{{ $campaign['start_date'] }}</td>
                                <td>&#8358; {{ $campaign['budget'] }}</td>
                                @if($campaign['mpo_status'] === 1)
                                    <td class="color_base weight_medium">Approved</td>
                                @else
                                    <td class="weight_medium" style="color: red;">Pending</td>
                                @endif
                                <td>{{ $campaign['status'] }}</td>
                            </tr>
                        @endforeach

                    </table>
                    @endif

                </div>
                <!-- end -->

            </div>
        </div>

    </div>

    {{--modal for view mpo--}}
    @foreach($campaigns as $campaign)
        <div class="modal_contain" id="mpo{{ $campaign['camp_id'] }}">

            <h3>Mpo Details for {{ $campaign['name'] }}</h3>
        </div>


        <div class="modal_contain" id="invoice{{ $campaign['camp_id'] }}">

            <h3>Invoice Details for {{ $campaign['name'] }}</h3>
        </div>
    @endforeach
@stop

@section('scripts')
    <script>
        $(document).ready(function( $ ) {

            $("body").delegate(".modal_mpo", "click", function () {
                var href = $(this).attr("href");
                $(href).modal();
                return false;
            });

            $("body").delegate(".modal_invoice", "click", function () {
                var href = $(this).attr("href");
                $(href).modal();
                return false;
            })
        })
    </script>
@stop
