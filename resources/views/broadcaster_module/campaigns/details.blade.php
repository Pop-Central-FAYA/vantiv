@extends('layouts.ssp.layout')

@section('title')
    <title>Torch | Campaign Details</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    @include('partials.new-frontend.broadcaster.campaign_management.sidebar')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">{{ $campaign_details['campaign_det']['campaign_name'] }}</h2>
            </div>
        </div>


        <!-- main stats -->
        <div class="the_frame clearfix mb">

            <div class="clearfix client_personal campaign_filter">
                @if($campaign_details['campaign_det']['agency_id'])
                    <div class="column col_2">
                        <span class="small_faint">Client</span>

                        <div class="select_wrap">
                            <select name="client">
                                    <option
                                    >{{ $campaign_details['campaign_det']['brand'] }}</option>
                            </select>
                        </div>
                    </div>
                @else
                    <div class="column col_2">
                        <span class="small_faint">Client</span>

                        <div class="select_wrap">
                            <select name="client" id="client">
                                @foreach($all_clients as $all_client)
                                    <option value="{{ $all_client->user_id }}"
                                            @if($all_client->user_id === $campaign_details['campaign_det']['company_user_id'])
                                            selected
                                            @endif
                                    >{{ $all_client->company_name ? $all_client->company_name : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                <div class="column col_2">
                    <span class="small_faint">Campaign Name</span>
                    <div class="show_this"></div>
                    <div class="select_wrap load_this" id="hide_this">
                        <select name="campaign" id="campaign">
                            @foreach($all_campaigns as $all_campaign)
                                <option value="{{ $all_campaign->campaign_id }}"
                                        @if($all_campaign->campaign_id === $campaign_details['campaign_det']['campaign_id'])
                                        selected
                                        @endif
                                >{{ $all_campaign->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="column col_2">
                    <span class="small_faint block_disp">Media Type</span>
                    <select name="channel" id="channel" >
                        <option class="{{ strtolower($campaign_details['campaign_det']['channel']->channel) }}" selected value="{{ $campaign_details['campaign_det']['channel']->id }}">{{ $campaign_details['campaign_det']['channel']->channel }}</option>
                    </select>
                </div>

                <div class="column col_2 check_this">
                    <span class="small_faint block_disp">Media Channel</span>
                    <select name="media" id="media">
                        <option class="" selected value="{{ $campaign_details['broadcasters'][0]->id }}">{{ $campaign_details['broadcasters'][0]->name }}</option>
                    </select>
                </div>

                <div class="column col_2 the_stats">
                    <span class="weight_medium small_faint uppercased m-b block_disp">Total Budget</span>
                    <p class="weight_medium">N{{ $campaign_details['campaign_det']['campaign_cost'] }}</p>
                </div>

            </div>
        </div>


        <!-- main charts -->
        @if($campaign_details['campaign_det']['status'] === 'active-not' || $campaign_details['campaign_det']['status'] === 'expired-not')
            <div class="clearfix mb3">

            <div class="column col_8 the_frame broadcaster_campaign_chart">

                <!-- filter -->
                <div class="filters clearfix compliance_load">
                    <!-- <div class="column col_7">
                        <h4 class="small_faint 7ppercased weight_medium">Compliance</h4>
                        <div class="small_faint weight_medium add_reports">

                        </div>
                        <p><br></p>
                    </div> -->
                    <!-- <div class="column col_4 clearfix">
                        <form action="{{ route('broadcaster.campaign_details.compliance') }}" method="GET" id="compliance-form">
                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                                <input type="hidden" id="start_date">
                                <input type="hidden" id="end_date">
                            </div>
                        </form>
                    </div> -->
                    <!-- <div id="container" style="min-width: 310px; height: 370px; margin: 0 auto"></div> -->
                    <div id="container" style="min-width: 310px; height: 370px; margin: 0 auto"></div>
                </div>
                <!-- end -->

            </div>

            <div class="column col_4">
                <div class="the_frame clearfix _stat mb">
                    <!-- <div class="column col_3 the_stats">
                        <span class="weight_medium small_faint uppercased m-b block_disp">Target CPM</span>
                        <p class="weight_medium">23</p>
                    </div> -->

                    <!-- <div class="column col_3 the_stats">
                        <span class="weight_medium small_faint uppercased m-b block_disp">Reached CPM</span>
                        <p class="weight_medium">23</p>
                    </div> -->

                    <!-- <div class="column col_12 the_stats">
                        <span class="weight_medium small_faint uppercased m-b block_disp">Total Budget</span>
                        <p class="weight_medium">N{{ $campaign_details['campaign_det']['campaign_cost'] }}</p>
                    </div> -->
                </div>

                <div class="the_frame _pie media_mix_load">
                    <h4 class="small_faint uppercased weight_medium">Media Mix</h4><br>
                    <div id="media_mix" style="height: 250px;"></div>

                </div>
            </div>

        </div>
        @endif
        <!-- campaign details -->
        <div class="the_frame client_dets mb4">

            <!-- tab links -->
            <div class="tab_header m4 border_bottom clearfix">
                <a href="#summary">Summary</a>
                <a href="#slots">Ad Slots</a>
                <a href="#files">Files</a>
                @if($campaign_details['campaign_det']['status'] === 'active' || $campaign_details['campaign_det']['status'] === 'expired')
                    <a href="#comp">Compliance</a>
                @endif
            </div>

            {{--{{ dd($campaign_details) }}--}}

            <div class="tab_contain default_summary">

                <!-- summary -->
                <div class="tab_content col_10 campaign_summary" id="summary">

                    <div class="clearfix mb">
                        <div class="column col_3">
                            <span class="weight_medium small_faint">Campaign</span>
                            <p class="weight_medium">{{ $campaign_details['campaign_det']['campaign_name'] }}</p>
                        </div>
                        <div class="column col_3">
                            <span class="small_faint">Start Date</span>
                            <p class="weight_medium">{{ $campaign_details['campaign_det']['start_date'] }}</p>
                        </div>
                        <div class="column col_3">
                            <span class="small_faint">Client</span>
                            <p class="weight_medium">{{ $campaign_details['campaign_det']['company_name'] }}</p>
                        </div>
                        <div class="column col_3">
                            <span class="small_faint">Media Type</span>
                             <p class="weight_medium"> {{ $campaign_details['campaign_det']['channel']->channel }} </p>
                        </div>
                    </div>

                    <div class="clearfix mb">
                        <div class="column col_3">
                            <span class="weight_medium small_faint">Budget</span>
                            <p class="weight_medium">N{{ $campaign_details['campaign_det']['campaign_cost'] }}</p>
                        </div>
                        <div class="column col_3">
                            <span class="small_faint">End Date</span>
                            <p class="weight_medium">{{ $campaign_details['campaign_det']['end_date'] }}</p>
                        </div>
                        <div class="column col_3">
                            <span class="small_faint">Brand</span>
                            <p class="weight_medium">{{ ucfirst($campaign_details['campaign_det']['brand']) }}</p>
                        </div>
                        <div class="column col_3">
                            <span class="small_faint">Media Channel</span>
                            @foreach($campaign_details['broadcasters'] as $broadcaster)
                                <p class="weight_medium">{{ $broadcaster->name }}</p>
                            @endforeach
                        </div>
                    </div>

                    <div class="clearfix mb">

                        <div class="column col_3">
                            <span class="small_faint">Market</span>
                            <div>
                                <p class="weight_medium"><span class="small_faint">Location</span> - @foreach($campaign_details['campaign_det']['location'] as $location) {{ $location->region.',' }} @endforeach</p>
                                <p class="weight_medium"><span class="small_faint">Audience</span> - @foreach($campaign_details['campaign_det']['target_audience'] as $audience) {{ $audience->audience.',' }} @endforeach</p>
                                <p class="weight_medium"><span class="small_faint">Age</span> - {{ $campaign_details['campaign_det']['age'] }}</p>
                            </div>
                        </div>

                        <div class="column col_3">
                            <span class="small_faint">LSM</span>
                            <p class="weight_medium">A, B, C1, C2</p>
                        </div>
                    </div>

                </div>
                <!-- end -->


                <!-- Ad slots -->
                <div class="tab_content" id="slots">
                    <!-- filter -->


                    <table>
                        <tr>
                            <th>Day</th>
                            <th>Day Parts</th>
                            <th>Region</th>
                            <th>Media Channel</th>
                            <th>Hourly Range</th>
                        </tr>

                        @foreach($campaign_details['file_details'] as $file_detail)
                            <tr>
                                <td>{{ $file_detail['day'] }}</td>
                                <td>{{ $file_detail['day_part'] }}</td>
                                <td>{{ $file_detail['region'] }}</td>
                                <td>{{ $file_detail['broadcast_station'] }}</td>
                                <td>{{ $file_detail['hourly_range'] }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <!-- end -->

                {{--files--}}
                <div class="tab_content" id="files">
                    <!-- filter -->
                    <table>
                        <tr>
                            <th>Files</th>
                            <th>Adslots</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        @foreach($campaign_details['uploaded_files'] as $uploaded_file)
                            <form method="GET" action="{{ route('file.change', ['file_id' => $uploaded_file->id]) }}" id="form-data{{ $uploaded_file->id }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <tr>
                                    <td><video src="{{ asset($uploaded_file->file_url) }}" width="150" height="100" controls></video></td>
                                    <td>
                                        @if($uploaded_file->get_adslot()->count() != 0)
                                            <p>{{ $uploaded_file->get_adslot->get_rate_card->get_day->day }}</p>
                                            <p>{{ $uploaded_file->get_adslot->day_part->day_parts }}</p>
                                            <p>{{ $uploaded_file->get_adslot->get_rate_card->hourly_range->time_range }}</p>
                                            <p>{{ $uploaded_file->get_adslot->from_to_time }}</p>
                                        @endif
                                    </td>
                                    @if($uploaded_file->status === 'rejected')
                                        <td>
                                            <p style="color: red">{{ $uploaded_file->adslot_reasons()->orderBy('updated_at', 'desc')->first()->rejection_reason->name }}</p>
                                        </td>
                                        <td>
                                            <p>{{ $uploaded_file->adslot_reasons()->orderBy('updated_at', 'desc')->first()->recommendation }}</p>
                                        </td>
                                        @if($uploaded_file->id === $uploaded_file->rejection_reasons()->orderBy('updated_at', 'desc')->first()->pivot->selected_adslot_id && $uploaded_file->agency_id === null)
                                            <td>
                                                <div class="progress{{ $uploaded_file->id }}">

                                                </div><br>
                                                <div class="{{ $errors->has('upload') ? ' has-error' : '' }}">
                                                    <input type="file" class="form-control file_content" data-file_id="{{ $uploaded_file->id }}" data-time_slot="{{ $uploaded_file->time_picked }}" id="file_upload" name="uploads">
                                                    @if($errors->has('upload'))
                                                        <span class="help-block">
                                                            {{ $errors->first('upload') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <input type="hidden" name="file_duration" class="file_duration" id="file_duration" size="5" />
                                            </td>
                                            <td>
                                                <div class="gallery{{ $uploaded_file->id }}">

                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" data-time_slot="{{ $uploaded_file->time_picked }}" class="update_mpo btn btn-success">Update</button>
                                            </td>
                                        @endif
                                    @endif
                                </tr>
                            </form>
                        @endforeach
                    </table>
                </div>

                <audio id="audio"></audio>

                <!-- Complaince -->
                <div class="tab_content" id="comp">
                    <!-- filter -->

                    <!-- end -->
                    <table>
                        <tr>
                            <th>Scheduled Date</th>
                            <th>Station</th>
                            <th>Schedule Spot</th>
                            <th>Time Aired</th>
                            <th>Duration</th>
                            <th>File Name</th>
                        </tr>

                        @foreach($campaign_details['compliance_reports'] as $compliance_report)
                            <tr>
                                <td>{{ date('M j, Y', strtotime($compliance_report->schedule_date)) }}</td>
                                <td>
                                    {{ $compliance_report->broadcaster_station }}
                                </td>
                                <td>{{ $compliance_report->schedule_spot }}</td>
                                <td>{{ $compliance_report->compliance_status == \Vanguard\Libraries\Enum\BroadcasterPlayoutStatus::PLAYED ?
                                 date('H:i:s', strtotime($compliance_report->played_date)) : '00:00:00' }}</td>
                                <td>{{ $compliance_report->duration }}</td>
                                <td>{{ $compliance_report->asset_name }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <!-- end -->

            </div>

            <div class="tab_contain new_summary" style="display: none;">

            </div>

        </div>

    </div>
@stop

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>
        <?php echo "var media_mix_data = ".$media_mix_data .";\n"; ?>
        <?php echo "var campaign_price_graph = ".$campaign_price_graph .";\n"; ?>

        jQuery( document ).ready(function () {

            $('.js-example-basic-multiple_1').select2();

            $('body').delegate("#client", "change", function (e) {
                var user_id = $("#client").val();
                var campaign_id = "<?php echo $campaign_details['campaign_det']['campaign_id']; ?>";
                $(".load_this").css({
                    opacity: 0.3
                });
                $.ajax({
                    url: '/campaign/'+user_id,
                    method: "GET",
                    data: {
                        user_id: user_id, campaign_id : campaign_id,
                    },
                    success: function (data) {
                        var big_html = '<div class="select_wrap show_this" id="show_this">\n' +
                            ' <select name="campaign" id="campaign"><option value="">Select Campaign</option>';
                        big_html +=  '';
                        $.each(data.campaign, function (index, value) {
                            big_html += '<option value="'+value.campaign_id+'">'+value.name+'</option>';
                        });

                        big_html += '</select>\n' +
                            '      </div>';

                        var chanel_html = '';
                        $.each(data.channel, function (index, value) {
                            chanel_html += '<span> '+value.brand+' <a href=""></a></span>';
                        });

                        $("#hide_this").hide();
                        $(".show_this").show();
                        $(".show_this").html(big_html);

                        $(".load_this").css({
                            opacity: 1
                        });
                    },
                    error: function () {
                        $(".load_this").css({
                            opacity: 1
                        });
                    }
                });
            });

            $('.js-example-basic-multiple').select2();
            //placeholder for target audienct


            $('body').delegate("#campaign", "change", function (e) {
                var campaign_id = $("#campaign").val();
                window.location = '/campaign/campaign-details/'+campaign_id;

            });

            // Highcharts.chart('media_mix',{
            //     chart: {
            //         renderTo: 'container',
            //         type: 'pie',
            //     },
            //     title: {
            //         text: ''
            //     },

            //     credits: {
            //         enabled: false
            //     },
            //     plotOptions: {
            //         pie: {
            //             allowPointSelect: false,
            //             dataLabels: {
            //                 enabled: true,
            //                 formatter: function () {
            //                     return this.y > 1 ? '<b>' + this.point.name + ':</b> ' +
            //                         this.y + '%' : null;
            //                 }
            //             }
            //         }
            //     },
            //     exporting: { enabled: false },
            //     series: [{
            //         innerSize: '50%',
            //         data: media_mix_data
            //     }]
            // });

            // Highcharts.chart('container', {

            //     chart: {
            //         type: 'column'
            //     },

            //     title: {
            //         text: ''
            //     },

            //     xAxis: {
            //         categories: campaign_price_graph.date
            //     },
            //     tooltip: {
            //         enabled: false
            //     },
            //     yAxis: {
            //         allowDecimals: false,
            //         min: 0,
            //         title: {
            //             text: 'Total Amount'
            //         }
            //     },
            //     credits: {
            //         enabled: false
            //     },
            //     exporting: { enabled: false },
            //     tooltip: {
            //         formatter: function () {
            //             return '<b>' + this.x + '</b><br/>' +
            //                 this.series.name + ': ' + this.y + '<br/>';
            //         },

            //     },

            //     plotOptions: {
            //         column: {
            //             stacking: 'normal'
            //         },
            //         bar: {
            //             animation: true,
            //             dataLabels: {
            //                 enabled: true,
            //                 align: "center",
            //                 color: "#FFFFFF"
            //             }
            //         }
            //     },

            //     series: campaign_price_graph.campaign_price_data
            // });

        });

        $(function () {
            var start_php = "<?php echo date('Y-m-d',strtotime($campaign_details['campaign_det']['start_date'])) ?>";
            var end_php = "<?php echo date('Y-m-d',strtotime($campaign_details['campaign_det']['end_date']))?>"
            var start = moment(start_php);
            var end =  moment(end_php);

            // function cb(start, end) {
            //     $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

            //     var start_date = start.format('YYYY-MM-DD');
            //     var stop_date = end.format('YYYY-MM-DD');
            //     var url = $("#compliance-form").attr("action");
            //     var campaign_id = "<?php echo $campaign_details['campaign_det']['campaign_id']; ?>";
            //     var media_type = $("#channel").val();
            //     var media_channel = $("#media").val();
            //     if(media_type != null && media_channel != null){
            //         $.ajax({
            //             method: "GET",
            //             url: url,
            //             data: { campaign_id : campaign_id,start_date: start_date, stop_date: stop_date, media_channel: media_channel },
            //             success: function (data) {
            //                 var small_html = '<p>'+ data.percentage_compliance +'</p>'
            //                 $(".add_reports").html(small_html);
            //                 Highcharts.chart('container', {

            //                     chart: {
            //                         type: 'column'
            //                     },

            //                     title: {
            //                         text: ''
            //                     },

            //                     xAxis: {
            //                         categories: data.date
            //                     },

            //                     yAxis: {
            //                         allowDecimals: false,
            //                         min: 0,
            //                         title: {
            //                             text: 'Total Amount'
            //                         }
            //                     },
            //                     credits: {
            //                         enabled: false
            //                     },
            //                     exporting: { enabled: false },
            //                     tooltip: {
            //                         formatter: function () {
            //                             return '<b>' + this.x + '</b><br/>' +
            //                                 this.series.name + ': ' + this.y + '<br/>';
            //                         },

            //                     },

            //                     plotOptions: {
            //                         column: {
            //                             stacking: 'normal'
            //                         }
            //                     },

            //                     series: data.compliance_data
            //                 });

            //             }
            //         })
            //     }else{
            //         // toastr.error("Please select the media type and channel");
            //     }


            // }

            // $('#reportrange').daterangepicker({
            //     startDate: start,
            //     endDate: end,
            //     ranges: {
            //         'Ongoing': [moment(start_php), moment(end_php)],
            //         'Last Week': [moment().subtract(6, 'days'), moment()],
            //         'One Month': [moment().subtract(1, 'month').startOf('month'), moment()],
            //         'Three Month': [moment().subtract(3, 'month').startOf('month'), moment()],
            //         'Six Month': [moment().subtract(6, 'month').startOf('month'), moment()],
            //         '1 Year': [moment().subtract(12, 'month').startOf('month'), moment()],
            //     }
            // }, cb);

            // cb(start, end);

        })


        $(".file_content").on('change', function () {
            var url = '/presigned-url';
            var time_slots_string = $(this).data('time_slot');
            var time_slot = parseInt(time_slots_string);
            var reader = new FileReader();

            for (var file, i = 0; i < this.files.length; i++) {
                file = this.files[i];
                if(file.name && !file.name.match(/.(mp4|mkv|avi|flv|vob)$/i)) {
                    toastr.error('Invalid Video format');
                    return;
                }

                var file_id = $(this).data("file_id");
                var splitedName = file.name.split(".");
                var video_format = splitedName[splitedName.length - 1];
                $(".tv_campaign_proceed").prop('disabled', true);
                $.ajax({
                    url : url,
                    type : "GET",
                    cache : false,
                    data: {filename : file.name, folder: 'campaign-tv-contents/'},
                    success: function (data) {
                        $.ajax({
                            xhr: function() {
                                var xhr = new window.XMLHttpRequest();
                                xhr.upload.addEventListener("progress", function(evt) {
                                    if (evt.lengthComputable) {
                                        var percentComplete = evt.loaded / evt.total;
                                        percentComplete = parseInt(percentComplete * 100);
                                        var big_html = '<div class="progress-bar" role="progressbar" aria-valuenow="'+percentComplete+'"'+
                                            'aria-valuemin="0" aria-valuemax="100" style="width:'+percentComplete+'%">'+
                                            '<span class="sr-only">'+percentComplete+'% Complete</span>'+
                                            '</div>';
                                        $('.progress'+file_id).html(big_html);
                                        if (percentComplete === 100) {
                                            $('.progress').fadeOut(1000);
                                        }

                                    }
                                }, false);

                                return xhr;
                            },
                            url : data,
                            type : "PUT",
                            data : file,
                            dataType : "text",
                            cache : false,
                            contentType : file.type,
                            processData : false,
                        })
                            .done(function(){
                                toastr.success('Your upload was successful, please hit the submit button to update your mpo');
                                var uploadedUrl = 'https:'+data.split('?')[0].substr(6);
                                var vid_show = '<video width="200" height="200" controls>\n' +
                                    '  <source src="'+uploadedUrl+'" type="video/mp4">\n' +
                                    '</video>' ;
                                $(".gallery"+file_id).html(vid_show);
                                var user_id = "";
                                var channel = 'nzrm6hchjats36';

                                $(".update_mpo").click(function () {
                                    var time_slots_string = $(this).data('time_slot');
                                    var file_duration_string = $(".file_duration").val();
                                    var url1 = $("#form-data"+file_id).attr('action');
                                    var time_slot = parseInt(time_slots_string);
                                    var file_duration = parseInt(file_duration_string);
                                    reader.onload = function(e) {
                                        var videoElement = document.createElement('video');
                                        videoElement.src = e.target.result;
                                        var timer = setInterval(function () {
                                            if (videoElement.readyState === 4){
                                                if(Math.round(videoElement.duration) > time_slot){
                                                    toastr.error('You are trying to upload a file of '+Math.round(videoElement.duration)+' seconds into a '+time_slot+' seconds slot');
                                                }else{
                                                    $.ajax({
                                                        url: url1,
                                                        method: "GET",
                                                        data: {'file_url' : uploadedUrl, 'file_name' : file.name, 'file_format' : video_format},
                                                        success: function(result){
                                                            if(result.error === 'error'){
                                                                toastr.error('An error occurred, please try again');
                                                                return;
                                                            }else if(result.success === 'success'){
                                                                toastr.success('Your upload for '+time_slot+' seconds was successful');
                                                                location.reload();
                                                            }
                                                            $(".tv_campaign_proceed").prop('disabled', false);
                                                        }
                                                    })
                                                }
                                                clearInterval(timer);
                                            }
                                        })
                                    };
                                    reader.readAsDataURL(file);
                                });
                            })
                            .fail(function(){
                                toastr.error('An error occurred, please try again ');
                            })
                    }
                })
            }
        });

    </script>
@stop


@section('styles')
    {{--<link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">--}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>

        .highcharts-grid path { display: none;}
        .highcharts-legend {
            display: none;
        }

        .radio {
            background-color: #00C4CA !important;
        }

        .tv {
            background-color: #5281FE !important;
        }
        .daterangepicker td.in-range {
            background-color: #00C4CA !important;
        }

        .daterangepicker td.active {
            background-color: #00C4CA !important;
        }

        .daterangepicker .ranges li.active {
            background-color: #00C4CA !important;
        }

        .daterangepicker .drp-calendar {
            max-width: 600px !important;
        }
    </style>
@stop
