<template>
    <v-layout row wrap class="white-bg" px-0 pb-0>
        <v-flex xs12 sm12 md12 lg12 px-0 pb-0>
            <v-expansion-panel px-0>
                <v-layout px-4 py-2>
                    <v-flex md6><h4>STATION</h4></v-flex>
                    <v-flex md6 pl-0><h4>AUDIENCE</h4></v-flex>
                </v-layout>
                <v-expansion-panel-content v-for="(timebelts, key, index) in suggestions" v-bind:key="key">
                    <template v-slot:header>
                        <v-layout @click="renderTimeBelts(index)">
                            <v-flex md6>{{ key }}</v-flex>
                            <v-flex md6><span class="pl-2">{{ totalAudiencePerStation(timebelts) }}</span></v-flex>
                        </v-layout>
                    </template>
                    <v-card v-if="stationsToggle[index]['show']" outlined>
                        <v-card-text>
                            <v-layout wrap>
                                <v-flex md12 px-0 style="overflow-x:auto;  height: 40vh;">
                                    <media-plan-timebelt-table :station-time-belts="timebelts"></media-plan-timebelt-table>
                                </v-flex>
                            </v-layout>
                        </v-card-text>
                    </v-card>
                </v-expansion-panel-content>
            </v-expansion-panel>
        </v-flex>
    </v-layout>
</template>

<style>
.v-expansion-panel__header {
    /* padding: 0 !important; */
    font-size: 14px;
}
.v-expansion-panel__body {
    margin: 0px 10px;
}
</style>


<script>
    export default {
        data() {
            return {
                suggestions: {},
                accordionCounter: 0,
                newTimeBelt: {},
                curPos: 0,
                stationsToggle: []
            };
        },
        created() {
            var self = this;
            //Render the table whenever suggestions is updated
            Event.$on('ratings-created', function (data) {
                self.resetSuggestionsList(data.stations);
            });
        },
        mounted() {
            console.log('Suggestions Table Component mounted.')
            this.resetSuggestionsList(this.suggestions);
        },
        methods: {
            resetSuggestionsList(suggestions) {
                this.suggestions = suggestions
                var stations = Object.keys(this.suggestions);
                var stationsToggle = [];
                stations.forEach((element, key) => {
                    stationsToggle[key] = {station: element, show: false}
                });
                this.stationsToggle = stationsToggle;
            },
            totalAudiencePerStation(timeBelts) {
                var totalAudience = 0;
                timeBelts.forEach(function (element) {
                    totalAudience += element.total_audience;
                });
                return this.format_audience(Math.round(totalAudience));
            },
            renderTimeBelts(position) {
                this.stationsToggle[position]['show'] = true;
            }
        }
    }
</script>
