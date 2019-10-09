<template>
    <v-container grid-list-md class="pt-0 pb-3 px-0">
        <v-layout row wrap>
            <v-card px-0 pt-0 style="width: 100%">
                <v-card-title>
                    <v-layout>
                        <v-flex md4 mt-3>
                            <h4 class="mt-4 weight_medium">AVAILABLE STATION & TIMES</h4>
                        </v-flex>
                        <v-flex md8>
                            <media-plan-suggestion-filter :routes="redirectUrls" :selected-filters="selectedFilters" :filter-values="filterValues"></media-plan-suggestion-filter>
                        </v-flex>
                    </v-layout>
                </v-card-title>
                <v-card-text class="px-0 pt-0 pb-0">
                    <v-tabs background-color="deep-purple accent-4" class="elevation-2" :centered="true" :grow="true">
                        <v-tabs-slider></v-tabs-slider>
                        <v-tab :href="`#table-view`"><v-icon class="mr-2">view_list</v-icon> Table</v-tab>
                        <v-tab :href="`#graph-view`"><v-icon class="mr-2">multiline_chart</v-icon> Graph</v-tab>

                        <v-tab-item :value="'table-view'">
                            <v-card flat tile>
                                <v-card-text class="px-1 pb-0">
                                    <media-plan-station-rating-table :selected-time-belts="selectedSuggestions"></media-plan-station-rating-table>
                                </v-card-text>
                            </v-card>
                        </v-tab-item>

                        <v-tab-item :value="'graph-view'">
                            <v-card flat tile>
                                <v-card-text class="pb-0">
                                    <media-plan-suggestion-graph :routes="redirectUrls" :graph-days="graphDays"></media-plan-suggestion-graph>
                                </v-card-text>
                            </v-card>
                        </v-tab-item>

                    </v-tabs>
                </v-card-text>
            </v-card>
        </v-layout>
        <v-layout row wrap mt-5>
            <v-card px-0 pt-0 style="width: 100%">
                <v-card-text class="px-0 pt-0 pb-0">
                    <media-plan-suggestion-selected :plan-id="planId" :selected-time-belts="selectedSuggestions"></media-plan-suggestion-selected>
                </v-card-text>
            </v-card>
        </v-layout>
        <v-layout row wrap class="px-0 py-5">
            <v-flex xs12 sm12 md4 class="px-0">
                <v-btn @click="buttonRedirect(redirectUrls.back_action)" color="vue-back-btn" large><v-icon left>navigate_before</v-icon>Back</v-btn>
            </v-flex>
            <v-flex xs12 s12 md8 class="px-0 text-right">
                <v-btn :disabled="isRunRatings || isMediaPlanPastReviewStage(planStatus)" @click="save(false)" color="default-vue-btn" large><v-icon left>save</v-icon>Save</v-btn>
                <v-btn @click="goToCompletePlan()" color="default-vue-btn" large>Next<v-icon right>navigate_next</v-icon></v-btn>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<style>
    .accent {
        background-color: #44c1c9 !important;
        border-color: #44c1c9 !important;
    }
    .v-window__container {
        height: 100% !important;
    }
    .theme--light.application {
        background: #fafafa !important;
    }
</style>

<script>
    export default {
        props: {
            selectedSuggestions: Array,
            graphDays: Array,
            filterValues: Object,
            selectedFilters: [Object, Array],
            planId: String,
            planStatus: String,
            redirectUrls: Object,
            permissionList:Array,
        },
        data() {
            return {
                timeBeltsArr: [],
                isRunRatings: false,
                viewGraph: false,
                isSaved: false,
                sumNewSelected: 0
            };
        },
        mounted() {
            console.log('Full Suggestions Table Component mounted.')
        },
        created() {
            var self = this;
            Event.$on('selected-timebelts', function (time_belts) {
                self.timeBeltsArr = time_belts;
                self.incrementSelection();
            });
        },
        methods: {
            buttonRedirect(url) {
                window.location = url;
            },
            incrementSelection() {
                this.sumNewSelected++;
            },
            goToCompletePlan() {
                if (this.selectedSuggestions.length == 0 && this.isSaved == false) {
                    this.sweet_alert("Select the station you want to add", 'error');
                } else if (this.selectedSuggestions.length > 0 && this.sumNewSelected > 1 && this.isSaved == false) {
                    this.sweet_alert("New timebelts selected. Please save!", 'info');
                } else {
                    window.location = this.redirectUrls.next_action;
                }
            },
            /**
             * Saving sends the entire media plan suggestion back to the backend
             */
            save(isRedirect) {
                if(this.hasPermissionAction(this.permissionList, ['create.media_plan','update.media_plan'])){
                    if (this.isEmpty(this.timeBeltsArr)) {
                        this.sweet_alert("Select the station you want to add", 'error');
                        return;
                    }

                    this.isRunRatings = true;
                    var msg = `${this.timeBeltsArr.length} suggestion(s) selected. Saving in progress, please wait...`;
                    this.sweet_alert(msg, 'info', null);

                    axios({
                        method: 'post',
                        url: this.redirectUrls.save_action,
                        data: {'data': this.timeBeltsArr}
                    }).then((res) => {
                        this.isSaved = true;
                        this.isRunRatings = false;
                        this.timeBeltsArr = res.data.data;
                        this.sweet_alert("Selected suggestions successfully saved!", 'success');
                    }).catch((error) => {
                        this.isRunRatings = false;
                        if (error.response && (error.response.status == 422)) {
                            this.displayServerValidationErrors(error.response.data.errors);
                        } else {
                            this.sweet_alert('An unknown error has occurred, please try again', 'error');
                        }
                    });
                }
            }
        }
    }
</script>