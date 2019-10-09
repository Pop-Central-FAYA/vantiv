<template>
    <v-container grid-list-md class="pt-0 pb-3 px-0">
        <v-layout row wrap class="px-4 pb-3 white-bg">
            <v-flex xs12 style="min-height: 70px">
                <comment :model-id="plan.id" :routes="plan.routes.comments"></comment>
            </v-flex>
            <v-flex xs12>
                <media-plan-deliverables :media-plan="mediaPlan"></media-plan-deliverables>
            </v-flex>
        </v-layout>
        <v-layout row wrap class="white-bg">
            <v-flex xs12 sm12 md12 lg12 mb-2>
                <v-expansion-panel popout>
                    <v-expansion-panel-content v-for="(duration, durationkey) in fayaDurations" :key="durationkey" class="py-2">
                        <template v-slot:header>
                            <div>{{ duration }} Seconds</div>
                        </template>
                        <v-card>
                            <v-card-text>
                                <v-layout wrap style="border-bottom: 1px solid #e8e8e8;">
                                    <v-flex md12 px-0 style="overflow-x:auto;">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="fixed-side">Station</th>
                                                    <th class="fixed-side">Day</th>
                                                    <th class="fixed-side">Time Belt</th>
                                                    <th class="fixed-side">Program</th>
                                                    <th class="fixed-side">Rating</th>
                                                    <th class="fixed-side">Grp</th>
                                                    <th class="fixed-side">Unit Rate</th>
                                                    <th class="fixed-side">Discount</th>
                                                    <th class="fixed-side">Net Total</th>
                                                    <th class="fixed-side">Total Exposure</th>
                                                    <th v-for="(date, labelDateKey) in timeBelts['labeldates']" :key="labelDateKey" class="fixed-side">{{date}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(timebelt, timeBeltkey) in fayaTimebelts['programs_stations']" :key="timeBeltkey">
                                                    <td>{{ timebelt.station}}</td>
                                                    <td>{{ shortDay(timebelt.day) }}</td>
                                                    <td>{{ format_time(timebelt.start_time) }} {{ format_time(timebelt.end_time)}}</td>
                                                    <td>
                                                        <media-plan-program-details :time-belt="timebelt"></media-plan-program-details>
                                                    </td>
                                                    <td>
                                                        {{ timebelt.rating }}
                                                    </td>
                                                    <td>
                                                        {{ calculateGrp(timebelt.rating, getTotalExposureByTimebelt(timeBeltkey, duration)) }}
                                                    </td>
                                                    <td>{{ formatNumber(getUnitRate(timebelt, duration)) }}</td>
                                                    <td>
                                                        <input v-if="timebelt.volume_discount" min="0" max="100" :value="timebelt.volume_discount" type="number" @blur="storeVolumeDiscount(timebelt.station,$event)">
                                                        <input v-else min="0" max="100" :value="0" type="number" @blur="storeVolumeDiscount(timebelt.station,$event)">
                                                    </td>
                                                    <td>{{ formatAmount(getNetTotalByTimeBelt(timebelt, timeBeltkey, duration)) }}</td>
                                                    <td>{{ getTotalExposureByTimebelt(timeBeltkey, duration) }}</td>
                                                    <td v-for="(timebeltDate, timebeltDatekey) in fayaTimebelts['dates']" :key="timebeltDatekey" class="exposure-td">
                                                        <input type="number" v-if="fayaTimebelts['days'][timebeltDatekey] == timebelt.day" v-model="fayaTimebelts['programs_stations'][timeBeltkey]['exposures'][duration]['dates'][timebeltDate]" @change="countExposureByTimeBelt(timeBeltkey, duration, timebeltDate)">
                                                        <input type="number" class="disabled_input" disabled v-else>
                                                    </td>
                                                </tr>
                                                <tr class="subtotal">
                                                    <td>Subtotal</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>{{ formatAmount(getNetTotalByDuration(duration)) }}</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </v-flex>
                                    <!-- <v-flex md3 style="overflow-x:auto;">
                                        <table class="dates">
                                            <thead>
                                                <tr>
                                                    <th v-for="(date, labelDateKey) in timeBelts['labeldates']" :key="labelDateKey" class="fixed-side">{{date}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(timebelt, programKey) in fayaTimebelts['programs_stations']" :key="programKey">
                                                    <td v-for="(timebeltDate, timebeltDatekey) in fayaTimebelts['dates']" :key="timebeltDatekey">
                                                        <input type="number" v-if="fayaTimebelts['days'][timebeltDatekey] == timebelt.day" v-model="fayaTimebelts['programs_stations'][programKey]['exposures'][duration]['dates'][timebeltDate]" @change="countExposureByTimeBelt(programKey, duration)">
                                                        <input type="number" class="disabled_input" disabled v-else>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </v-flex> -->
                                </v-layout>
                            </v-card-text>
                        </v-card>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-flex>
        </v-layout>
        <v-layout row wrap class="px-0 py-5">
            <v-flex xs12 sm12 md4 class="px-0">
                <v-btn @click="buttonRedirect(plan.routes.insertions.back_action)" color="vue-back-btn" large><v-icon left>navigate_before</v-icon>Back</v-btn>
            </v-flex>
            <v-flex xs12 s12 md8 class="px-0 text-right">
                <v-btn :disabled="isRunRatings || plan.status =='Approved' ||  plan.status =='Declined'" @click="save(false)" color="default-vue-btn" large><v-icon left>save</v-icon>Save</v-btn>
                <v-btn @click="goToSummary()" color="default-vue-btn" large>Summary<v-icon right>navigate_next</v-icon></v-btn>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<style>
    .v-content {
        background: #fafafa;
    }
    .white-bg {
        background: #ffffff;
    }
    .subtotal td {
        border-bottom: none !important;
    }
    .dates td, .exposure-td {
        padding: 13px 5px;
        font-size: 12px;
    }
    .dates td input, .exposure-td input {
        padding: 8px 2px 5px;
        text-align: center;
    }
    .v-text-field .v-input__slot {
        padding: 0px 12px;
        min-height: 45px;
        margin-bottom: 0px;
        border: 1px solid #ccc;
        border-radius: 5px;
        /* box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12); */
    }
    .v-text-field>.v-input__control>.v-input__slot:after, .v-text-field>.v-input__control>.v-input__slot:before {
        content: none;
    }
    .theme--dark.v-btn.v-btn--disabled:not(.v-btn--icon):not(.v-btn--flat):not(.v-btn--outline) {
        background-color: hsl(184, 55%, 53%)!important;
    }
</style>

<script>
    export default {
        props: {
            durations: Array,
            timeBelts: Object,
            plan: Object,
        },
        data() {
            return {
                fayaDurations: this.durations,
                fayaTimebelts: this.timeBelts,
                volumeDiscounts: [],
                newPrograms: [],
                isRunRatings: false,
                isSaved: false,
                isNewInsertion: false,
                mediaPlan: JSON.parse(JSON.stringify(this.plan))
            };
        },
        created() {
            var self = this;
            Event.$on('update-program-details', function (details) {
                self.newPrograms.push(details);
                self.updateProgramsStations(details);
            });
        },
        mounted() {
            console.log('Media Plan Details Component mounted.');
        },
        methods: {
            getUnitRate(time_belt, duration) {
                if (time_belt.duration_lists != '[null]' && time_belt.rate_lists != '[null]') {
                    if (typeof(time_belt.duration_lists) === 'string') {
                        var current_timebelt_durations = JSON.parse(time_belt.duration_lists);
                        var current_timebelt_rate_lists = JSON.parse(time_belt.rate_lists);
                    } else {
                        var current_timebelt_durations = time_belt.duration_lists;
                        var current_timebelt_rate_lists = time_belt.rate_lists;
                    }
                    var unit_rate = 0;
                    current_timebelt_durations.forEach((element, key) => {
                        if (element == duration) {
                            unit_rate = current_timebelt_rate_lists[key];
                        }
                    });
                    return unit_rate;
                } else {
                    return 0;
                }
            },
            countExposureByTimeBelt(time_belt_pos, duration, exposure_date) {
                this.isNewInsertion = true;
                var exposure_date_value = this.fayaTimebelts['programs_stations'][time_belt_pos]['exposures'][duration]['dates'][exposure_date];
                if (exposure_date_value == "" || exposure_date_value < 0) {
                    this.fayaTimebelts['programs_stations'][time_belt_pos]['exposures'][duration]['dates'][exposure_date] = 0;
                }
                var exposures = this.fayaTimebelts['programs_stations'][time_belt_pos]['exposures'][duration]['dates'];
                this.fayaTimebelts['programs_stations'][time_belt_pos]['exposures'][duration]['total_exposures'] = Object.values(exposures).reduce((a,b) => parseInt(a) + parseInt(b), 0);
                this.$forceUpdate();
            },
            getTotalExposureByTimebelt(time_belt_pos, duration) {
                return this.fayaTimebelts['programs_stations'][time_belt_pos]['exposures'][duration]['total_exposures'];
            },
            getNetTotalByTimeBelt(time_belt, time_belt_pos, duration) {
                var exposures = this.fayaTimebelts['programs_stations'][time_belt_pos]['exposures'][duration]['total_exposures'];
                if (this.fayaTimebelts['programs_stations'][time_belt_pos]['volume_discount'] == null) {
                    var discount = 0;
                } else {
                    var discount = this.fayaTimebelts['programs_stations'][time_belt_pos]['volume_discount'];
                }
                var unit_rate = this.getUnitRate(time_belt, duration);
                var gross_total = unit_rate * exposures;
                var deducted_value = (discount/100) * gross_total;
                var net_total = gross_total - deducted_value;
                this.fayaTimebelts['programs_stations'][time_belt_pos]['exposures'][duration]['net_total'] = net_total;
                return net_total;
            },
            getNetTotalByDuration(search_duration) {
                var net_total = 0;
                this.fayaTimebelts['programs_stations'].forEach(program_station => {
                    Object.keys(program_station.exposures).forEach(duration => {
                        if (search_duration == duration) {
                            net_total += program_station.exposures[duration]['net_total'];
                        }
                    });
                });
                return net_total;
            },
            storeVolumeDiscount(station, evt) {
                var discount = evt.target.value;
                if (discount >= 0 && discount <= 100) {
                    this.volumeDiscounts.push({discount: discount, station: station});
                    this.fayaTimebelts.programs_stations.forEach(element => {
                        if (element.station == station) {
                            element.volume_discount = discount;
                        }
                    });
                }
            },
            updateProgramsStations(details) {
                var filterByStation = this.fayaTimebelts.programs_stations.filter(program => program.station == details.station);
                filterByStation.forEach((station, station_key) => {
                    details.days.forEach((day, key) => {
                        if (station.day == day && this.format_time(station.start_time) >= this.format_time(details.start_time[key]) && this.format_time(station.end_time) <= this.format_time(details.end_time[key])) {
                            filterByStation[station_key]['program'] = details.program_name;
                            filterByStation[station_key]['duration_lists'] = details.duration;
                            filterByStation[station_key]['rate_lists'] = details.unit_rate;
                        }
                    });
                });
            },
            buttonRedirect(url) {
                window.location = url;
            },
            getNetTotalAllDurations() {
                var net_total_all_durations = 0;
                this.fayaDurations.forEach(duration => {
                    net_total_all_durations += this.getNetTotalByDuration(duration);
                });
                return net_total_all_durations;
            },
            goToSummary() {
                var net_total_all_durations = this.getNetTotalAllDurations();
                if (this.plan.status == "Approved" || this.plan.status == "Declined") {
                    window.location = this.plan.routes.insertions.next_action;
                } else if (net_total_all_durations <= 0 && this.isNewInsertion == false) {
                    this.sweet_alert("Please add at least one insertion", 'error');
                    return;
                } else if (net_total_all_durations <= 0 && this.isNewInsertion == true) {
                    this.sweet_alert("One or more insertions does not have unit rate", 'error');
                    return;
                } else if (net_total_all_durations > 0 && this.isNewInsertion == true && this.isSaved == false) {
                    this.sweet_alert("Please save insertion !!", 'info');
                    return;
                } else {
                    // Validate inputs using vee-validate plugin 
                    this.$validator.validate().then(valid => {
                        if (valid) {
                            window.location = this.plan.routes.insertions.next_action;
                        }
                    });
                }
            },
            save(is_redirect) {
                var net_total_all_durations = this.getNetTotalAllDurations();
                if (net_total_all_durations <= 0 && this.isNewInsertion == false) {
                    this.sweet_alert("Please add at least one insertion", 'error');
                    return;
                } else if (net_total_all_durations <= 0 && this.isNewInsertion == true) {
                    this.sweet_alert("One or more insertions does not have unit rate", 'error');
                    return;
                }
                // Validate inputs using vee-validate plugin 
                this.$validator.validate().then(valid => {
                    if (!valid) {
                        console.log('invalid form');
                    }
                    if (valid) {
                        this.isRunRatings = true;
                        var msg = "Saving media plan, please wait";
                        this.sweet_alert(msg, 'info', 60000);
                        axios({
                            method: 'post',
                            url: this.plan.routes.insertions.save_action,
                            data: {
                                new_programs: this.newPrograms,
                                new_volume_discounts: this.volumeDiscounts,
                                programs_stations: this.fayaTimebelts.programs_stations,
                                plan_id: this.plan.id
                            }
                        }).then((res) => {
                            this.isRunRatings = false;
                            this.isSaved = true;
                            if (res.data.status === "success") {
                                this.sweet_alert(res.data.message, 'success');
                                //update the plan model with the new data
                                this.mediaPlan = res.data.media_plan
                            } else {
                                this.sweet_alert(res.data.message, 'error');
                            }
                        }).catch((error) => {
                            this.isRunRatings = false;
                            this.sweet_alert('An unknown error has occurred, please try again', 'error');
                        });
                    }
                });
            },
            calculateGrp(reach, exposures) {
                var grp = parseFloat(reach) * parseInt(exposures);
                return grp.toFixed(2);
            },
            getTotalGrpByDuration(search_duration) {
                var grp_total = parseFloat(0);
                this.fayaTimebelts['programs_stations'].forEach(program_station => {
                    Object.keys(program_station.exposures).forEach(duration => {
                        if (search_duration == duration) {
                            var grp = this.calculateGrp(program_station.rating, program_station.exposures[search_duration]['total_exposures']);
                            grp_total += parseFloat(grp);
                        }
                    });
                });
                return grp_total.toFixed(2);
            }
        }
    }
</script>