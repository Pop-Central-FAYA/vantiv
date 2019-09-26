
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// sweetalert package
import VueSweetalert2 from 'vue-sweetalert2';
Vue.use(VueSweetalert2);

// Highcharts package
import Highcharts from 'highcharts'
import HighchartsVue from 'highcharts-vue'
Vue.use(HighchartsVue, {
	highcharts: Highcharts
})

// Moment
import moment from 'moment';

// VeeValidate
import VeeValidate from 'vee-validate';
Vue.use(VeeValidate);

// Vuetify
import Vuetify from 'vuetify';
Vue.use(Vuetify);
import 'vuetify/dist/vuetify.min.css';

import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue);

import 'bootstrap-vue/dist/bootstrap-vue.css';

// Vue MultiSelect
import Multiselect from 'vue-multiselect';
Vue.component('multiselect', Multiselect);

// Vue time picker
import VueTimepicker from 'vue2-timepicker';
Vue.component('vue-timepicker', VueTimepicker);

// declared to manage events globally
window.Event = new Vue();

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// MEDIA PLANNING
Vue.component('media-plan-suggestion-table', () => import('./components/media_plan/customise/SuggestionTable.vue'));
Vue.component('media-plan-suggestion-graph', () => import('./components/media_plan/customise/SuggestionGraph.vue'));
Vue.component('media-plan-suggestion-selected', () => import('./components/media_plan/customise/SelectedSuggestions.vue'));
Vue.component('media-plan-suggestion-filter', () => import('./components/media_plan/customise/FilterSuggestions.vue'));
Vue.component('media-plan-suggestions', () => import('./components/media_plan/customise/Suggestions.vue'));
Vue.component('media-plan-station-rating-table', () => import('./components/media_plan/customise/StationRatingList'));
Vue.component('media-plan-timebelt-table', () => import('./components/media_plan/customise/TimeBeltsTable.vue'));
Vue.component('media-plan-timebelt', () => import('./components/media_plan/customise/TimeBeltsTable.vue'));
Vue.component('media-plan-create-campaign', () => import('./components/media_plan/summary/CreateCampaign.vue'));
Vue.component('media-plan-details', () => import('./components/media_plan/complete/PlanDetails.vue'));
Vue.component('media-plan-program-details', () => import('./components/media_plan/complete/ProgramDetails.vue'));
Vue.component('media-plan-summary', () => import('./components/media_plan/summary/Summary.vue'));
Vue.component('media-plan-criteria-form', () => import('./components/media_plan/CriteriaForm.vue'));
Vue.component('media-plan-list', () => import('./components/media_plan/AllMediaPlans.vue'));
Vue.component('media-plan-request-approval', () => import('./components/media_plan/summary/RequestApproval'));

// CAMPAIGN
Vue.component('campaign-list', () => import('./components/campaign/AllCampaigns.vue'));
Vue.component('campaign-display', () => import('./components/campaign/DisplayCampaign.vue'));
Vue.component('campaign-summary', () => import('./components/campaign/Summary.vue'));

// ASSET MANAGEMENT
Vue.component('media-asset-upload', () => import('./components/asset_management/Upload.vue'));
Vue.component('media-asset-display', () => import('./components/asset_management/DisplayAssets.vue'));
Vue.component('media-asset-delete', () => import('./components/asset_management/DeleteAsset.vue'));
Vue.component('media-asset-play-video', () => import('./components/asset_management/PlayVideo.vue'));


//Schedule
Vue.component('weekly-schedule', () => import('./components/schedule/weekly/WeeklySchedule.vue'));
Vue.component('ad-break-modal', () => import('./components/schedule/partials/AdbreakModal.vue'));
Vue.component('schedule-mpo-filter', () => import('./components/schedule/weekly/MpoFilter.vue'));

//mpo list
Vue.component('campaign-mpos-list', () => import('./components/campaign_mpos/DisplayMpoList.vue'));
Vue.component('mpo-slot-list', () => import('./components/campaign_mpos/DisplayAdslotList.vue'));
Vue.component('mpo-file-manager', () => import('./components/campaign_mpos/AssociateFiles.vue'));
Vue.component('submit-mpo-modal', () => import('./components/campaign_mpos/SubmitMpoModal.vue'));
Vue.component('delete-slots-modal', () => import('./components/campaign_mpos/DeleteSlotModal.vue'));
Vue.component('edit-slots-modal', () => import('./components/campaign_mpos/EditSlotModal.vue'));
Vue.component('campaign-file-list', () => import('./components/campaign_mpos/MpoFileList.vue'));
Vue.component('add-adslot-modal', () => import('./components/campaign_mpos/AddAdslotModal.vue'));
Vue.component('share-link-modal', () => import('./components/campaign_mpos/ShareLinkModal.vue'));
Vue.component('edit-volume-campaign-price', () => import('./components/campaign_mpos/EditVolumePrice.vue'));
Vue.component('list-campaign-adslot', () => import('./components/campaign/ListCampaignAdslot.vue'));
Vue.component('list-group-adslot', () => import('./components/campaign/ListGroupAdslot.vue'));

// AD VENDOR MANAGEMENT
Vue.component('ad-vendor-list', () => import('./components/ad_vendors/VendorList.vue'));
Vue.component('ad-vendor-create', () => import('./components/ad_vendors/CreateVendor.vue'));
Vue.component('ad-vendor-view', () => import('./components/ad_vendors/ViewVendor.vue'));
Vue.component('ad-vendor-publisher-list', () => import('./components/ad_vendors/PublisherList.vue'));
Vue.component('ad-vendor-details', () => import('./components/ad_vendors/VendorDetails.vue'));

// COMPANY MANAGEMENT
Vue.component('company-index', () => import('./components/company/CompanyIndex.vue'));

//CLIENT MANAGEMENT
Vue.component('clients-list', () => import('./components/client/DisplayClients.vue'));
Vue.component('clients-create', () => import('./components/client/CreateClient.vue'));
Vue.component('clients-edit', () => import('./components/client/EditClient.vue'));
Vue.component('client-details', () => import('./components/client/ClientDetails.vue'));


//BRAND MANAGEMENT
Vue.component('brand-list', () => import('./components/brand/ListBrands.vue'));
Vue.component('brand-create', () => import('./components/brand/CreateBrand.vue'));
Vue.component('brands-edit', () => import('./components/brand/EditBrand.vue'));



//GUEST
Vue.component('guest-mpo', () => import('./components/guest/Mpo.vue'));
Vue.component('guest-adslot-list', () => import('./components/guest/AdslotList.vue'));

//Dashboard
Vue.component('dashboard', () => import('./components/dashboard/DisplayDashboard.vue'));
Vue.component('dashboard-campaign-summary', () => import('./components/dashboard/CampaignSummary.vue'));
Vue.component('dashboard-mediaplan-summary', () => import('./components/dashboard/MediaPlanSummary.vue'));
Vue.component('dashboard-campaign-summary-chart', () => import('./components/dashboard/CampaignPieChart.vue'));

//Partials
Vue.component('vantage-side-menu', () => import('./components/partials/Sidebar.vue'));
Vue.component('vantage-header', () => import('./components/partials/Header.vue'));

//USER MANAGEMENT
Vue.component('users-list', () => import('./components/user/UserList.vue'));
Vue.component('invite-user', () => import('./components/user/CreateUser.vue'));
Vue.component('edit-user', () => import('./components/user/EditUser.vue'));


Vue.mixin({
    methods: {
        format_audience(number) {
            return number.toLocaleString();
        },
        format_time(time) {
            time = time.split(":");
            return `${time[0]}h${time[1]}m`;
        },
        sweet_alert(message, type, timer=10000) {
            let background = '';
            if (type === 'success') {
                background = '#28a745';
            } else if (type === 'error') {
                background = '#dc3545';
            } else {
                background = '#17a2b8';
            }
            this.$swal({
                position: 'top-end',
                type: type,
                html: message,
                showConfirmButton: false,
                toast: true,
                background: background,
                timer: timer
            });
        },
        displayServerValidationErrors(validationErrors, withoutKey = null) {
            var err = [];
            for (var key in validationErrors) {
                if (validationErrors.hasOwnProperty(key)) {
                    var errors = validationErrors[key];
                    validationErrors[key].forEach(element => {
                        if(withoutKey){
                            err.push(element)
                            return
                        }
                        err.push(`${key}: ${element}`);
                    });
                }
            }
            this.sweet_alert(err.join('<br>'), 'error');
        },
        formatDate(date_str) {
            if (date_str) {
                var dateParts = date_str.split("-");
                return `${dateParts[0]}-${dateParts[1]}-${dateParts[2].substr(0,2)}`;
            }
            return '';
        },
        shortDay(str) {
            return str.substring(0, 3);
        },
        formatAmount(number) {
            return number.toLocaleString();
        },
        dateToHumanReadable(date) {
            return moment(date).format('MMM DD, YYYY');
        },
        dayName (date) {
            return moment(date).format('dddd')
        },
        numberFormat(n) {
            return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(n);
        },
        hasPermission(permissionList,search_permission){
            if(typeof search_permission === 'string'){
                search_permission = [search_permission]
            }
            const found = permissionList.some(permission => search_permission.indexOf(permission) >= 0)
            return found
        },
        hasPermissionAction(permissionList, permission) {
            if(this.hasPermission(permissionList, permission)){
                return true
            }
            this.sweet_alert('You dont have the permission to perform this action', 'info');
            return false
        },
        addTimebelt(time_belt) {
            Event.$emit('timebelt-to-add', time_belt);
            var time = `${this.format_time(time_belt.start_time)} - ${this.format_time(time_belt.end_time)}`;
            var successMsg = `${time_belt.station} - ${time_belt.program}  showing on  ${time_belt.day}  ${time} added successfully`;
            this.sweet_alert(successMsg, 'success');
        },
        filterMpo : function (mpos, mpo_id){
            return mpos.filter(function(mpo) {
                return mpo.id === mpo_id
            })
        },
        /**
         * @todo This assumes if input is not array it is object, this is an assumption that could cause issues
         * @param  testInput 
         */
        isNotEmpty(testInput){
            if (testInput == null) {
                return false;
            }
            
            if (Array.isArray(testInput)) {
                return testInput.length > 0;
            }
            return Object.keys(testInput).length > 0;
        },
        isEmpty(testInput){
            return this.isNotEmpty(testInput) === false;
        }
    }
})

const app = new Vue({
    el: '#app'
});

