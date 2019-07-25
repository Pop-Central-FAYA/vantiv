<template>
     <div>
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <div class="margin_center col_10 clearfix create_fields">
                <div class="the_stats the_frame clearfix mb4 mt4">
                    <table class="display dashboard_campaigns">
                        <tbody>
                           <tr>
                            <td><span class="mr-2"><b>Client Name:</b></span>  {{ summaryDetails.client.company_name }}</td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Product Name:</b></span>  {{ summaryDetails.product_name }}</td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Flight Date:</b></span> {{  dateToHumanReadable(summaryDetails.start_date) }} to {{ dateToHumanReadable(summaryDetails.end_date) }} </td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Status:</b></span>  {{ summaryDetails.status }}</td>
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
                                <tr v-for="(summaryData,key) in summaryData" v-bind:key="key">
                                    <td>{{ summaryData.medium }}</td>
                                    <td> <span v-for="(list, index) in summaryData.material_durations" v-bind:key="index"> <span>{{list}}</span><span v-if="index+1 < summaryData.material_durations.length">, </span>  </span> </td>
                                    <td> {{ summaryData.total_spots }} </td>
                                    <td>{{ numberFormat(summaryData.gross_value) }} </td>
                                    <td> {{ numberFormat(summaryData.net_value) }}</td>
                                    <td> {{ numberFormat(summaryData.savings) }}</td>
                                </tr>
                          
                            <tr>
                                <td>Total</td>
                                <td></td>
                                <td>{{ total_spots }}</td>
                                <td>{{ numberFormat(total_gross_value) }}</td>
                                <td>{{ numberFormat( total_net_value) }}</td>
                                <td>{{ numberFormat(total_savings) }}</td>
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
                    <button id="back_btn" @click="buttonAction(routes.back)"  class="btn small_btn"><i class="media-plan material-icons">navigate_before</i> Back</button>
                </div>
                <div class="col-md-8 p-0 text-right">
                      <span v-if="summaryDetails.status == 'Suggested'" >
                            <button v-if="hasPermission(permissionList,'approve.media_plan')"  @click="buttonAction(routes.approve)" class="media-plan btn block_disp uppercased mr-1"><i class="media-plan material-icons">check</i>Approve Plan</button>
                      
                            <button v-if="hasPermission(permissionList,'decline.media_plan')"  @click="buttonAction(routes.decline)"  class="media-plan btn block_disp uppercased bg_red mr-1"><i class="media-plan material-icons">clear</i>Decline Plan</button>
                      </span>
                              <span v-if="summaryDetails.status == 'Approved'" >
                            <button v-if="hasPermission(permissionList,'approve.media_plan')"  @click="buttonAction(routes.approve)" class="media-plan btn block_disp uppercased mr-1">Request Approval</button>
                      </span>
                            <button v-if="hasPermission(permissionList,'export.media_plan')"  @click="buttonAction(routes.export)"  class="btn block_disp uppercased"><i class="media-plan material-icons">file_download</i>Export Plan</button>
                     <span v-if="summaryDetails.status == 'Approved'" >
                            <media-plan-create-campaign v-if="hasPermission(permissionList,'convert.media_plan')" :id="summaryDetails.id"></media-plan-create-campaign>
                     </span>
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
            permissionList:Array,
            routes:Object,
        },
        data() {
            return {
                total_spots: 0,
                total_gross_value: 0,
                total_net_value: 0,
                total_savings: 0,
            };
        },
        mounted() {
             console.log("Summary component mounted");
           
        },  
        created() {    
           this.getSums();

          }, 
         methods: {
                getSums(){
                    let self = this;
                    this.summaryData.forEach(function (item, key) {
                        self.total_spots += item['total_spots']
                        self.total_gross_value += item['gross_value']
                        self.total_net_value += item['net_value']
                        self.total_savings += item['savings']
                    });
                },
                buttonAction(destination) {
                    window.location = destination; 
                },
                   
              
         }
    }
</script>
