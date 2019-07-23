@extends('layouts.ssp.layout')

@section('title')
    <title>Torch | Edit Rate Card</title>
@stop

@section('content')

    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')

    <div class="main_contain">
        {{--Header--}}
        @include('partials.new-frontend.broadcaster.header')
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Edit Rate Card</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color pt">
            <form action="{{ route('rate_card.management.update', ['rate_card_id' => $rate_card->id]) }}" method="post">
                {{ csrf_field() }}
                <div class="margin_center col_11 clearfix pt4 create_fields">

                    <div class="clearfix mb3">
                        <div class="input_wrap column col_4 {{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="small_faint">Rate Card Name</label>
                            <div class="">
                                <input type="text" name="name" required
                                       @if($rate_card->is_base)
                                           readonly is
                                       @endif
                                       value="{{ $rate_card->title }}" placeholder="Rate Card Name">

                                @if($errors->has('name'))
                                    <strong>
                                        <span class="help-block">
                                            {{ $errors->first('name') }}
                                        </span>
                                    </strong>
                                @endif
                            </div>
                        </div>
                        @if(Auth::user()->companies->count() > 1)
                            <div class="input_wrap column col_4">
                                <label class="small_faint">Publisher</label>
                                <div class="{{ $errors->has('company') ? ' has-error' : '' }}">
                                    <select required name="company">
                                        <option value="{{ $rate_card->company->id }}">{{ $rate_card->company->name }}</option>
                                    </select>

                                    @if($errors->has('company'))
                                        <strong>
                                        <span class="help-block">
                                            {{ $errors->first('company') }}
                                        </span>
                                        </strong>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="clearfix mb3">
                        <div class="create_check clearfix mb3">
                            <ul>
                                <li class="col_4 column m-b">
                                    <input name="is_base" value="true"
                                           @if($rate_card->is_base)
                                           checked
                                           disabled
                                           @endif
                                           type="checkbox" id='base_rate'>
                                    <label for="base_rate">Base Rate</label>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="clearfix mb3">
                        <table>
                            <thead>
                            <tr>
                                <th>Media Length</th>
                                <th>Media Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rate_card->rate_card_durations as $rate_card_duration)
                                <tr>
                                    <td>
                                        <div class="input_wrap column">
                                            <label class="small_faint">{{ $rate_card_duration->duration }} Seconds</label>
                                            <input type="text" readonly required name="duration[]" value="{{ $rate_card_duration->duration }}"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input_wrap column">
                                            <label class="small_faint"></label>
                                            <input type="text" required name="price[]" value="{{ $rate_card_duration->price }}"/>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                {{--<a id="add_custom" class="btn small_btn">Add Custom</a>--}}
                <!-- end -->

                    <div class="mb4 align_right pt">
                        <input type="submit" value="Update Rate Card" class="btn uppercased mb4">
                    </div>

                </div>
            </form>
        </div>
        <!-- main frame end -->
    </div><!-- main contain -->
@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('new_frontend/js/wickedpicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('new_frontend/js/aria-accordion.js') }}"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    <script>

        $(document).ready(function () {

            var max = 12;
            var i = 0;
            $("#add_custom").click(function () {
                event.preventDefault();
                i++;
                if (i >= max) {
                    return false;
                }
                var big_html = '';
                big_html += '<tr class="remove_div'+i+'">\n' +
                    '                                    <td>\n' +
                    '                                        <div class="input_wrap column">\n' +
                    '                                            <label class="small_faint">Custom Seconds</label>\n' +
                    '                                            <input type="text" required name="duration[]" value=""/>\n' +
                    '                                        </div>\n' +
                    '                                    </td>\n' +
                    '                                    <td>\n' +
                    '                                        <div class="input_wrap column">\n' +
                    '                                            <label class="small_faint"></label>\n' +
                    '                                            <input type="text" required name="price[]" value=""/>\n' +
                    '                                        </div>\n' +
                    '                                    </td>\n' +
                    '<td>' +
                    '<a href="" id="remove'+i+'" data-button_id="'+i+'" class="uppercased btn small_btn color_initial remove">Remove</a>\n';
                '</td>'+
                '                                </tr>';
                $("tbody").append(big_html);
            });

            $("body").delegate(".remove", "click", function () {
                event.preventDefault();
                var button_id = $(this).data("button_id");
                $(".remove_div"+button_id).remove();

            })

        })
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('new_frontend/css/wickedpicker.min.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link href="{{ asset('new_frontend/css/aria-accordion.css') }}" rel="stylesheet">
@stop
