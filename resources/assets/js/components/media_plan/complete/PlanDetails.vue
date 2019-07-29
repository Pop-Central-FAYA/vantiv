<template>
    <v-container grid-list-md class="pt-0 pb-3 px-0">
        <v-layout row wrap class="white-bg">
            <v-flex xs12 sm12 md12 lg12 mb-3>
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
                                                    <td>{{ formatAmount(getUnitRate(timebelt, duration)) }}</td>
                                                    <td>
                                                        <input v-if="timebelt.volume_discount" min="0" max="100" :value="timebelt.volume_discount" type="number" @blur="storeVolumeDiscount(timebelt.station,$event)">
                                                        <input v-else min="0" max="100" :value="0" type="number" @blur="storeVolumeDiscount(timebelt.station,$event)">
                                                    </td>
                                                    <td>{{ formatAmount(getNetTotalByTimeBelt(timebelt, timeBeltkey, duration)) }}</td>
                                                    <td>{{ getTotalExposureByTimebelt(timeBeltkey, duration) }}</td>
                                                    <td v-for="(timebeltDate, timebeltDatekey) in fayaTimebelts['dates']" :key="timebeltDatekey" class="exposure-td">
                                                        <input type="number" v-if="fayaTimebelts['days'][timebeltDatekey] == timebelt.day" v-model="fayaTimebelts['programs_stations'][timeBeltkey]['exposures'][duration]['dates'][timebeltDate]" @change="countExposureByTimeBelt(timeBeltkey, duration)">
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
        <v-layout row wrap class="px-4 pb-3 white-bg">
            <v-flex xs12 sm4 md4>
                <span>Client:</span>
                <v-select
                    v-model="client"
                    :items="clients"
                    item-text="company_name"
                    item-value="id"
                    v-validate="'required'"
                    name="client"
                    @change="getBrands"
                    placeholder="Please select client"
                ></v-select>
                <span class="text-danger" v-show="errors.has('client')">{{ errors.first('client') }}</span>
            </v-flex>
            <v-flex xs12 sm4 md4>
                <span>Brand:</span>
                <v-select
                    v-model="brand"
                    :items="filteredBrands"
                    item-text="name"
                    item-value="id"
                    v-validate="'required'"
                    name="brand"
                    placeholder="Please select brand"
                ></v-select>
                <span class="text-danger" v-show="errors.has('brand')">{{ errors.first('brand') }}</span>
            </v-flex>
            <v-flex xs12 sm4 md4>
                <span>Product Name:</span>
                <v-text-field name="product" placeholder="Product name" v-model="product" v-validate="'required'"></v-text-field>
                <span class="text-danger" v-show="errors.has('product')">{{ errors.first('product') }}</span>
            </v-flex>
        </v-layout>
        <v-layout row wrap class="px-0 py-5">
            <v-flex xs12 sm12 md4 class="px-0">
                <v-btn @click="buttonRedirect(redirectUrls.back_action)" color="vue-back-btn" large><v-icon left>navigate_before</v-icon>Back</v-btn>
            </v-flex>
            <v-flex xs12 s12 md8 class="px-0 text-right">
                <v-btn :disabled="isRunRatings || plan.status =='Approved' ||  plan.status =='Declined'" @click="save(false)" color="default-vue-btn" large><v-icon left>save</v-icon>Save</v-btn>
                <v-btn @click="buttonRedirect(redirectUrls.next_action)" color="default-vue-btn" v-if="plan.status == 'Approved' || plan.status == 'Declined'" large>Next<v-icon right>navigate_next</v-icon></v-btn>
                <v-btn :disabled="isRunRatings" @click="save(true)" color="default-vue-btn" v-else large>Summary<v-icon right>navigate_next</v-icon></v-btn>
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
            clients: Array,
            brands: Object,
            redirectUrls: Object
        },
        data() {
            return {
                client: this.plan.client_id,
                brand: this.plan.brand_id,
                product: this.plan.product_name,
                filteredBrands: [],
                fayaDurations: this.durations,
                fayaTimebelts: this.timeBelts,
                volumeDiscounts: [],
                newPrograms: [],
                isRunRatings: false
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
            if (this.client) {
                this.getBrands();
            }
        },
        methods: {
            getBrands() {
                this.filteredBrands = this.brands[this.client];
            },
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
            countExposureByTimeBelt(time_belt_pos, duration) {
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
                if (discount > 0 && discount <= 100) {
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
                        if (station.day == day && this.format_time(station.start_time) == this.format_time(details.start_time[key]) && this.format_time(station.end_time) == this.format_time(details.end_time[key])) {
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
            save(is_redirect) {
                var net_total_all_durations = 0;
                this.fayaDurations.forEach(duration => {
                    net_total_all_durations += this.getNetTotalByDuration(duration);
                });

                if (net_total_all_durations <= 0) {
                    this.sweet_alert("Please select some exposures", 'info');
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
                            url: this.redirectUrls.save_action,
                            data: {
                                new_programs: this.newPrograms,
                                new_volume_discounts: this.volumeDiscounts,
                                programs_stations: this.fayaTimebelts.programs_stations,
                                client_id: this.client,
                                brand_id: this.brand,
                                product_name: this.product,
                                plan_id: this.plan.id
                            }
                        }).then((res) => {
                            this.isRunRatings = false;
                            console.log(res.data);
                            if (res.data.status === "success") {
                                this.sweet_alert(res.data.message, 'success');
                                if (is_redirect) {
                                    window.location = this.redirectUrls.next_action;
                                }
                            } else {
                                this.sweet_alert(res.data.message, 'error');
                            }
                        }).catch((error) => {
                            this.isRunRatings = false;
                            this.sweet_alert('An unknown error has occurred, please try again', 'error');
                        });
                    }
                });
            }
        }
    }
</script>