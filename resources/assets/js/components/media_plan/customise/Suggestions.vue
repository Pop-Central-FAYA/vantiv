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
                            <media-plan-suggestion-filter :redirect-urls="redirectUrls" :plan-id="planId" :selected-filters="selectedFilters" :filter-values="filterValues"></media-plan-suggestion-filter>
                        </v-flex>
                    </v-layout>
                </v-card-title>
                <v-card-text class="px-0 pt-0 pb-0">
                    <v-tabs background-color="deep-purple accent-4" class="elevation-2" :centered="true" :grow="true">
                        <v-tabs-slider></v-tabs-slider>
                        <v-tab :href="`#table-view`"><v-icon class="mr-2">view_list</v-icon> Table</v-tab>
                        <v-tab :href="`#graph-view`" @click="renderGraph"><v-icon class="mr-2">multiline_chart</v-icon> Graph</v-tab>
                        <v-tab-item :value="'table-view'">
                            <v-card flat tile>
                                <v-card-text class="px-1 pb-0">
                                    <media-plan-suggestion-table :suggestions="suggestions"></media-plan-suggestion-table>
                                </v-card-text>
                            </v-card>
                        </v-tab-item>
                        <v-tab-item :value="'graph-view'" v-if="viewGraph">
                            <v-card flat tile>
                                <v-card-text class="pb-0">
                                    <media-plan-suggestion-graph :suggestions="suggestions" :graph-days="graphDays" :graph-details="graphDetails"></media-plan-suggestion-graph>
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
                <v-btn :disabled="isRunRatings || planStatus =='Approved' ||  planStatus =='Declined'" @click="save(false)" color="default-vue-btn" large><v-icon left>save</v-icon>Save</v-btn>
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
            suggestions: Object,
            selectedSuggestions: Array,
            graphDays: Array,
            graphDetails: Object,
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
            console.log('Suggestions Table Component mounted.')
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
            save(isRedirect) {
                if(this.hasPermissionAction(this.permissionList, ['create.media_plan','update.media_plan'])){
                    var suggestionIds = [];
                    var self = this;
                    this.timeBeltsArr.forEach(function (timeBelt) {
                        suggestionIds.push(timeBelt.id);
                    });

                    if (suggestionIds.length === 0) {
                        this.sweet_alert("Select the station you want to add", 'error');
                    }else {
                        this.isRunRatings = true;
                        var msg = `${self.timeBeltsArr.length} suggestion(s) selected. Saving in progress, please wait...`;
                        this.sweet_alert(msg, 'info', null);
                        axios({
                            method: 'post',
                            url: this.redirectUrls.save_action,
                            data: {
                                data: JSON.stringify(suggestionIds),
                                mediaplan: this.planId
                            }
                        }).then((res) => {
                            this.isSaved = true;
                            this.isRunRatings = false;
                            if (res.data.status === 'success') {
                                this.sweet_alert("Selected suggestions successfully saved!", 'success');
                            } else {
                                this.sweet_alert("Selected suggestions couldn't be saved, please try again", 'error');
                            }
                        }).catch((error) => {
                            this.isRunRatings = false;
                            this.sweet_alert("An unknown error has occurred, please try again", 'error');
                        });
                    }   
                }
            },
            renderGraph() {
                this.viewGraph = true;
            }
        }
    }
</script>