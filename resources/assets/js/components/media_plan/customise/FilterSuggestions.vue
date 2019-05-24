<template>
   <form>
        <div class="row">
            <div class="col-md-3 px-1">
                <label for="station_type">Station Type</label>
                <select name="station_type" v-model="stationType">
                    <option v-for="(station,key) in filterValues['station_type']" v-bind:key="key" :value="station">{{station}}</option>
                </select>                    
            </div>
            <div class="col-md-2 px-1">
                <label for="days">Days</label>
                <select name="days" v-model="days">
                    <option value="all" selected="true">All</option>
                    <option v-for="(day,key) in filterValues['days']" v-bind:key="key" :value="day">{{day}}</option>
                </select>
            </div>
            <div class="col-md-2 px-1">
                <label for="states">States</label>
                <select name="states" v-model="states">
                    <option value="all" selected="true">All</option>
                    <option v-for="(state,key) in filterValues['state_list']" v-bind:key="key" :value="state">{{state}}</option>
                </select>
            </div>
            <div class="col-md-2 px-1">
                <label for="day_parts">Day Parts</label>
                <select name="day_parts" v-model="dayParts">
                    <option value="all" selected="true">All</option>
                    <option v-for="(day_part,key) in filterValues['day_parts']" v-bind:key="key" :value="day_part">{{day_part}}</option>
                </select>                    
            </div>
            <div class="col-md-3 px-1">
                <button type="button" @click="filter_suggestions" class="filter-btn" id="filter-btn"><i class="material-icons">search</i>FILTER</button>
            </div>
        </div>
    </form>
</template>

<script>
    export default {
        props: {
            filterValues: Object,
            selectedFilters: Object,
            planId: String
        },
        data() {
            return {
                dayParts: 'all',
                states: 'all',
                days: 'all',
                stationType: 'Network'
            };
        },
        mounted() {
            console.log('Filter Suggestions Component mounted.');
        },
        created() {
            this.set_selected_filters();
        },
        methods: {
            set_selected_filters() {
                if (this.selectedFilters['station_type']) { 
                    this.stationType = this.selectedFilters['station_type'];
                } 
                if (this.selectedFilters['days']) {
                    this.days = this.selectedFilters['days'];
                } 
                if (this.selectedFilters['dayParts']) {
                    this.dayParts = this.selectedFilters['day_parts'];
                } 
                if (this.selectedFilters['states']) {
                    this.states = this.selectedFilters['states'];
                }
            },
            filter_suggestions(e) {
                $("#load_this").css({ opacity: 0.2 });
                var msg = "Setting up filters, please wait";
                this.sweet_alert(msg, 'info');
                axios({
                    method: 'post',
                    url: '/agency/media-plan/customise-filter',
                    data: {
                        day_parts: this.dayParts,
                        states: this.states,
                        days: this.days,
                        station_type: this.stationType,
                        mediaPlanId: this.planId
                    }
                }).then((res) => {
                    if (res.data.status === 'success') {
                        this.sweet_alert(res.data.message, 'success');
                        window.location = '/agency/media-plan/vue/customise/' + res.data.redirect_url;
                    } else {
                        this.sweet_alert('An unknown error has occurred, please try again', 'error');
                        $('#load_this_div').css({opacity: 1});
                    }
                }).catch((error) => {
                    console.log(error.response.data);
                    this.sweet_alert('An unknown error has occurred, please try again', 'error');
                    $('#load_this_div').css({opacity: 1});
                });
            }
        }
    }
</script>
