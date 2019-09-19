<template>
    <v-dialog v-model="dialog" persistent max-width="500px">
        <template v-slot:activator="{ on }">
            <v-icon color="#01c4ca" dark v-on="on" @click="dialog = true" left>add</v-icon>
        </template>
        <v-card>
            <v-card-text>
                <v-container grid-list-md>
                    <v-form>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Ad Vendor
                                </span>
                                <v-select
                                    v-model="ad_vendor"
                                    :items="adVendors"
                                    item-text="name"
                                    item-value="id"
                                    v-validate="'required'"
                                    name="ad_vendor"
                                    placeholder="Select Ad Vendor"
                                    solo
                                    return-object
                                ></v-select>
                                <span class="text-danger" v-show="errors.has('ad_vendor')">{{ errors.first('ad_vendor') }}</span>
                            </v-flex>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Publisher
                                </span>
                                <v-select
                                    v-model="publisher"
                                    :items="ad_vendor.publishers"
                                    item-text="name"
                                    item-value="id"
                                    v-validate="'required'"
                                    name="publisher"
                                    placeholder="Select Publisher"
                                    solo
                                ></v-select>
                                <span class="text-danger" v-show="errors.has('publisher')">{{ errors.first('publisher') }}</span>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md12>
                                <span>
                                    Program
                                </span>
                                <v-text-field v-validate="'required'" 
                                type="text" placeholder="Program name" 
                                name="program" v-model="program"></v-text-field>
                                <span class="text-danger" v-show="errors.has('progeam')">{{ errors.first('progeam') }}</span>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md12>
                                <span>
                                    Playout Date
                                </span>
                                <v-menu
                                v-model="dateMenu"
                                    :close-on-content-click="false"
                                    :nudge-right="40"
                                    lazy
                                    transition="scale-transition"
                                    offset-y
                                    full-width
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-text-field
                                        v-model="playout_date"
                                        name="date"
                                        v-validate="'required|date_format:yyyy-MM-dd'"
                                        placeholder="DD/MM/YYYY"
                                        v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker v-model="playout_date" no-title @input="dateMenu = false"></v-date-picker>
                                </v-menu>
                                <span class="text-danger" v-show="errors.has('date')">{{ errors.first('date') }}</span>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Unit Price
                                </span>
                                <v-text-field v-validate="'required|min:1'" 
                                type="number" placeholder="Unit Price" 
                                name="unit_price" v-model="unit_rate"></v-text-field>
                                <span class="text-danger" v-show="errors.has('unit_price')">{{ errors.first('unit_price') }}</span>
                            </v-flex>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Volume Discount (%)
                                </span>
                                <v-text-field v-validate="'required|min:1'" 
                                type="number" placeholder="Unit Price"
                                name="volume_discount" v-model="volume_discount"></v-text-field>
                                <span class="text-danger" v-show="errors.has('volume_discount')">{{ errors.first('volume_discount') }}</span>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Duration
                                </span>
                                <v-select
                                    v-model="duration"
                                    :items="durations"
                                    v-validate="'required'"
                                    name="duration"
                                    placeholder="Select duration"
                                    solo
                                ></v-select>
                                <span class="text-danger" v-show="errors.has('duration')">{{ errors.first('duration') }}</span>
                            </v-flex>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Media Asset
                                </span>
                                <v-select
                                    v-model="asset_id"
                                    :items="filterAssetByDuration(assets, duration)"
                                    item-text="file_name"
                                    item-value="id"
                                    v-validate="'required'"
                                    name="media_asset"
                                    placeholder="Select Media Asset"
                                    solo
                                ></v-select>
                                <span class="text-danger" v-show="errors.has('media_asset')">{{ errors.first('media_asset') }}</span>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Time Belt
                                </span>
                                <p></p>
                                <v-select
                                    v-model="time_belt"
                                    :items="timeBeltRange"
                                    item-text="start_time"
                                    item-value="start_time"
                                    v-validate="'required'"
                                    name="time_belt"
                                    solo
                                ></v-select>
                                <span class="text-danger" v-show="errors.has('time_belt')">{{ errors.first('time_belt') }}</span>
                            </v-flex>

                            <v-flex xs12 sm12 md6>
                                <span>
                                    Insertion
                                </span>
                                <v-text-field v-validate="'required|min:1'" 
                                type="number" placeholder="Unit Price"
                                name="insertion" v-model="insertion"></v-text-field>
                                <span class="text-danger" v-show="errors.has('insertion')">{{ errors.first('insertion') }}</span>
                            </v-flex>
                        </v-layout>
                    </v-form>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="red" class="default-vue-btn" dark @click="dialog = false">Close</v-btn>
                <v-btn class="default-vue-btn" dark @click="addAdslot()">Add Adslot</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    props : {
        assets : {
            required : true,
            type : Array
        },
        timeBeltRange : {
            required : true,
            type : Array
        },
        adVendors : Array,
        campaignId : String,
        group : String
    },
    data () {
        return {
            dialog : false,
            media_asset_id : null,
            dateMenu: false,
            publisher : '',
            ad_vendor : {},
            program : '',
            playout_date : '',
            unit_rate : 0,
            volume_discount : 0,
            asset_id : '',
            time_belt : '',
            insertion : 0,
            duration : '',
            mpo_id : this.mpo_id,
            durations : [15, 30, 45, 60]
        }
    },
    mounted() {
        const dictionay = {
        custom: {
                media_asset: {
                    required: 'please choose a video file'
                },
                duration: {
                    required: 'please choose a media duration'
                }
            }
        };
        this.$validator.localize('en', dictionay);
    },
    methods : {
        addAdslot : async function(event) {
            let isValid = await this.$validator.validate().then(valid => {
                if (!valid) {
                    return false;
                } else {
                    return true;
                }
            });

            if (!isValid) {
                return false;
            }
            var msg = "Processing request, please wait...";
            this.sweet_alert(msg, 'info');
            axios({
                method: 'POST',
                url: `/campaigns/${this.campaignId}/adslots`,
                data: {
                    publisher : this.publisher,
                    ad_vendor : this.ad_vendor.id,
                    program : this.program,
                    playout_date : this.playout_date,
                    unit_rate : this.unit_rate,
                    volume_discount : this.volume_discount,
                    asset_id : this.asset_id,
                    time_belt : this.time_belt,
                    insertion : this.insertion,
                    duration : this.duration,
                    mpo_id : this.mpo_id,
                    group : this.group
                }
            }).then((res) => {
                if (res.data.status === 'success') {
                    this.sweet_alert(res.data.message, 'success');
                    Event.$emit('updated-group-adslots',res.data.data.grouped_time_belts)
                    Event.$emit('updated', true)
                    Event.$emit('updated-campaign', res.data.data)
                    Event.$emit('updated-adslots', res.data.data.time_belts)
                    Event.$emit('group', 'publisher_id')
                    this.dialog = false;
                } else {
                    this.sweet_alert(res.data.message, 'error');
                    this.isHidden = true
                }
            }).catch((error) => {
                this.sweet_alert(error.response.data.message, 'error');
                this.isHidden = true
            });
        },
        filterAssetByDuration : function(assets, duration) {
            return assets.filter(item => item.duration === duration);
        }
    }
}
</script>
<style>
    .v-date-picker-table {
            height: 380px !important;
        }
    .menuable__content__active {
        min-width : 0px !important;
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
    .v-date-picker-table {
        height: 100% !important;
    }
    .theme--dark.v-btn.v-btn--disabled:not(.v-btn--icon):not(.v-btn--flat):not(.v-btn--outline) {
        background-color: hsl(184, 55%, 53%)!important;
    }
</style>


