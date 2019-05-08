@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Create Rate Card</title>
@stop

@section('content')

    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')

    <div class="main_contain">
        {{--Header--}}
        @include('partials.new-frontend.broadcaster.header')
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Create Rate Card</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color pt">
            <form action="{{ route('rate_card.management.store') }}" method="post">
                {{ csrf_field() }}
                <div class="margin_center col_11 clearfix pt4 create_fields">

                    <div class="clearfix mb3">
                        <div class="input_wrap column col_4 {{ $errors->has('title') ? ' has-error' : '' }}">
                            <label class="small_faint">Rate Card Name</label>
                            <div class="">
                                <input type="text" name="title" required placeholder="Rate Card Name">

                                @if($errors->has('title'))
                                    <strong>
                                        <span class="help-block">
                                            {{ $errors->first('title') }}
                                        </span>
                                    </strong>
                                @endif
                            </div>
                        </div>
                        <br>
                        <div class="create_check clearfix mb3{{ $errors->has('is_base') ? ' has-error' : '' }}">
                            @if($rate_card_count == 0)
                            <input name="is_base" type="hidden" value="{{ true }}"/>
                            @endif

                            <ul>
                                <li class="col_4 column m-b">
                                    <input name="is_base" value="{{ true }}"
                                           @if($rate_card_count == 0)
                                                checked
                                                disabled
                                           @endif
                                           type="checkbox" id='base_rate'>
                                    <label for="base_rate">Base Rate</label>
                                </li>
                            </ul>

                                @if($errors->has('is_base'))
                                    <strong>
                                            <span class="help-block">
                                                {{ $errors->first('is_base') }}
                                            </span>
                                    </strong>
                                @endif
                        </div>
                    </div>
                    <div class="margin_center col_11 clearfix pt4 create_fields">
                        @if($company_id_count > 1)
                            <div class="input_wrap column col_4">
                                <label class="small_faint">Publisher</label>
                                <div class="select_wrap{{ $errors->has('company') ? ' has-error' : '' }}">
                                    <select required name="company">
                                        <option>Select Publisher</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
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
                        @else
                            <input type="hidden" name="company" value="{{ $companies[0]->id }}">
                        @endif
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
                                <tr>
                                    <td>
                                        <div class="input_wrap column">
                                            <label class="small_faint">15 Seconds</label>
                                            <input type="text" readonly required name="duration[]" value="15"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input_wrap column">
                                            <label class="small_faint"></label>
                                            <input type="number" required name="price[]" value=""/>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input_wrap column">
                                            <label class="small_faint">30 Seconds</label>
                                            <input type="text" readonly required name="duration[]" value="30"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input_wrap column">
                                            <label class="small_faint"></label>
                                            <input type="number" required name="price[]" value=""/>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input_wrap column">
                                            <label class="small_faint">45 Seconds</label>
                                            <input type="text" readonly required name="duration[]" value="45"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input_wrap column">
                                            <label class="small_faint"></label>
                                            <input type="number" required name="price[]" value=""/>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input_wrap column">
                                            <label class="small_faint">60 Seconds</label>
                                            <input type="text" readonly required name="duration[]" value="60"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input_wrap column">
                                            <label class="small_faint"></label>
                                            <input type="number" required name="price[]" value=""/>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    {{--<a id="add_custom" class="btn small_btn">Add Custom</a>--}}
                    <!-- end -->

                    <div class="mb4 align_right pt">
                        <input type="submit" value="Create Rate Card" class="btn uppercased mb4">
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
