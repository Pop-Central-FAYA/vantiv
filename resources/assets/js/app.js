
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

// VeeValidate
import VeeValidate from 'vee-validate';
Vue.use(VeeValidate);

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
// ASSET MANAGEMENT
Vue.component('media-asset-upload', require('./components/asset_management/Upload.vue'));
Vue.component('media-asset-display', require('./components/asset_management/DisplayAssets.vue'));



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
        }
    }
})
const app = new Vue({
    el: '#app'
});
