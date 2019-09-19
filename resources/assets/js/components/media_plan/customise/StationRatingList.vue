<template>
    <v-layout row wrap class="white-bg" px-0 pb-0 style="height: 45vh !important; overflow: auto;">
        <v-flex xs12 sm12 md12 lg12 px-0 pb-0>
            <v-layout px-4 py-2>
                <v-flex v-for="(header, key) in headers" v-bind:key="key" md6>
                    <h4>{{header.text}} <v-icon class="sort-icon" @click="sortStationRatings(header.value)">{{header.icon}}</v-icon></h4>
                </v-flex>
            </v-layout>
            <v-layout style="height: 45vh !important; overflow: auto;">
                <v-flex md12>
                    <v-expansion-panel px-0>
                        <v-expansion-panel-content v-for="item in stationsList" v-bind:key="item.key" @input="renderTimeBelts($event, item)">
                            <template v-slot:actions>
                                <!-- <v-icon color="secondary" @click="renderTimeBelts(item)">$vuetify.icons.expand</v-icon> -->
                                <v-icon color="secondary">$vuetify.icons.expand</v-icon>
                            </template>
                            <template v-slot:header>
                                <!-- <v-layout @click="renderTimeBelts(item)"> -->
                                <v-layout>
                                    <v-flex md6>{{ getStationName(item) }}</v-flex>
                                    <v-flex md6><span class="pl-2">{{ format_audience(item.total_audience) }}</span></v-flex>
                                </v-layout>
                            </template>
                            <!-- <v-card v-if="stationsToggle[item.key]['show']" outlined> -->
                            <v-card outlined>
                                <v-card-text>
                                    <v-layout wrap>
                                        <v-flex md12 px-0 style="overflow-x:auto;  height: 40vh;">
                                            <media-plan-timebelt :station="item"></media-plan-timebelt>
                                        </v-flex>
                                    </v-layout>
                                </v-card-text>
                            </v-card>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-flex>
            </v-layout>
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
.sort-icon {
    font-size: 17px;
    width: 8px;
    margin: 2px;
}
</style>


<script>
    export default {
        data() {
            return {
                stationsList: [],
                stationsToggle: [],
                expand: false,
                headers: [
                    { text: 'Station', value: 'name', icon: 'arrow_downward', sort: 'desc' },
                    { text: 'Audience', value: 'total_audience', icon: 'arrow_downward', sort: 'desc' },
                ],
                pagination: {
                    rowsPerPage: -1
                },
            };
        },
        created() {
            var self = this;
            //Render the table whenever suggestions is updated
            Event.$on('ratings-created', function(data) {
                self.resetStationsList(data);
            });
        },
        mounted() {
            console.log('Stations Rating Table Component mounted.')
            this.resetStationsList(this.stationsList);
        },
        methods: {
            resetStationsList(stationsList) {
                var stationsToggle = [];
                stationsList.forEach((station) => {
                    stationsToggle[station.key] = {station: station, show: false}
                });
                this.stationsList = stationsList
                this.stationsToggle = stationsToggle;
            },
            getStationName(item) {
                if (item.state != '') {
                    return `${item.name} (${item.state})`;
                }
                return item.name;
            },
            renderTimeBelts($event, station) {
                if ($event == true) {
                    console.log($event);
                    var privateEvent = `${station.key}-timebelt-table-opened`;
                    Event.$emit(privateEvent, station);
                }
            },
            sortStationRatings(column_name) {
                this.headers.forEach((element) => {
                    if (element.value == column_name) {
                        if (element.sort == 'desc') {
                            this.stationsList.sort((a,b) => (a[column_name] > b[column_name]) ? 1 : -1); 
                            element.sort = 'asc';
                            element.icon = 'arrow_upward';
                        } else {
                            this.stationsList.sort((a,b) => (b[column_name] > a[column_name]) ? -1 : -1); 
                            element.sort = 'desc';
                            element.icon = 'arrow_downward';
                        }
                    }
                });
            }
        }
    }
</script>
