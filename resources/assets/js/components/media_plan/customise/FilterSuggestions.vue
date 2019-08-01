<template>
    <v-layout wrap>
        <v-flex xs12 sm3 md3>
            <span>Station Type:</span>
                <v-select class="mt-0 pt-1" v-model="stationType" :items="filterValues['station_type']"></v-select>
        </v-flex>
        <v-flex xs12 sm2 md2>
            <span>Days:</span>
                <v-select class="mt-0 pt-1" v-model="days" :items="filterValueDays"></v-select>
        </v-flex>
        <v-flex xs12 sm2 md2>
            <span>States:</span>
                <v-select class="mt-0 pt-1" v-model="states" :items="filterValueStates"></v-select>
        </v-flex>
        <v-flex xs12 sm2 md2>
            <span>Day Parts:</span>
                <v-select class="mt-0 pt-1" v-model="dayParts" :items="filterValueDayParts"></v-select>
        </v-flex>
        <v-flex xs12 sm3 md3 pt-4>
            <v-btn @click="filterSuggestions" color="default-vue-btn">
                <v-icon>search</v-icon> FILTER
            </v-btn>
        </v-flex>
    </v-layout>
</template>

<style>
    .v-text-field .v-input__slot {
        padding: 0px 12px;
        min-height: 45px;
        margin-bottom: 0px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .v-text-field>.v-input__control>.v-input__slot:after, .v-text-field>.v-input__control>.v-input__slot:before {
        content: none;
    }
    .theme--dark.v-btn.v-btn--disabled:not(.v-btn--icon):not(.v-btn--flat):not(.v-btn--outline) {
        background-color: hsl(184, 55%, 53%)!important;
    }
    .v-btn {
        height: 45px !important;
    }
</style>

<script>
    export default {
        props: {
            filterValues: Object,
            selectedFilters: Array,
            planId: String,
            redirectUrls: Object
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
            this.setSelectedFilters();
        },
        computed: {
            filterValueDayParts() {
                var day_parts = Object.values(this.filterValues.day_parts)
                day_parts.splice(0,0,'all');
                return day_parts;
            },
            filterValueDays() {
                var days = Object.values(this.filterValues.days)
                days.splice(0,0,'all');
                return days;
            },
            filterValueStates() {
                var states = Object.values(this.filterValues.state_list)
                states.splice(0,0,'all');
                return states;
            }
        },
        methods: {
            setSelectedFilters() {
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
            filterSuggestions() {
                var msg = "Setting up filters, please wait";
                this.sweet_alert(msg, 'info');
                axios({
                    method: 'post',
                    url: this.redirectUrls.filter_action,
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
                        window.location = res.data.redirect_url;
                    } else {
                        this.sweet_alert('An unknown error has occurred, please try again', 'error');
                    }
                }).catch((error) => {
                    console.log(error.response.data);
                    this.sweet_alert('An unknown error has occurred, please try again', 'error');
                });
            }
        }
    }
</script>
