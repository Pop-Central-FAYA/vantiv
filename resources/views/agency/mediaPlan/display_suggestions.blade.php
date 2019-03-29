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
                <h2 class="sub_header">Suggested stations &</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <div class="margin_center col_7 clearfix create_fields">

                <div class="the_stats the_frame clearfix mb4">
                    <div class="column col_4">
                        <span class="weight_medium small_faint uppercased">TV </span>
                        <h3><a href="{{ route('campaign.all') }}">{{ $fayaFound['total_tv'] }}</a></h3>
                    </div>

                    <div class="column col_4">
                        <span class="weight_medium small_faint uppercased">Radio</span>
                        <h3><a href="{{ route('agency.campaigns.hold') }}" style="color: red;">{{ $fayaFound['total_radio'] }}</a></h3>
                    </div>

                    <div class="column col_4">
                        <span class="weight_medium small_faint uppercased">Total Audience</span>
                        <h3><a href="{{ route('clients.list') }}">{{ $fayaFound['total_audiences'] }}</a></h3>
                    </div>
                </div>

                <div class="the_frame client_dets mb4">
                    <!-- Suggestions table -->
                    <table class="display dashboard_campaigns">
                        <thead>
                        <tr>
                            <th>Station</th>
                            <th>Program</th>
                            <th>Time Belt</th>
                            <th>Day</th>
                            <th>Media Type</th>
                            <th>Total Audience</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($fayaFound['programs_stations'] as $found)
                                <tr>
                                    <td>{{ $found->station }}</td>
                                    <td>{{ $found->program }}</td>
                                    <td>{{ $found->start_time.' - '.$found->end_time }}</td>
                                    <td>{{ $found->day }}</td>
                                    <td>{{ $found->media_type }}</td>
                                    <td>{{ $found->audience }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- end -->
                </div>
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