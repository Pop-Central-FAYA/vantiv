<template>
     <div>
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <div class="margin_center col_10 clearfix create_fields">
                <div class="the_stats the_frame clearfix mb4 mt4">
                    <table class="display dashboard_campaigns">
                        <tbody>
                           <tr>
                            <td><span class="mr-2"><b>Client Name:</b></span>  {{ summaryDetail.client.name }}</td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Product Name:</b></span>  {{ summaryDetail.product_name }}</td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Flight Date:</b></span> {{  dateToHumanReadable(summaryDetail.start_date) }} to {{ dateToHumanReadable(summaryDetail.end_date) }} </td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Status:</b></span>  {{ summaryDetail.status }}</td>
                           </tr>
                        </tbody>
                    </table>
                </div>

                 <div class="the_frame client_dets mb4">
                    <media-plan-summary-sections :summary-data="summaryData" :full-station-data="fullStationData"></media-plan-summary-sections>
                </div>
            </div>
        </div>
        
        <div class="container-fluid my-5">
            <div class="row">
                <div class="col-md-4 p-0">
                    <button id="back_btn" @click="buttonAction(routes.back)"  class="btn small_btn"><i class="media-plan material-icons">navigate_before</i> Back</button>
                </div>
                <div class="col-md-8 p-0 text-right">
                    <span v-if="summaryDetail.status == 'In Review'">
                        <button v-if="hasPermission(permissionList,'approve.media_plan')"  @click="changeStatus('Approved')" class="media-plan btn block_disp uppercased mr-1 btn-sm"><i class="media-plan material-icons">check</i>Approve Plan</button>
                    
                        <button v-if="hasPermission(permissionList,'decline.media_plan')"  @click="changeStatus('Declined')"  class="media-plan btn block_disp uppercased bg_red mr-1  btn-sm"><i class="media-plan material-icons">clear</i>Decline Plan</button>
                    </span>
                    <span v-if="summaryDetail.status === 'Suggested' || summaryDetail.status === 'Selected' || summaryDetail.status === 'Pending'" >
                        <media-plan-request-approval  
                        :users="userList"  
                        :media-plan="summaryDetail.id" 
                        :action-link="routes.approval"
                        :permissionList="permissionList">
                        </media-plan-request-approval>
                    </span>
                     <span>
                        <button v-if="hasPermission(permissionList,'export.media_plan')"  @click="buttonAction(routes.export, 'export.media_plan')"  class="btn block_disp uppercased"><i class="media-plan material-icons">file_download</i>Export Plan</button>
                          </span>
                    <span v-if="summaryDetail.status == 'Approved'" >
                        <media-plan-create-campaign 
                        v-if="hasPermission(permissionList,'convert.media_plan')" 
                        :id="summaryDetail.id"
                        :permissionList="permissionList"
                        ></media-plan-create-campaign>
                    </span>
                </div>
            </div>
        </div>
 </div>
</template>

<style>
    .btn{
    padding: 7px 10px 5px !important;
    }
</style>

<script>
    export default {
        props: {
            fullStationData: Object,
            summaryDetails: Object,
            summaryData: Array,
            permissionList:Array,
            userList:Array,
            routes:Object,
        },
        data() {
            return {
                totalSpots: 0,
                totalGrossValue: 0,
                totalNetValue: 0,
                totalSavings: 0,
                summaryDetail:  this.summaryDetails
            };
        },
        mounted() {
              this.getSums();
             console.log("Summary component mounted");
           
        },  
        created() {    
            var self = this;
            Event.$on('updated-media-plan', function (mediaPlan) {
                self.summaryDetail = mediaPlan;

            });
          }, 
         methods: {
            getSums(){
                let self = this;
                this.summaryData.forEach(function (item, key) {
                    self.totalSpots += item['total_spots']
                    self.totalGrossValue += item['gross_value']
                    self.totalNetValue += item['net_value']
                    self.totalSavings += item['savings']
                });
            },
            buttonAction(destination, permission) {
                if(permission != null && !this.hasPermissionAction(this.permissionList, permission)){
                    return
                }else{
                    window.location = destination;
                }
            },  
             changeStatus(action) {
                if(this.hasPermissionAction(this.permissionList, ['create.media_plan', 'update.media_plan'])){                    
                    axios({
                        method: 'post',
                        url: this.routes.change_status,
                        data: {
                            media_plan_id: this.summaryDetail.id,
                            action: action
                        }
                    }).then((res) => {
                    if (res.data.status === 'success') {
                        Event.$emit('updated-media-plan', res.data.data);
                        this.sweet_alert('Media Plan was '+ action.toLowerCase()+' successfully', 'success');
                    } else {
                        this.sweet_alert('Something went wrong, Try again!', 'error');
                    }
                    }).catch((error) => {
                        this.sweet_alert(error.response.data.message, 'error');
                    });
                }
            },
        }
    }
</script>
