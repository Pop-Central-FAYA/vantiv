@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Create Media Plan</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- header -->
        @if(Session::get('broadcaster_id'))
            @include('partials.new-frontend.broadcaster.header')
            @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
        @else
            @include('partials.new-frontend.agency.header')
        @endif

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Summary</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <div class="margin_center col_7 clearfix create_fields">

                <div class="the_stats the_frame clearfix mb4">
                    <table class="display dashboard_campaigns">
                        <tbody>
                           <tr>
                            <td>Client Name</td><td>{{ $media_plan->client->company_name }}</td>
                           </tr>
                           <tr>
                            <td>Product Name</td><td>{{ $media_plan->product_name }}</td>
                           </tr>
                           <tr>
                            <td>Flight Date</td><td>{{ date('d-M-Y',strtotime($media_plan->start_date)).' to '.date('d-M-Y',strtotime($media_plan->end_date)) }}</td>
                           </tr>
                        </tbody>
                    </table>
                </div>

                <div class="the_frame client_dets mb4">
                    <!-- Suggestions table -->
                    <table class="display dashboard_campaigns">
                        <thead>
                        <tr>
                            <th>Medium</th>
                            <th>Material Duration</th>
                            <th>Number of Spots/units</th>
                            <th>Gross Media Cost</th>
                            <th>Net Media Cost</th>
                            <th>Savings</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php $sum_total_spots=0; $sum_gross_value=0; $sum_net_value=0; $sum_savings=0; @endphp
                            @foreach($summary as $summary)
                                <tr>
                                    <td>{{ $summary->medium }}</td>
                                    <td>{{ implode($summary->material_durations, '", ') }}</td>
                                    <td>{{ $summary->total_spots }}</td>
                                    <td>{{ number_format($summary->gross_value, 2) }}</td>
                                    <td>{{ number_format($summary->net_value, 2) }}</td>
                                    <td>{{ number_format($summary->savings, 2) }}</td>
                                    @php
                                        $sum_total_spots += $summary->total_spots;
                                        $sum_gross_value += $summary->gross_value;
                                        $sum_net_value += $summary->net_value;
                                        $sum_savings += $summary->savings;
                                    @endphp
                                </tr>
                            @endforeach
                            <tr>
                                <td>Total</td>
                                <td></td>
                                <td>{{ $sum_total_spots }}</td>
                                <td>{{ number_format($sum_gross_value, 2) }}</td>
                                <td>{{ number_format($sum_net_value, 2) }}</td>
                                <td>{{ number_format($sum_savings, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- end -->
                </div>

                @if(Auth::user()->hasRole('admin'))
                    <div class="mb3">
                        <a href="{{ route('agency.media_plan.approve', ['id'=>$media_plan->id]) }}" class="btn block_disp uppercased align_center mb3"><span class="_plus"></span>Approve Plan</a>

                        <a href="{{ route('agency.media_plan.decline', ['id'=>$media_plan->id]) }}" class="btn block_disp uppercased align_center"><span class="_plus"></span>Decline Plan</a>

                        <a href="{{ route('campaign.get_campaign_general_information') }}" class="btn block_disp uppercased align_center"><span class="_plus"></span>Export Plan</a>
                    </div>
                @endif
            </div>
        </div>
        <!-- main frame end -->

    </div>
@stop

@section('scripts')
    <script src="https://unpkg.com/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            //flatpickr
            flatpickr(".flatpickr", {
                altInput: true,
            });
            //select for target audience
            $('.js-example-basic-multiple').select2();
            //placeholder for target audienct
            $('#region').select2({
                placeholder: "Please select region"
            });

            $('#lsm').select2({
                placeholder: "Please select LSM"
            });

            $('#social_class').select2({
                placeholder: "Please select social class"
            });

            $('#state').select2({
                placeholder: "Please select state"
            });

            $(".checkbox_region").click( function (){
                var checkbox_region = $(this).data("region");
                var $inputs = $('.checkbox_region');
                if(checkbox_region === "naem6hqwjhjatseog8" && $(this).is(':checked')){
                    $(".checkbox_region").prop('checked', false);
                    $("#naem6hqwjhjatseog8").prop('checked', true);
                    $inputs.not(this).prop('disabled',true);
                }else{
                    $(".checkbox_region").prop('disabled', false);
                }
            });

            // fetch all brands when a clientSis selected
            $('body').delegate('#clients','change', function(e){
                var clients = $("#clients").val();
                if(clients != ''){
                    $(".load_stuff").css({
                        opacity: 0.3
                    });
                    $("#industry").val('');
                    $("#sub_industry").val('');
                    var url = '/client/get-brands/'+clients;
                    $.ajax({
                        url: url,
                        method: "GET",
                        data: {clients: clients},
                        success: function(data){
                            if(data.brands){
                                var big_html = '<select name="brand" id="brand">\n';
                                if(data.brands != ''){
                                    big_html += '<option value="">Select Brand</option>';
                                    $.each(data.brands, function (index, value) {
                                        big_html += '<option value="'+value.id+'">'+value.name+'</option>';
                                    });
                                    big_html += '</select>';
                                    $(".brand_hide").hide();
                                    $(".brand_select").show();
                                    $(".brand_select").html(big_html);
                                    $(".load_stuff").css({
                                        opacity: 1
                                    });
                                }else{
                                    big_html += '<option value="">Please Select a Client</option></section>';
                                    $(".brand_hide").hide();
                                    $(".brand_select").show();
                                    $(".brand_select").html(big_html);
                                    $(".load_stuff").css({
                                        opacity: 1
                                    });
                                }
                            }else{
                                $(".load_stuff").css({
                                    opacity: 1
                                });
                                toastr.error('An error occurred, please contact the administrator')
                            }

                        }
                    });
                }else{
                    $("#industry").val('');
                    $("#sub_industry").val('');
                }
            });

            //fetch all industry and sub-industry attached to a brand
            $('body').delegate('#brand','change', function(e) {
                var brand = $("#brand").val();
                if (brand != '') {
                    $(".load_stuff").css({
                        opacity: 0.5
                    });
                    $('.next').attr("disabled", true);
                    var url = '/brand/get-industry';
                    $.ajax({
                        url: url,
                        method: "GET",
                        data: {brand: brand},
                        success: function (data) {
                            if (data.error === 'error') {
                                $(".load_stuff").css({
                                    opacity: 1
                                });
                                toastr.error('An error occured, please contact the administratot ')
                            } else {
                                $(".load_stuff").css({
                                    opacity: 1
                                });

                                $("#industry").val(data.industry.name);
                                $("#sub_industry").val(data.sub_industry.name);
                            }

                        }
                    });
                } else {
                    $("#industry").val('');
                    $("#sub_industry").val('');
                }
            });
        });
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@stop