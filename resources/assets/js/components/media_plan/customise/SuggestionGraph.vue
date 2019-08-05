<template>
    <v-layout row wrap class="white-bg">
        <v-flex xs12 sm12 md12 lg12 class="day-toggle">
            <v-btn-toggle>
                <div v-for="(day, key) in computedGraphDays" v-bind:key="key">
                    <v-btn text :class="`${activateFirstButton} px-4 mr-3`" @click="seriesByDay(day, key)" v-if="key == 0">
                        {{ shortDay(day) }}
                    </v-btn>
                    <v-btn text class="px-4 mr-3" @click="seriesByDay(day)" v-else>
                        {{ shortDay(day) }}
                    </v-btn>
                </div>
            </v-btn-toggle>
        </v-flex>
        <v-flex xs12 sm12 md12 lg12 px-0>
            <highcharts :options="chartOptions" :highcharts="hcInstance"></highcharts>
        </v-flex>
    </v-layout>
   
</template>

<style>
    .day-toggle .v-btn-toggle--selected {
        box-shadow: none !important;
    }
    .day-toggle .v-btn {
        border: 1px solid #3dadb5 !important;
        color: #000 !important;
    }
    .day-toggle .v-btn.v-btn--active.theme--light {
        background: #44c1c9;
        color: #ffffff !important;
    }
</style>


<script>
    import Highcharts from 'highcharts';
    const generalTimeBeltValue = ["00:00 - 00:15", "00:15 - 00:30", "00:30 - 00:45", "00:45 - 01:00", "01:00 - 01:15", "01:15 - 01:30", "01:30 - 01:45", "01:45 - 02:00", "02:00 - 02:15", "02:15 - 02:30", "02:30 - 02:45", "02:45 - 03:00", "03:00 - 03:15", "03:15 - 03:30", "03:30 - 03:45", "03:45 - 04:00", "04:00 - 04:15", "04:15 - 04:30", "04:30 - 04:45", "04:45 - 05:00", "05:00 - 05:15", "05:15 - 05:30", "05:30 - 05:45", "05:45 - 06:00", "06:00 - 06:15", "06:15 - 06:30", "06:30 - 06:45", "06:45 - 07:00", "07:00 - 07:15", "07:15 - 07:30", "07:30 - 07:45", "07:45 - 08:00", "08:00 - 08:15", "08:15 - 08:30", "08:30 - 08:45", "08:45 - 09:00", "09:00 - 09:15", "09:15 - 09:30", "09:30 - 09:45", "09:45 - 10:00", "10:00 - 10:15", "10:15 - 10:30", "10:30 - 10:45", "10:45 - 11:00", "11:00 - 11:15", "11:15 - 11:30", "11:30 - 11:45", "11:45 - 12:00", "12:00 - 12:15", "12:15 - 12:30", "12:30 - 12:45", "12:45 - 13:00", "13:00 - 13:15", "13:15 - 13:30", "13:30 - 13:45", "13:45 - 14:00", "14:00 - 14:15", "14:15 - 14:30", "14:30 - 14:45", "14:45 - 15:00", "15:00 - 15:15", "15:15 - 15:30", "15:30 - 15:45", "15:45 - 16:00", "16:00 - 16:15", "16:15 - 16:30", "16:30 - 16:45", "16:45 - 17:00", "17:00 - 17:15", "17:15 - 17:30", "17:30 - 17:45", "17:45 - 18:00", "18:00 - 18:15", "18:15 - 18:30", "18:30 - 18:45", "18:45 - 19:00", "19:00 - 19:15", "19:15 - 19:30", "19:30 - 19:45", "19:45 - 20:00", "20:00 - 20:15", "20:15 - 20:30", "20:30 - 20:45", "20:45 - 21:00", "21:00 - 21:15", "21:15 - 21:30", "21:30 - 21:45", "21:45 - 22:00", "22:00 - 22:15", "22:15 - 22:30", "22:30 - 22:45", "22:45 - 23:00", "23:00 - 23:15", "23:15 - 23:30", "23:30 - 23:45", "23:45 - 00:00"];
    let generalTimeBeltObj = []
    Highcharts.setOptions({
        lang: {
        decimalPoint: '.',
        thousandsSep: ','
        }
    });

    export default {
        props: {
            graphDays: Array,
            graphDetails: Object
        },
        data() {
            return {
                activateFirstButton: 'v-btn--active',
                hcInstance: Highcharts,
                chartOptions: {
                    chart: {
                        type: 'line',
                    },
                    title: {
                        text: ''
                    },
                    series: [],
                    subtitle: {
                        text: ' Time belt'
                    },
                    xAxis: {
                        title: {
                            text: 'Time Belt'
                        },
                        categories: generalTimeBeltValue,
                        min: 0,
                        max: 96
                    },
                    yAxis: {
                        title: {
                            text: 'Audience'
                        },
                        stackLabels: {
                            enabled: true,
                            format: '{total:,.f}'
                        },
                        labels: {
                            format: "{value:,.f}",
                        }
                    }
                }
            }
        },
        computed: {
            computedGraphDays: function () {
                let graphDetailsFoundDays = Object.keys(this.graphDetails);
                return this.graphDays.filter(f => graphDetailsFoundDays.includes(f));
            }
        },
        mounted() {
            console.log('Suggestions Graph Component mounted.')
            generalTimeBeltObj = this.defaultTimeBeltsObj();
            this.seriesByDay(this.computedGraphDays[0], 0);
        },
        methods: {
            defaultTimeBeltsObj() {
                let newArray = [];
                generalTimeBeltValue.forEach(function (item) {
                    newArray[item] = 0;
                });
                return newArray;
            },
            seriesByDay(day, key) {
                if (key != 0) {
                    this.activateFirstButton = '';
                }
                let stationTimebelts = this.graphDetails[day];
                let seriesValues = [];
                Object.keys(stationTimebelts).forEach(function (station) {
                    let suggestionTimebeltArr = [];
                    stationTimebelts[station].forEach(function (item, key) {
                        let startTime = item['start_time'].split(":");
                        startTime = `${startTime[0]}:${startTime[1]}`;
                        let endTime = item['end_time'].split(":");
                        endTime = `${endTime[0]}:${endTime[1]}`;
                        suggestionTimebeltArr[`${startTime} - ${endTime}`] = item['total_audience'];
                    });
                    var mergedArr = Object.assign(generalTimeBeltObj, suggestionTimebeltArr);
                    var audiencesPerTimeBelt = Object.values(mergedArr);
                    seriesValues.push({
                        name: `${station}/${day}`,
                        data: audiencesPerTimeBelt,
                        showInLegend:true,
                    });
                });
                this.chartOptions.series = seriesValues;
            }
        }
    }
</script>
