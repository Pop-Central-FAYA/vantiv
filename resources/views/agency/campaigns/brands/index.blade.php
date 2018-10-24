@extends('layouts.faya_app')
@section('title')
    <title>FAYA | Agency - All Brands</title>
@stop
@section('content')
    <div class="main_contain">
        <!-- header -->
    @include('partials.new-frontend.agency.header')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Brands</h2>
            </div>
        </div>

        <div class="similar_table pt3">
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
                                    <a href="{{ route('campaign.brand.client', ['id' => $all_brand['id'], 'client_id' => $all_brand['client_id']]) }}">Details</a>
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
        <p><br></p>
        {{ $all_brands->links('pagination.general') }}

    </div>
@stop

@section('scripts')
    {{--modal for editing brands--}}
    @foreach($all_brands as $all_brand)
        <div class="modal_contain" id="brand{{ $all_brand['id'] }}">
            <h2 class="sub_header mb4">Edit Brand : {{ $all_brand['brand'] }}</h2>
            <form action="{{ route('brands.update', ['id' => $all_brand['id']]) }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="clearfix">
                    <div class="input_wrap column col_7{{ $errors->has('brand_name') ? ' has-error' : '' }}">
                        <label class="small_faint">Brand Name</label>
                        <input type="text" name="brand_name" value="{{ $all_brand['brand'] }}"  placeholder="e.g Coca Cola">
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

                <input type="hidden" name="walkin_id" value="{{ $all_brand['client_id'] }}">

                <div class="input_wrap">
                    <label class="small_faint">Industry</label>

                    <div class="select_wrap">
                        <select name="industry" id="industry">
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
                        <select name="sub_industry" id="sub_industry">
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
    <script>
        $(document).ready(function () {
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
                                        '    margin-top: -27px; " >');
                                })
                                .fail(function(){
                                    toastr.error('An error occurred, please try again ');
                                })
                        }
                    })
                }
            });
        })
    </script>
@stop
