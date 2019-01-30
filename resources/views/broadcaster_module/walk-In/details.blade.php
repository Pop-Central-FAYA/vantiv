@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Walk-In Details</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    @include('partials.new-frontend.broadcaster.campaign_management.sidebar')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Walk-In</h2>
            </div>
        </div>

        <!-- main stats -->
        <div class="the_frame clearfix mb ">
            <div class="border_bottom clearfix client_name">
                <a href="{{ route('walkins.all') }}" class="back_icon block_disp left"></a>
                <div class="left">
                    <h2 class='sub_header'>{{ $client->company_name }}</h2>
                    <p class="small_faint">{{ $client->location }}</p>
                </div>

                <span class="client_ava right"><img src="{{ $client->company_logo ? asset($client->company_logo) : '' }}"></span>
            </div>

            <div class="clearfix client_personal">
                <div class="column col_3">
                    <span class="small_faint">Account Executive</span>
                    <p class='weight_medium'>{{ $user_details->firstname.' '.$user_details->lastname }}</p>
                </div>

                <div class="column col_3">
                    <span class="small_faint">Email</span>
                    <p class='weight_medium'>{{ $user_details->email }}</p>
                </div>

                <div class="column col_3">
                    <span class="small_faint">Phone</span>
                    <p class='weight_medium'>{{ $user_details->phone_number }}</p>
                </div>

                <div class="column col_3">
                    <span class="small_faint">Joined</span>
                    <p class='weight_medium'>{{ date('M j, Y', strtotime($user_details->created_at)) }}</p>
                </div>
            </div>
        </div>

        <!-- client charts -->
        <div class="the_frame mb client_charts content_month">
            <form action="{{ route('client.date', ['client_id' => $client_id]) }}" id="client_month" method="get">

                <div class="filters chart_filters border_bottom clearfix">
                    <div class="column col_6 date_filter">
                        <a id="yearly_client" href="{{ route('client.year', ['client_id' => $client_id]) }}">1Y</a>
                    </div>

                    <div class="column col_2 m-b">
                        <input type="text" class="flatpickr" id="start_date" name="start_date" placeholder="Start Date">
                    </div>
                    <div class="column col_2 m-b">
                        <input type="text" class="flatpickr" id="stop_date" name="stop_date" placeholder="Stop Date">
                    </div>
                    <div class="column col_2 m-b">
                        <button type="button" id="filterDate" class="btn small_btn">Filter</button>
                    </div>
                </div>

                <div class="the_stats clearfix mb border_bottom mb" id="this_box">
                    <div class="active_fill column col_4">
                        <span class="small_faint uppercased weight_medium">Total Campaigns</span>
                        <h3>{{ count($all_campaigns) }}</h3>
                    </div>

                    <div class="column col_4">
                        <span class="small_faint uppercased weight_medium">Total Spend</span>
                        <h3>&#8358; {{ $total_spent ? number_format($total_spent, 2) : 0 }}</h3>
                    </div>

                    <div class="column col_4">
                        <span class="small_faint uppercased weight_medium">Brands</span>
                        <h3>{{ count($all_brands) }}</h3>

                        {{--<a href="" class="weight_medium small_font view_brands">View Brands</a>--}}
                    </div>
                </div>

                <div class="the_stats clearfix mb border_bottom mb" id="show_this" style="display: none">

                </div>


                <div class="main_chart padd">
                    <p><br></p><br>
                    <div id="container" style="min-width: 310px; height: 350px; margin: 0 auto"></div>
                </div>

            </form>

        </div>


        <div class="the_frame client_dets mb4">

            <div class="tab_header m4 border_bottom clearfix">
                <a href="#history">Campaign History</a>
                <a href="#brands">Brands</a>
            </div>

            <div class="tab_contain">
                <div class="tab_content" id="history">

                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Product</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Budget</th>
                            <th>Ad Slots</th>
                            <th>Status</th>
                        </tr>
                        @foreach($all_campaigns as $all_campaign)
                            <tr>
                                <td>243</td>
                                <td><a href="{{ route('broadcaster.campaign.details', ['id' => $all_campaign->campaign_id]) }}">{{ $all_campaign->name }}</a></td>
                                <td>{{ ucfirst($all_campaign->brand) }}</td>
                                <td>{{ $all_campaign->product }}</td>
                                <td>{{ $all_campaign->start_date }}</td>
                                <td>{{ $all_campaign->stop_date }}</td>
                                <td>&#8358;{{ $all_campaign->budget }}</td>
                                <td>{{ $all_campaign->adslots }}</td>
                                @if($all_campaign->status === 'active')
                                    <td><span class="span_state status_success">Active</span></td>
                                @elseif($all_campaign->status === 'expired')
                                    <td><span class="span_state status_danger">Expired</span></td>
                                @else
                                    <td><span class="span_state status_pending">Pending</span></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>

                </div>
                <!-- end -->

                <!-- brand -->
                <div class="tab_content" id="brands">

                    <div class="similar_table p_t">
                        <div class="filters clearfix mb">
                            <div class="right col_6 clearfix">


                                <div class="col_12 column align_right">
                                    <a href="#new_brand" class="btn small_btn modal_click"><span class="_plus"></span> New Brand</a>
                                </div>
                            </div>
                        </div>
                        <!-- table header -->
                        <div class="_table_header clearfix m-b">
                            <span class="weight_medium small_faint block_disp column col_4 padd">Brand</span>
                            <span class="weight_medium small_faint block_disp column col_2">All Campaigns</span>
                            <span class="weight_medium small_faint block_disp column col_2">Total Expense</span>
                            <span class="weight_medium small_faint block_disp column col_3">Last Campaign</span>
                            <span class="weight_medium block_disp column col_1 color_trans">.</span>
                        </div>

                        <!-- table item -->
                        @foreach($all_brands as $all_brand)
                            <div class="_table_item the_frame clearfix">
                                <div class="padd column col_4">
                                    <span class="client_ava"><img src="{{ $all_brand['image_url'] ? asset($all_brand['image_url']) : '' }}"></span>
                                    <p>{{ ucfirst($all_brand['brand']) }}</p>
                                    <span class="small_faint">Added {{ date('M j, Y', strtotime($all_brand['date'])) }}</span>
                                </div>
                                <div class="column col_2">{{ $all_brand['campaigns'] }}</div>
                                <div class="column col_2">&#8358; {{ $all_brand['total'] }}</div>
                                <div class="column col_3">{{ ucfirst($all_brand['last_campaign']) }}</div>
                                <div class="column col_1">
                                <span class="more_icon">
                                    <!-- more links -->
                                    <div class="list_more">
                                        <span class="more_icon"></span>

                                        <div class="more_more">
                                            <a href="{{ route('brand.details', ['id' => $all_brand['id'], 'client_id' => $client_id]) }}">Details</a>
                                            <a href="#brand{{ $all_brand['id'] }}" class="modal_click">Edit</a>
                                            {{--<a href="" class="color_red">Delete</a>--}}
                                        </div>
                                    </div>
                                </span>
                                </div>
                            </div>
                    @endforeach
                    <!-- table item end -->
                    </div>

                </div>
                <!-- end -->
            </div>
        </div>
    </div>

    {{--modal for adding up brands--}}
    <div class="modal_contain" id="new_brand">
        <h2 class="sub_header mb4">New Brand</h2>
        <form action="{{ route('brand.store') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="clearfix">
                <div class="input_wrap column col_7{{ $errors->has('brand_name') ? ' has-error' : '' }}">
                    <label class="small_faint">Brand Name</label>
                    <input type="text" required name="brand_name" class="brands_name" id="brands_name" placeholder="e.g Coca Cola">
                    @if($errors->has('brand_name'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('brand_name') }}</span>
                        </strong>
                    @endif
                </div>
                <div class='column col_5 brand_upload_button file_select align_center pt3{{ $errors->has('brand_logo') ? ' has-error' : '' }}' style="height: 70px;">
                    <input type="file" class="brand_logo" />
                    <span class="small_faint block_disp mb3">Brand Logo</span>
                    @if($errors->has('brand_logo'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('brand_logo') }}</span>
                        </strong>
                    @endif
                </div>
                <input type="hidden" name="image_url" required class="brand_logo_url">
                <div class='column col_5 brand_uploaded_image align_center pt3' style="display: none;">

                </div>
                <div class="upload_new_brand" style="font-size: 12px; padding-left: 250px; display: none;">
                    <input class="brand_logo upload_new_brand" name="brand_logo" type="file">
                </div>
            </div>

            <input type="hidden" name="walkin_id" value="{{ $client_id }}">

            <div class="input_wrap">
                <label class="small_faint">Industry</label>

                <div class="select_wrap">
                    <select name="industry" required id="industry">
                        <option value="">Select Industry</option>
                        @foreach($industries as $industry)
                            <option value="{{ $industry->sector_code }}">{{ $industry->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="input_wrap">
                <label class="small_faint">Sub Industry</label>

                <div class="select_wrap">
                    <select name="sub_industry" required class="sub_industry" id="sub_industry">

                    </select>
                </div>
            </div>

            <div class="align_right">
                <input type="submit" value="Create Brand" class="btn uppercased update">
            </div>

        </form>
    </div>

    {{--modal for editing brands--}}
    @foreach($all_brands as $all_brand)
        <div class="modal_contain" id="brand{{ $all_brand['id'] }}">
            <h2 class="sub_header mb4">Edit Brand : {{ $all_brand['brand'] }}</h2>
            <form action="{{ route('brands.update', ['id' => $all_brand['id']]) }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="clearfix">
                    <div class="input_wrap column col_7{{ $errors->has('brand_name') ? ' has-error' : '' }}">
                        <label class="small_faint">Brand Name</label>
                        <input type="text" name="brand_name" required class="brands_update" id="brands_name_update" value="{{ $all_brand['brand'] }}"  placeholder="e.g Coca Cola">
                        @if($errors->has('brand_name'))
                            <strong>
                                <span class="error-block" style="color: red;">{{ $errors->first('brand_name') }}</span>
                            </strong>
                        @endif
                    </div>
                    <div class='column col_5 brand_upload_button align_center pt3{{ $errors->has('brand_logo') ? ' has-error' : '' }}' style="height: 70px;">
                        <img src="{{ asset($all_brand['image_url']) }}" style="width: 100px; height: 100px; padding: 0 0 11px; margin-right: auto; margin-left: auto; margin-top: -65px;">
                    </div>
                    <input type="hidden" name="image_url" required class="brand_logo_url">
                    <div class='column col_5 brand_uploaded_image align_center pt3' style="display: none;">

                    </div>
                    <div class="upload_new_brand" style="font-size: 12px; padding-left: 250px; padding-bottom: 10px;">
                        <input class="brand_logo upload_new_brand" type="file">
                    </div>
                </div>

                <input type="hidden" name="walkin_id" value="{{ $client_id }}">

                <div class="input_wrap">
                    <label class="small_faint">Industry</label>

                    <div class="select_wrap">
                        <select name="industry" required >
                            <option value="">Select Industry</option>
                            @foreach($industries as $industry)
                                @if($industry->sector_code === $all_brand['industry_id'])
                                    <option value="{{ $industry->sector_code }}"
                                            @if($industry->sector_code === $all_brand['industry_id'])
                                            selected
                                            @endif
                                    >{{ $industry->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input_wrap">
                    <label class="small_faint">Sub Industry</label>

                    <div class="select_wrap">
                        <select name="sub_industry" required class="sub_industry">
                            @foreach($sub_industries as $sub_industry)

                                @if($sub_industry->sub_sector_code === $all_brand['sub_industry_id'])
                                    <option value="{{ $sub_industry->sub_sector_code }}"
                                            @if($sub_industry->sub_sector_code === $all_brand['sub_industry_id'])
                                            selected
                                            @endif
                                    >{{ $sub_industry->name }}</option>
                                @endif

                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="align_right">
                    <input type="submit" value="Update Brand" class="btn uppercased update">
                </div>

            </form>
        </div>
    @endforeach

@stop
@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    <script>
        <?php echo "var campaign_date = ".$campaign_date. ";\n"; ?>
        <?php echo "var campaign_amount = ".$campaign_payment. ";\n"; ?>
        $(document).ready(function() {

            flatpickr(".flatpickr", {
                altInput: true,
            });

            //default chart
            Highcharts.chart('container', {
                chart: {
                    type: 'area'
                },
                xAxis: {
                    categories: campaign_date
                },
                title:{
                    text:''
                },
                yAxis: {
                    title: {
                        text: ''
                    },
                    labels: {
                        formatter: function () {
                            return this.value / 1000 + 'k';
                        }
                    }
                },
                tooltip: {
                    pointFormat: '<b>{series.name} {point.y:,.0f}</b><br/> '
                },
                plotOptions: {
                    area: {
                        pointStart: 0,
                        marker: {
                            enabled: false,
                            symbol: 'circle',
                            radius: 2,
                            states: {
                                hover: {
                                    enabled: true
                                }
                            }
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                title:{
                    text:''
                },
                exporting: { enabled: false },
                series: [{
                    name: 'Total Budget',
                    color: '#00C4CA',
                    data: campaign_amount,
                }]
            });

            //filter by date
            $("#filterDate").click(function () {

                $(".content_month").css({
                    opacity: 0.3
                });

                var start_date = $("#start_date").val();
                var stop_date = $("#stop_date").val();
                var url = $("#client_month").attr('action');
                var user_id = "<?php echo $client_id; ?>";

                $.get(url, {'start_date': start_date, 'stop_date': stop_date, 'user_id' : user_id, '_token':$('input[name=_token]').val()}, function(data) {

                    $(".content_month").css({
                        opacity: 1
                    });

                    var big_html =
                        '                    <div class="active_fill column col_4">\n' +
                        '                        <span class="small_faint uppercased weight_medium">Total Campaigns</span>\n' +
                        '                        <h3>'+data.all_campaign.length+'</h3>\n' +
                        '                    </div>\n' +
                        '\n' +
                        '                    <div class="column col_4">\n' +
                        '                        <span class="small_faint uppercased weight_medium">Total Spend</span>\n' +
                        '                        <h3>&#8358;'+ data.all_total +'</h3>\n' +
                        '                    </div>\n' +
                        '\n' +
                        '                    <div class="column col_4">\n' +
                        '                        <span class="small_faint uppercased weight_medium">Brands</span>\n' +
                        '                        <h3>'+data.all_brand.length +'</h3>\n' +
                        '\n' +
                        '                        <a href="" class="weight_medium small_font view_brands">View Brands</a>\n' +
                        '                    </div>\n';

                    $("#this_box").hide();
                    $('#show_this').show();
                    $('#show_this').html(big_html);


                    Highcharts.chart('container', {
                        chart: {
                            type: 'area'
                        },
                        xAxis: {
                            categories: data.monthly_date
                        },
                        title:{
                            text:''
                        },
                        yAxis: {
                            title: {
                                text: ''
                            },
                            labels: {
                                formatter: function () {
                                    return this.value / 1000 + 'k';
                                }
                            }
                        },
                        tooltip: {
                            pointFormat: '<b>{series.name} {point.y:,.0f}</b><br/> '
                        },
                        plotOptions: {
                            area: {
                                pointStart: 0,
                                marker: {
                                    enabled: false,
                                    symbol: 'circle',
                                    radius: 2,
                                    states: {
                                        hover: {
                                            enabled: true
                                        }
                                    }
                                }
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        exporting: { enabled: false },
                        series: [{
                            name: 'Total Budget',
                            color: '#00C4CA',
                            data: data.monthly_total,
                        }]
                    });


                })
            })

            //Yearly filter
            $("#yearly_client").click(function () {
                event.preventDefault();
                $(".content_month").css({
                    opacity: 0.3
                });

                var url = $("#yearly_client").attr('href');
                var user_id = "<?php echo $client_id; ?>";

                $.get(url, {'user_id' : user_id, '_token':$('input[name=_token]').val()}, function(data) {

                    $(".content_month").css({
                        opacity: 1
                    });

                    var big_html =
                        '                    <div class="active_fill column col_4">\n' +
                        '                        <span class="small_faint uppercased weight_medium">Total Campaigns</span>\n' +
                        '                        <h3>'+data.all_campaign.length+'</h3>\n' +
                        '                    </div>\n' +
                        '\n' +
                        '                    <div class="column col_4">\n' +
                        '                        <span class="small_faint uppercased weight_medium">Total Spend</span>\n' +
                        '                        <h3>&#8358;'+ data.all_total +'</h3>\n' +
                        '                    </div>\n' +
                        '\n' +
                        '                    <div class="column col_4">\n' +
                        '                        <span class="small_faint uppercased weight_medium">Brands</span>\n' +
                        '                        <h3>'+data.all_brand.length +'</h3>\n' +
                        '\n' +
                        '                        <a href="" class="weight_medium small_font view_brands">View Brands</a>\n' +
                        '                    </div>\n';

                    $("#this_box").hide();
                    $('#show_this').show();
                    $('#show_this').html(big_html);
                    $("#default").removeClass('active');
                    $("#yearly_client").addClass('active');

                    Highcharts.chart('container', {
                        chart: {
                            type: 'area'
                        },
                        xAxis: {
                            categories: data.monthly_date
                        },
                        title:{
                            text:''
                        },
                        yAxis: {
                            title: {
                                text: ''
                            },
                            labels: {
                                formatter: function () {
                                    return this.value / 1000 + 'k';
                                }
                            }
                        },
                        tooltip: {
                            pointFormat: '<b>{series.name} {point.y:,.0f}</b><br/> '
                        },
                        plotOptions: {
                            area: {
                                pointStart: 0,
                                marker: {
                                    enabled: false,
                                    symbol: 'circle',
                                    radius: 2,
                                    states: {
                                        hover: {
                                            enabled: true
                                        }
                                    }
                                }
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        title:{
                            text:''
                        },
                        exporting: { enabled: false },
                        series: [{
                            name: 'Total Budget',
                            color: '#00C4CA',
                            data: data.monthly_total,
                        }]
                    });


                })

            })

        });

        $(document).ready(function () {
            // $("#state").change(function() {
            $('#industry').on('change', function(e){
                $(".modal_contain").css({
                    opacity: 0.5
                });
                $('.update').attr("disabled", true);
                var industry = $("#industry").val();
                var url = '/walk-in/brand';
                $.ajax({
                    url: url,
                    method: "GET",
                    data: {industry: industry},
                    success: function(data){
                        if(data.error === 'error'){
                            $(".changing").css({
                                opacity: 1
                            });
                            $('.update').attr("disabled", false);
                        }else{
                            $(".modal_contain").css({
                                opacity: 1
                            });
                            $('.update').attr("disabled", false);

                            $('#sub_industry').empty();

                            $('#sub_industry').append(' Please choose one');

                            $.each(data, function(index, title){
                                $("#sub_industry").append('' + '<option value ="'+ title.sub_sector_code + '"  > ' + title.name + '  </option>');
                            });
                        }

                    }
                });
            });

            $(".brands_name").keyup(function () {
                var brand_name = $("input#brands_name").val();
                console.log(brand_name);
                var url = '/check-brand-existence';
                $.ajax({
                    url : url,
                    method : 'GET',
                    data: {brand_name: brand_name},
                    success: function (data) {
                        console.log(data);
                        if(data === 'already_exists'){
                            toastr.info('This brand already exists on our platform, by continuing this process means you are aware of its existence');
                        }
                    }
                })
            });

            $(".brand_logo").on('change', function () {
                var url = '/presigned-url';
                for (var file, i = 0; i < this.files.length; i++) {
                    file = this.files[i];
                    if(file.name && !file.name.match(/.(gif|jpeg|jpg|png|svg)$/i)) {
                        toastr.error('Only Images are allowed');
                        return;
                    }
                    $.ajax({
                        url : url,
                        type : "GET",
                        cache : false,
                        data: {filename : file.name, folder: 'brand-images/'},
                        success: function (data) {
                            console.log(data);
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
                                            $('.progress').html(big_html);
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
                                    toastr.success('Your upload was successful');
                                    var uploadedUrl = 'https:'+data.split('?')[0].substr(6);
                                    $(".brand_logo_url").val(uploadedUrl);
                                    $(".brand_upload_button").hide();
                                    $(".brand_uploaded_image").show();
                                    $(".upload_new_brand").show();
                                    $(".brand_uploaded_image").html('<img src="'+uploadedUrl+'" style="width: 100px;\n' +
                                        '    height: 100px;\n' +
                                        '    padding: 0 0 11px;\n' +
                                        '    margin-right: auto;\n' +
                                        '    margin-left: auto;\n' +
                                        '    margin-top: -45px; " >');
                                })
                                .fail(function(){
                                    toastr.error('An error occurred, please try again ');
                                })
                        }
                    })
                }
            });

        });
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <style>
        .highcharts-grid path { display: none;}
    </style>
@stop
