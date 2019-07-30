<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="600px">
        <template v-slot:activator="{ on }">
            <a v-on="on" class="default-vue-link">{{ timeBelt.program}}</a>
        </template>
        <v-card>
            <v-card-title>
                <span class="headline">Update program details for {{timeBelt.station}} {{timeBelt.start_time}} - {{timeBelt.end_time}}</span>
            </v-card-title>
            <v-card-text class="px-2 pt-2 pb-0">
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex xs12 sm12 md12>
                            <span>Program Name:</span>
                            <v-text-field type="text" placeholder="Program name" v-model="selectedValues.program"></v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex xs12 sm6 md6>
                            <span>15 Seconds:</span>
                            <v-text-field type="number" placeholder="Enter Unit Rate" v-model="selectedValues.ratingsPerDuration['15']"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6 md6>
                            <span>30 Seconds:</span>
                            <v-text-field type="number" placeholder="Enter Unit Rate" v-model="selectedValues.ratingsPerDuration['30']"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6 md6>
                            <span>45 Seconds:</span>
                            <v-text-field type="number" placeholder="Enter Unit Rate" v-model="selectedValues.ratingsPerDuration['45']"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6 md6>
                            <span>60 Seconds:</span>
                            <v-text-field type="number" placeholder="Enter Unit Rate" v-model="selectedValues.ratingsPerDuration['60']"></v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap v-for="(times, day) in selectedValues.timeBelts" :key="day">
                        <v-flex xs12 sm12 md3>
                            <span>{{day}}</span>
                        </v-flex>
                        <v-flex xs12 sm12 md9>
                            <v-layout wrap v-for="(time, key) in times" :key="key">
                                <v-flex xs12 sm3 md5>
                                    <vue-timepicker class="mb-2" :minute-interval="5" v-model="selectedValues.timeBelts[day][key]['start_time']"></vue-timepicker>
                                </v-flex>
                                <v-flex xs12 sm3 md5>
                                    <vue-timepicker class="mb-2" :minute-interval="5" v-model="selectedValues.timeBelts[day][key]['end_time']"></vue-timepicker>
                                </v-flex>
                                <v-flex xs12 sm1 md1 px-1>
                                    <v-icon class="pt-1" v-if="key == 0" color="success" dark @click="addTimeBelt(day)">add_box</v-icon>
                                    <v-icon class="pt-1" v-else color="red" dark @click="deleteTimeBelt(day, key)">delete</v-icon>
                                </v-flex>
                            </v-layout>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="red" dark @click="dialog = false">Close</v-btn>
                <v-btn color="" class="default-vue-btn" dark @click="updateProgramDetails">Update Program</v-btn>
            </v-card-actions>
        </v-card>
        </v-dialog>
    </v-layout>
</template>

<style>
    .v-text-field {
        padding-top: 2px;
        margin-top: 0px;
    }
</style>

<script>
    export default {
        props: {
            timeBelt: Object
        },
        data() {
            return {
                dialog: false,
                selectedValues: {
                    program: this.timeBelt.program,
                    station: this.timeBelt.station,
                    timeBelts: {
                        Monday: [],
                        Tuesday: [],
                        Wednesday: [],
                        Thursday: [],
                        Friday: [],
                        Saturday: [],
                        Sunday: []
                    },
                    ratingsPerDuration: {
                        15: '',
                        30: '',
                        45: '',
                        60: ''
                    }
                }
            };
        },
        created() {
            this.setUnitRates();
        },
        computed: {
            currentProgramStartTime() {
                var time = this.timeBelt.start_time;
                time = time.split(":");
                return { HH: time[0], mm: time[1] };
            },
            currentProgramEndTime() {
                var time = this.timeBelt.end_time;
                time = time.split(":");
                return { HH: time[0], mm: time[1] };
            }
        },
        mounted() {
            console.log('Program Details Component mounted.');
            // console.log(this.timeBelt);
            this.setTimeBelts();
        },
        methods: {
            setUnitRates() {
                if (this.timeBelt.duration_lists != '[null]' && this.timeBelt.rate_lists != '[null]') {
                    var current_timebelt_durations = JSON.parse(this.timeBelt.duration_lists);
                    var current_timebelt_rate_lists = JSON.parse(this.timeBelt.rate_lists);
                    current_timebelt_durations.forEach((element, key) => {
                        this.selectedValues.ratingsPerDuration[element] = current_timebelt_rate_lists[key];
                    });
                } 
            },
            updateProgramDetails() {
                Event.$emit('update-program-details', this.reformatNewProgram(this.selectedValues));
                this.dialog = false;
            },
            reformatNewProgram(details) {
                var start_time_arr = [];
                var end_time_arr = [];
                Object.values(details.timeBelts).forEach(timebelt => {
                    var start_time_obj = timebelt[0].start_time;
                    var end_time_obj = timebelt[0].end_time;
                    start_time_arr.push(`${start_time_obj.HH}:${start_time_obj.mm}`);
                    end_time_arr.push(`${end_time_obj.HH}:${end_time_obj.mm}`);
                })
                return {
                    program_name: details.program,
                    station: details.station,
                    duration: Object.keys(details.ratingsPerDuration),
                    unit_rate: Object.values(details.ratingsPerDuration),
                    days: Object.keys(details.timeBelts),
                    start_time: start_time_arr,
                    end_time: end_time_arr
                }
            },
            setTimeBelts() {
                Object.keys(this.selectedValues.timeBelts).forEach(element => {
                    if (this.timeBelt.day == element) {
                        var start_time = this.currentProgramStartTime;
                        var end_time = this.currentProgramEndTime;
                        this.selectedValues.timeBelts[element][0] = {start_time: {HH: start_time['HH'], mm: start_time['mm']}, end_time: {HH: end_time['HH'], mm: end_time['mm']}};
                    } else {
                        this.selectedValues.timeBelts[element][0] = {start_time: {HH:'00', mm:'00'}, end_time:{HH:'00', mm:'00'}};
                    }
                });
            },
            addTimeBelt(day) {
                var timeBeltsPerDay = this.selectedValues.timeBelts[day];
                timeBeltsPerDay.push({start_time: {HH:'00', mm:'00'}, end_time:{HH:'00', mm:'00'}});
            },
            deleteTimeBelt(day, time_belt_pos) {
                var timeBeltsPerDay = this.selectedValues.timeBelts[day];
                timeBeltsPerDay.splice(time_belt_pos, 1);
            }
        },
    }
</script>