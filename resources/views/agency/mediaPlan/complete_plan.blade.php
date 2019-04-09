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
                <h2 class="sub_header">Selected stations & programs</h2>
            </div>
        </div>

       
        
<div class="the_frame client_dets mb4">

<div class="filters border_bottom clearfix">
            <div class="column col_8 p-t">
                <p class="uppercased weight_medium">15 sec </p>
            </div>
       
</div>

                <!-- campaigns table -->
                <div class="accordion-group">
                <table lass="display default_mpo filter_mpo" id="default_mpo_table">
                                                            <thead>
                                                                    <tr>
                                    <th>Station</th>
                                    <th>Time belt</th>
                                    <th>Programme</th>
                                    <th>Unit Rate</th>
                                    <th>Volume Disc</th>
                                    @for ($i = 0; $i < count($fayaFound['labeldates']); $i++)
                                    <th>{{ $fayaFound['labeldates'][$i] }}</th>
                                    @endfor



                                    </tr>
                                    </thead>     
                                <tbody >

                                    @foreach($fayaFound['programs_stations'] as  $value)
                                <tr class="{{ $value->program}}">
                                <td id="btn">{{ $value->station}}</td>
                                <td id="btn"> {{ $value->start_time}}  {{ $value->end_time}}</td>
                                <td id="btn"> {{ $value->program}}</td>
                                <td>   <input type="number" value="0" id="ur15{{$value->id}}"  data_12="{{ $value->id}}" data_11="60" data_10="" data_9="" ></td>
                                <td>   <input type="number" value="0" id="vd15{{$value->id}}"  data_12="{{ $value->id}}" data_11="60" data_10="" data_9="" ></td>
                                   

                                    @for ($i = 0; $i < count($fayaFound['dates']); $i++)
                                        @if($fayaFound['days'][$i] == $value->day )
                                        <td>   <input type="number" id="15{{$value->id}}" class="day_input"  data_12="{{ $value->id}}" data_11="15" data_10="{{$fayaFound['dates'][$i]}}" data_9="{{$fayaFound['days'][$i]}}"></td>
                                        @else
                                            <td id=""><input type="number" placeholder="N/A" name="lname" disabled></td>    
                                        @endif
                                    @endfor




                                </tr>

                                @endforeach
                        
                                </tbody>
                                                                </table>
                            

                            
                            

                <!-- end -->
                </div>










</div>



<div class="the_frame client_dets mb4">

<div class="filters border_bottom clearfix">
            <div class="column col_8 p-t">
                <p class="uppercased weight_medium">30 sec </p>
            </div>
       
</div>

                <!-- campaigns table -->
                <div class="accordion-group">
                <table lass="display default_mpo filter_mpo" id="default_mpo_table">
                                                            <thead>
                                                                    <tr>
                                    <th>Station</th>
                                    <th>Time belt</th>
                                    <th>Programme</th>
                                    <th>Unit Rate</th>
                                    <th>Volume Disc</th>
                                    @for ($i = 0; $i < count($fayaFound['dates']); $i++)
                                    <th>{{ $fayaFound['labeldates'][$i] }}</th>
                                    @endfor



                                    </tr>
                                    </thead>     
                                <tbody >

                                    @foreach($fayaFound['programs_stations'] as  $value)
                                <tr class="{{ $value->program}}">
                                <td id="btn">{{ $value->station}}</td>
                                <td id="btn"> {{ $value->start_time}}  {{ $value->end_time}}</td>
                                <td id="btn"> {{ $value->program}}</td>
                                <td>   <input type="number" value="0" id="ur30{{$value->id}}"  data_12="{{ $value->id}}" data_11="60" data_10="" data_9="" ></td>
                                <td>   <input type="number" value="0" id="vd30{{$value->id}}"  data_12="{{ $value->id}}" data_11="60" data_10="" data_9="" ></td>
                                   

                                    @for ($i = 0; $i < count($fayaFound['dates']); $i++)
                                        @if($fayaFound['days'][$i] == $value->day )
                                        <td>   <input type="number" id="30{{$value->id}}" class="day_input"  data_12="{{ $value->id}}" data_11="30" data_10="{{$fayaFound['dates'][$i]}}" data_9="{{$fayaFound['days'][$i]}}"></td>
                                        @else
                                            <td id=""><input type="number" placeholder="N/A" name="lname" disabled></td>    
                                        @endif
                                    @endfor




                                </tr>

                                @endforeach
                        
                                </tbody>
                                                                </table>
                <!-- end -->
                </div>










</div>





<div class="the_frame client_dets mb4">

<div class="filters border_bottom clearfix">
            <div class="column col_8 p-t">
                <p class="uppercased weight_medium">45 sec </p>
            </div>
       
</div>

                <!-- campaigns table -->
                <div class="accordion-group">
                <table lass="display default_mpo filter_mpo" id="default_mpo_table">
                                                            <thead>
                                                                    <tr>
                                    <th>Station</th>
                                    <th>Time belt</th>
                                    <th>Programme</th>
                                    <th>Unit Rate</th>
                                    <th>Volume Disc</th>
                                    @for ($i = 0; $i < count($fayaFound['dates']); $i++)
                                    <th>{{ $fayaFound['labeldates'][$i] }}</th>
                                    @endfor
                                    </tr>
                                    </thead>     
                                <tbody >

                                    @foreach($fayaFound['programs_stations'] as  $value)
                                <tr class="{{ $value->program}}">
                                <td id="btn">{{ $value->station}}</td>
                                <td id="btn"> {{ $value->start_time}}  {{ $value->end_time}}</td>
                                <td id="btn"> {{ $value->program}}</td>
                                <td>   <input type="number" value="0" id="ur45{{$value->id}}"  data_12="{{ $value->id}}" data_11="60" data_10="" data_9="" ></td>
                                <td>   <input type="number" value="0" id="vd45{{$value->id}}"  data_12="{{ $value->id}}" data_11="60" data_10="" data_9="" ></td>
                                   

                                    @for ($i = 0; $i < count($fayaFound['dates']); $i++)
                                        @if($fayaFound['days'][$i] == $value->day )
                                        <td>   <input type="number" id="45{{$value->id}}" class="day_input"  data_12="{{ $value->id}}" data_11="45" data_10="{{$fayaFound['dates'][$i]}}" data_9="{{$fayaFound['days'][$i]}}"></td>
                                        @else
                                            <td id=""><input type="number" placeholder="N/A" name="lname" disabled></td>    
                                        @endif
                                    @endfor




                                </tr>

                                @endforeach
                        
                                </tbody>
                                                                </table>
                            

                            
                            

                <!-- end -->
                </div>










</div>



<div class="the_frame client_dets mb4">

<div class="filters border_bottom clearfix">
            <div class="column col_8 p-t">
                <p class="uppercased weight_medium">60 sec </p>
            </div>
       
</div>

                <!-- campaigns table -->
                <div class="accordion-group">
                <table lass="display default_mpo filter_mpo" id="default_mpo_table">
                                                            <thead>
                                                                    <tr>
                                    <th>Station</th>
                                    <th>Time belt</th>
                                    <th>Programme</th>
                                    <th>Unit Rate</th>
                                    <th>Volume Disc</th>
                                    @for ($i = 0; $i < count($fayaFound['labeldates']); $i++)
                                    <th>{{ $fayaFound['labeldates'][$i] }}</th>
                                    @endfor



                                    </tr>
                                    </thead>     
                                <tbody >

                                    @foreach($fayaFound['programs_stations'] as  $value)
                                <tr class="{{ $value->program}}">
                                <td id="btn">{{ $value->station}}</td>
                                <td id="btn"> {{ $value->start_time}}  {{ $value->end_time}}</td>
                                <td id="btn"> {{ $value->program}}</td>
                                <td>   <input type="number" value="0" id="ur60{{$value->id}}"  data_12="{{ $value->id}}" data_11="60" data_10="" data_9="" ></td>
                                <td>   <input type="number" value="0" id="vd60{{$value->id}}"  data_12="{{ $value->id}}" data_11="60" data_10="" data_9="" ></td>
                                    @for ($i = 0; $i < count($fayaFound['dates']); $i++)
                                        @if($fayaFound['days'][$i] == $value->day )
                                            <td>   <input type="number" id="60{{$value->id}}" class="day_input"  data_12="{{ $value->id}}" data_11="60" data_10="{{$fayaFound['dates'][$i]}}" data_9="{{$fayaFound['days'][$i]}}" data_8="" ></td>
                                        @else
                                            <td id=""><input type="number" placeholder="N/A" name="lname" disabled></td>    
                                        @endif
                                    @endfor




                                </tr>

                                @endforeach
                        
                                </tbody>
                                                                </table>
                            

                            
                            

                <!-- end -->
                </div>

</div>



   <div class="clearfix mb">

   <div class="input_wrap column col_4">
<div class="select_wrap{{ $errors->has('client') ? ' has-error' : '' }}">
                                <select name="client" id="client_name"required>
                                    <option>Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}"
                                                @if((Session::get('campaign_information')) != null)
                                                @if($campaign_general_information->client === $client->id))
                                                selected="selected"
                                            @endif
                                            @endif
                                        >{{ $client->company_name }}</option>
                                    @endforeach
                                </select>

                                @if($errors->has('client'))
                                    <strong>{{ $errors->first('client') }}</strong>
                                @endif
                            </div>



                     
                           
                        </div>


                         <div class="input_wrap column col_4">
                            <label class="small_faint">Product name</label>
                            <input type="text" id="product_name"  name="age_groups[0][max]" placeholder="Product name">
                            <input type="hidden" id="plan_id" value="{{$fayaFound['programs_stations'][0]->media_plan_id}}">
                           
                        </div>
                    </div>




<div class="the_frame client_dets mb4">



<div class="col_4 column">
    <button type="button" id="mpo_filters" class="btn small_btn show">Create Plan</button>
</div>

</div>

<br><br><br><br><br><br><br>


    </div>
@stop

@section('scripts')
    <script src="https://unpkg.com/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
$(document).ready(function(){
     var plans = [
  ];
  var url = window.location.href;
               
                var trim = url.split('/');
               
                var fifthSegment = trim[6];
  $(".day_input").change(function(){
      


    var value_button = $(this).attr("data_12");
    var duration = $(this).attr("data_11");
    var date = $(this).attr("data_10");
    var day = $(this).attr("data_9");
    var slot = $("#"+duration+value_button).val();
    var unit_rate = $("#ur"+duration+value_button).val();
    var volume_disc = $("#vd"+duration+value_button).val();
if(plans.length > 0){
for(var i = 0; i < plans.length; i++){
    if(plans[i].id == value_button && plans[i].date == date && plans[i].duration == duration){

        plans.splice(i, 1)

    }
   
}
}

     plans.push({'id': value_button, 'material_length': duration,  "unit_rate":unit_rate, "volume_disc": volume_disc,  'date': date, 'day': day, 'slot': slot});
     
      
      
      
  });

      	$("body").delegate(".show", "click", function() {

               $('.show').prop('disabled', true);  
                 var client_name = $("#client_name").val();
                    var product_name = $("#product_name").val();
                    var product_name = $("#product_name").val();
                    var plan_id = $("#plan_id").val();
                     var body = {
                                "_token": "{{ csrf_token() }}",
                                "client_name":client_name,
                                "product_name" :product_name,
                                "plan_id": plan_id,
                                "data": JSON.stringify(plans)
                              }
                              console.log(plans.length )
                        if(plans.length == 0 ){
                            swal("input atleast one spot");
                            $('.show').prop('disabled', false);  
                        }else{
                           
                            if(client_name == "Select Client" && product_name == ""){
                                swal("Select client and enter product name");
                                $('.show').prop('disabled', false);  
                            }else{
                                
                              $.ajax({
                                        type: "POST",
                                        dataType: 'json',
                                        url: "/agency/media-plan/finish_plan",
                                        data: body,
                                            success:function(data){
                                                    console.log(data)
                                                swal("Success!", "Plans successfully selected!", "success")
                                                    .then((value) => {
                                                        location.href = '/agency/media-plan/summary/'+fifthSegment;
                                                    });
                                            },

                                            error: function() {
                                                    alert("some error");
                                                    $('.show').prop('disabled', false);  
                                                }
                           
                                   }); 



                            }

                        } 


                
                

           });
});
</script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@stop