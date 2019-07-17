<template>
  <div>
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <div class="margin_center col_10 clearfix create_fields">
                <div class="the_stats the_frame clearfix mb4 mt4">
                    <table class="display dashboard_campaigns">
                        <tbody>
                           <tr>
                            <td><span class="mr-2"><b>Client Name:</b></span>  {{ summarydataobj.client.company_name }}</td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Product Name:</b></span>  {{ summarydataobj.product_name }}</td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Flight Date:</b></span> {{ dateToYMD(summarydataobj.start_date) }} to {{ dateToYMD(summarydataobj.end_date) }} </td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Status:</b></span>  {{ summarydataobj.status }}</td>
                           </tr>
                        </tbody>
                    </table>
                </div>

                 <div class="the_frame client_dets mb4">
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
                                <tr v-for="(sumData,key) in sumData" v-bind:key="key">
                                    <td>{{ sumData.medium }}</td>
                                    <td> <span v-for="(list, index) in sumData.material_durations" v-bind:key="index"> <span>{{list}}</span><span v-if="index+1 < sumData.material_durations.length">, </span>  </span> </td>
                                    <td> {{ sumData.total_spots }} </td>
                                    <td>{{ nunberformat(sumData.gross_value) }} </td>
                                    <td> {{ nunberformat(sumData.net_value) }}</td>
                                    <td> {{ nunberformat(sumData.savings) }}</td>
                                </tr>
                          
                            <tr>
                                <td>Total</td>
                                <td></td>
                                <td>{{ total_total_spots }}</td>
                                <td>{{ nunberformat(total_gross_value) }}</td>
                                <td>{{ nunberformat( total_net_value) }}</td>
                                <td>{{ nunberformat(total_savings) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- end -->
                </div>

 
            </div>
        </div>
   

        <div class="container-fluid my-5">
            <div class="row">
                <div class="col-md-4 p-0">
                    <a id="back_btn" href="" class="btn small_btn"><i class="media-plan material-icons">navigate_before</i> Back</a>
                </div>
                <div class="col-md-8 p-0 text-right" >
                  
                            <a href="" class="media-plan btn block_disp uppercased mr-1"><i class="media-plan material-icons">check</i>Approve Plan</a>
                      
                            <a href="" class="media-plan btn block_disp uppercased bg_red mr-1"><i class="media-plan material-icons">clear</i>Decline Plan</a>
                  
                             <a href="" v-if="checkPermmission('export.media_plan')" class="btn block_disp uppercased"><i class="media-plan material-icons">file_download</i>Export Plan</a>


                         <media-plan-create-campaign :id="summarydataobj.id"></media-plan-create-campaign>
                    

                   
                </div>
            </div>
        </div>
         </div>
</template>
<script>
    export default {
        props: {
            summaryDetails: Object,
            summaryData: Array,
            userDetails: Array,
        },
        data() {
            return {
                summarydataobj: this.summaryDetails, 
                sumData: this.summaryData,
                user:this.userDetails,
                total_total_spots: 0,
                total_gross_value: 0,
                total_net_value: 0,
                total_savings: 0,
            };
        },
        mounted() {
            console.log(this.user)
            console.log('Suggestions Table Component mounted.')
            this. getSums();
        },   methods: {
            dateToYMD(date) {
                const months = ["Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                let current_datetime = new Date(date)
                return current_datetime.getDate() + "-" + months[current_datetime.getMonth()] + "-" + current_datetime.getFullYear()

            },
             nunberformat(n) {
                  return n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '');
               },
               getSums(){
                    let self = this;
                    this.sumData.forEach(function (item, key) {
                    self.total_total_spots += item['total_spots']
                    self.total_gross_value += item['gross_value']
                    self.total_net_value += item['net_value']
                    self.total_savings += item['savings']
                    });
               },
               checkPermmission(name){
                    let l = true
                    this.userDetails.forEach(function (item, key) {   
                      if(item['name'] == name){
                           console.log(name);
                      l= true;
                      }else{
                        l=  false;
                         console.log(' wrong');
                      }
                    });

                 return l;
               }

         }
    }
</script>
