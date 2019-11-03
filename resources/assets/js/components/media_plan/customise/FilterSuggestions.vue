<template>
     <v-container grid-list-md class="p-0">
        <v-layout row wrap class="white-bg">
            <v-flex xs12 sm12 md12 lg12>
                <v-expansion-panel class="filter-stations-panel">
                    <v-expansion-panel-content>
                        <template v-slot:header>
                            <div><h4 class="weight_medium">Filter Station & Times</h4></div>
                        </template>
                        <v-card flat tile>
                            <v-card-text>
                                <v-layout wrap>
                                    <v-flex xs12 sm3 md3 px-1>
                                        <span>Station Type:</span>
                                            <v-select class="mt-0 pt-1" v-model="selectedFilters.station_type" :items="filterValues['station_type']"></v-select>
                                    </v-flex>
                                    <v-flex xs12 sm3 md3 px-1>
                                        <span>Days:</span>
                                            <v-select class="mt-0 pt-1" v-model="selectedFilters.day" :items="filterValues['day']"></v-select>
                                    </v-flex>
                                    <v-flex xs12 sm2 md2 px-1>
                                        <span>States:</span>
                                            <v-select class="mt-0 pt-1" v-model="selectedFilters.state" :items="filterValues['state']"></v-select>
                                    </v-flex>
                                    <v-flex xs12 sm2 md2 px-1>
                                        <span>Day Parts:</span>
                                            <v-select class="mt-0 pt-1" v-model="selectedFilters.day_part" :items="filterValues['day_part']"></v-select>
                                    </v-flex>
                                    <v-flex xs12 sm2 md2 pt-4 px-1>
                                        <v-btn class="filter-suggestions" @click="createNewRatings" color="default-vue-btn">
                                            <v-icon>search</v-icon> FILTER
                                        </v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-card-text>
                        </v-card>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<style>
    .v-text-field .v-input__slot {
        padding: 2px 9px;
        min-height: 30px;
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
    .filter-suggestions.v-btn {
        height: 35px !important;
        width: 100%;
    }
    .filter-stations-panel .v-expansion-panel__header {
        padding: 5px 20px;
        min-height: 30px;
    }
</style>

<script>
    export default {
        props: {
            filterValues: Object,
            routes: Object
        },
        data() {
            return {
                selectedFilters: {
                    'station_type': 'Network',
                    'day': 'all',
                    'state': 'all',
                    'day_part': 'all'
                }
            };
        },
        mounted() {
            console.log('Filter Suggestions Component mounted.');
            this.createNewRatings();
        },
        methods: {
            createNewRatings() {
                this.sweet_alert('Getting station list based on filters', 'info', 60000);
                axios({
                    method: 'get',
                    url: this.routes.new_ratings_action,
                    params: this.selectedFilters
                }).then((res) => {
                    if (this.isNotEmpty(res.data.data)) {
                        this.sweet_alert('Ratings retrieved', 'success');
                        const eventData = {"data": res.data.data, "filters": this.selectedFilters};
                        Event.$emit('ratings-created', eventData);
                    } else {
                        this.sweet_alert('No results found, please try another filter', 'error');
                    }
                 }).catch((error) => {
                     if (error.response && (error.response.status == 422)) {
                        this.displayServerValidationErrors(error.response.data.errors);
                    } else {
                        this.sweet_alert('An unknown error has occurred, please try again', 'error');
                    }
                 })
            }
        }
    }
</script>
