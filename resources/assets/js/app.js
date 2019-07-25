
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
import moment from 'moment' 
Vue.use(HighchartsVue, {
	highcharts: Highcharts
})

// VeeValidate
import VeeValidate from 'vee-validate';
Vue.use(VeeValidate);

// Vuetify
import Vuetify from 'vuetify';
Vue.use(Vuetify);
import 'vuetify/dist/vuetify.min.css';

// declared to manage events globally
window.Event = new Vue();
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


// MEDIA PLANNING
Vue.component('media-plan-suggestion-table', require('./components/media_plan/customise/SuggestionTable.vue'));
Vue.component('media-plan-suggestion-graph', require('./components/media_plan/customise/SuggestionGraph.vue'));
Vue.component('media-plan-suggestion-selected', require('./components/media_plan/customise/SelectedSuggestions.vue'));
Vue.component('media-plan-suggestion-filter', require('./components/media_plan/customise/FilterSuggestions.vue'));
Vue.component('media-plan-footer-nav', require('./components/media_plan/customise/FooterNavigation.vue'));
Vue.component('media-plan-suggestions', require('./components/media_plan/customise/Suggestions.vue'));
Vue.component('media-plan-create-campaign', require('./components/media_plan/summary/CreateCampaign.vue'));
Vue.component('media-plan-summary', require('./components/media_plan/summary/Summary.vue'));
Vue.component('media-plan-list', require('./components/media_plan/AllMediaPlans.vue'));

// CAMPAIGN
Vue.component('campaign-list', require('./components/campaign/AllCampaigns.vue'));

// ASSET MANAGEMENT
Vue.component('media-asset-upload', require('./components/asset_management/Upload.vue'));
Vue.component('media-asset-display', require('./components/asset_management/DisplayAssets.vue'));
Vue.component('media-asset-delete', require('./components/asset_management/DeleteAsset.vue'));
Vue.component('media-asset-play-video', require('./components/asset_management/PlayVideo.vue'));


//Schedule
Vue.component('vue-cal-weekly-schedule', require('./components/schedule/weekly/WeeklyScheduleCalender.vue'));
Vue.component('ad-schedule-table', require('./components/schedule/partials/ScheduleTable.vue'));
Vue.component('ad-hour-table', require('./components/schedule/partials/AdHourTable.vue'));
Vue.component('ad-break-table', require('./components/schedule/partials/AdBreakTable.vue'));
//mpo list
Vue.component('campaign-mpos-list', require('./components/campaign_mpos/DisplayMpoList.vue'));
Vue.component('mpo-slot-list', require('./components/campaign_mpos/DisplayAdslotList.vue'));
Vue.component('mpo-file-manager', require('./components/campaign_mpos/AssociateFiles.vue'));
Vue.component('file-modal', require('./components/campaign_mpos/FileModal.vue'));
Vue.component('delete-slots-modal', require('./components/campaign_mpos/DeleteSlotModal.vue'));
Vue.component('edit-slots-modal', require('./components/campaign_mpos/EditSlotModal.vue'));
Vue.component('campaign-file-list', require('./components/campaign_mpos/MpoFileList.vue'));

Vue.mixin({
    methods: {
        format_audience(number) {
            return number.toLocaleString();
        },
        format_time(time) {
            time = time.split(":");
            return `${time[0]}h${time[1]}m`;
        },
        sweet_alert(message, type) {
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
                text: message,
                showConfirmButton: false,
                toast: true,
                background: background,
                timer: 10000
            });
        },
        groupAdslotByProgram(adslots) {
            var helper = {};
            var result = adslots.reduce(function(position, adslot) {
            var group_param = adslot.program + '-' + adslot.playout_date + '-' + adslot.duration;
            if(!helper[group_param]) {
                helper[group_param] = Object.assign({}, adslot); // create a copy of adslot
                position.push(helper[group_param]);
            } else {
                helper[group_param].ad_slots += adslot.ad_slots;
            }
            return position;
            }, []);
            return  result
        },
        formatDate(date_str) {
            var dateParts = date_str.split("-");
            return `${dateParts[0]}-${dateParts[1]}-${dateParts[2].substr(0,2)}`;
        },
        dateToHumanReadable(date) {
            return moment(date).format('Do-MMM-YYYY');
        },
        numberFormat(n) {
            return n.toFixed(2)
        },
        hasPermission(permissionList,search_permission){
            var result =  permissionList.filter(function(permission) {
                return permission.name == search_permission;
            });
              
            if (result.length==0){
                 return false
              }else{
                 return true
              }
     
        },
    }
})
const app = new Vue({
    el: '#app'
});
