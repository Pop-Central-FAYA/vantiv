<template>
    <v-container grid-list-md class="py-0 px-0">
        <template>
            <v-container grid-list-md class="py-0 px-0 media-plan-body">
                <div class="the_frame clearfix border_top_color load_stuff">
                    <div class="margin_center col_10 clearfix create_fields">
                        <div class="clearfix mb4 mt2" style="position: relative; margin-bottom: 4rem;">
                            <comment :model-id="planDetails.id" :routes="planDetails.routes.comments"></comment>
                        </div>
                        <div class="the_stats the_frame clearfix mb4 mt4">
                            <table class="display dashboard_campaigns">
                                <tbody>
                                    <tr>
                                    <td><span class="mr-2"><b>Client Name:</b></span>  {{ planDetails.client.name }}</td>
                                    </tr>
                                    <tr>
                                    <td><span class="mr-2"><b>Product Name:</b></span>  {{ planDetails.product_name }}</td>
                                    </tr>
                                    <tr>
                                    <td><span class="mr-2"><b>Flight Date:</b></span> {{  dateToHumanReadable(planDetails.start_date) }} to {{ dateToHumanReadable(planDetails.end_date) }} </td>
                                    </tr>
                                    <tr>
                                    <td><span class="mr-2"><b>Status:</b></span>  {{ (planDetails.status).toUpperCase() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="the_frame client_dets mb">
                            <media-plan-summary-sections :formatted-plan-data="formattedPlanData"></media-plan-summary-sections>
                        </div>
                    </div>
                </div>
            </v-container>
            <v-container grid-list-md class="py-0 px-0">
                <div class="container-fluid mt-4">
                    <div class="row">
                        <div class="col-md-4 p-0">
                            <button id="back_btn" @click="buttonAction(planDetails.routes.summary.back)"  class="btn small_btn"><i class="media-plan material-icons">navigate_before</i> Back</button>
                        </div>
                        <div class="col-md-8 p-0 text-right">
                            <template v-if="planDetails.status == 'pending'">
                                <media-plan-request-review
                                    :users="userList"  
                                    :media-plan="planDetails.id" 
                                    :action-link="planDetails.routes.summary.change_status"
                                    :permissionList="permissionList">
                                </media-plan-request-review>
                            </template>
                            <!-- change permission to finalize.media_plan -->
                            <template v-if="planDetails.status == 'in review' && hasPermission(permissionList,'approve.media_plan')">
                                <button  @click="changeStatus('pending')" class="media-plan btn block_disp uppercased bg_red mr-1 btn-sm">Needs Work</button>
                                <button  @click="changeStatus('finalized')" class="media-plan btn block_disp uppercased mr-1 btn-sm">Finalize Plan</button>
                            </template>
                            <template v-if="planDetails.status == 'finalized' && hasPermission(permissionList,'approve.media_plan')">
                                <button @click="changeStatus('approved')" class="media-plan btn block_disp uppercased mr-1 btn-sm">Approve Plan</button>
                                <button @click="changeStatus('rejected')"  class="media-plan btn block_disp uppercased bg_red mr-1  btn-sm">Reject Plan</button>
                            </template>
                            <template v-if="planDetails.status == 'approved' && hasPermission(permissionList,'convert.media_plan')">
                                <media-plan-create-campaign :id="planDetails.id" :permissionList="permissionList"></media-plan-create-campaign>
                            </template>
                            <template>
                                <button v-if="hasPermission(permissionList,'export.media_plan')"  @click="buttonAction(planDetails.routes.summary.export, 'export.media_plan')"  class="btn block_disp uppercased"><i class="media-plan material-icons">file_download</i>Export Plan</button>
                            </template>
                        </div>
                    </div>
                </div>
            </v-container>
        </template>
    </v-container>
</template>

<style>
    .btn{
    padding: 7px 10px 5px !important;
    }
</style>

<script>
    import SummarySections from "./SummarySections.vue";
    import RequestPlanReview from "./RequestPlanReview.vue";

    export default {
        props: {
            formattedPlanData: Object,
            permissionList:Array,
            userList:Array,
        },
        components: {
            'media-plan-summary-sections': SummarySections,
            'media-plan-request-review': RequestPlanReview,
        },
        data() {
            return {
                planDetails:  this.formattedPlanData.plan
            };
        },
        mounted() {
            console.log("Summary component mounted");
        },  
        created() {    
            var self = this;
            Event.$on('updated-media-plan', function (mediaPlan) {
                self.planDetails = mediaPlan;
            });
          }, 
         methods: {
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
                        url: this.planDetails.routes.summary.change_status,
                        data: {
                            media_plan_id: this.planDetails.id,
                            action: action
                        }
                    }).then((res) => {
                    if (res.data.status === 'success') {
                        Event.$emit('updated-media-plan', res.data.data);
                        this.sweet_alert('Media Plan is '+ action.toLowerCase()+' successfully', 'success');
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
