<template>
    <div class="container-fluid">
        <div class="row justify-content-center mb-4">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5 py-4">
                                <h4><b>AVAILABLE STATIONS AND TIMES</b></h4>
                            </div>
                            <div class="col-md-7">
                                <media-plan-suggestion-filter :plan-id="planId" :selected-filters="selectedFilters" :filter-values="filterValues"></media-plan-suggestion-filter>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-0 py-0">
                        <div class="row px-5 py-3 mb-0">
                            <div class="col-md-6 px-2">
                                <button @click="toggle_suggestion_view('table')"  :class="'btn full block_disp uppercased align_center '+ tableControl.toggle">Table</button>
                            </div>
                            <div class="col-md-6 px-2">
                                <button @click="toggle_suggestion_view('graph')"  :class="'btn full block_disp uppercased align_center '+ graphControl.toggle">Graph</button>
                            </div>
                        </div>
                        <div class="row" v-show="tableControl.show">
                            <media-plan-suggestion-table :suggestions="suggestions"></media-plan-suggestion-table>
                        </div>
                        <div class="row" v-show="graphControl.show">
                            <media-plan-suggestion-graph :graph-days="graphDays" :graph-details="graphDetails"></media-plan-suggestion-graph>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            suggestions: Object,
            graphDays: Array,
            graphDetails: Object,
            filterValues: Object,
            selectedFilters: Object,
            planId: String
        },
        data() {
            return {
                accordionCounter: 0,
                newTimeBelt: {},
                graphControl: {
                    show: false,
                    toggle: 'inactive-dashboard-toggle-btn'
                },
                tableControl: {
                    show: true,
                    toggle: ''
                }
            };
        },
        mounted() {
            console.log('Suggestions Table Component mounted.')
        },
        methods: {
            total_audience_per_station(timeBelts) {
                var totalAudience = 0;
                timeBelts.forEach(function (element) {
                    totalAudience += element.total_audience;
                });
                return this.format_audience(totalAudience);
            },
            add_timebelt(timeBelt) {
                this.newTimeBelt = timeBelt;
                Event.$emit('timebelt-to-add', timeBelt);
                var time = `${this.format_time(timeBelt.start_time)} - ${this.format_time(timeBelt.end_time)}`;
                var successMsg = `${timeBelt.station} - ${timeBelt.program}  showing on  ${timeBelt.day}  ${time} added successfully`;
                this.sweet_alert(successMsg, 'success');
            },
            toggle_suggestion_view(view) {
                if (view == 'table') {
                    this.tableControl.show = true;
                    this.graphControl.show = false;
                    this.graphControl.toggle = 'inactive-dashboard-toggle-btn';
                    this.tableControl.toggle = '';
                } else {
                    this.graphControl.show = true;
                    this.tableControl.show = false;
                    this.tableControl.toggle = 'inactive-dashboard-toggle-btn';
                    this.graphControl.toggle = '';
                }
            }
        }
    }
</script>