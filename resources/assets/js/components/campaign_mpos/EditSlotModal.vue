<template>
    <v-dialog v-model="dialog" persistent max-width="500px">
        <template v-slot:activator="{ on }">
            <v-tooltip top>
                <template v-slot:activator="{ on }">
                    <div v-on="on" class="d-inline-block position-icon">
                        <v-icon color="#01c4ca" dark v-on="on" @click="dialog = true" 
                        :disabled="selectedAdslots.length > 0 || !isCampaignOpen(campaign.status)"
                        right>edit</v-icon>
                    </div>
                </template>
                <span v-if="isCampaignOpen(campaign.status)">Edit adslot</span>
                <span v-else>You cant perform this action while campaign is {{ campaign.status.toLowerCase() }}</span>
            </v-tooltip>
        </template>
        <v-card>
            <v-card-title>
                <span class="headline"> {{ adslot.program }} 
                                    for {{ adslot.duration }} 
                                    Seconds duration on 
                                        {{ adslot.playout_date }}
                </span>
            </v-card-title>
            <v-card-text>
                <v-container grid-list-md>
                    <v-form>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Publisher
                                </span>
                                <v-select
                                    v-model="adslot.publisher_id"
                                    :items="publisherData"
                                    item-text="name"
                                    item-value="id"
                                    v-validate="'required'"
                                    name="publisher"
                                    placeholder="Select Publisher"
                                    solo
                                ></v-select>
                                <span class="text-danger" v-show="errors.has('publisher')">{{ errors.first('publisher') }}</span>
                            </v-flex>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Ad Vendor
                                </span>
                                <v-tooltip slot="append" bottom v-if="adVendors.length === 0">
                                    <v-icon slot="activator" color="primary" dark>info</v-icon>
                                    <span>You need to assign a vendors to this publisher</span>
                                </v-tooltip>
                                <v-select
                                    v-model="adslot.ad_vendor_id"
                                    :items="adVendors"
                                    item-text="name"
                                    item-value="id"
                                    v-validate="'required'"
                                    name="ad_vendor"
                                    placeholder="Select Ad Vendor"
                                    solo
                                ></v-select>
                                <span class="text-danger" v-show="errors.has('ad_vendor')">{{ errors.first('ad_vendor') }}</span>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md12>
                                <span>
                                    Program
                                </span>
                                <v-text-field v-validate="'required'" 
                                type="text" placeholder="Program name" 
                                name="program" v-model="adslot.program"></v-text-field>
                                <span class="text-danger" v-show="errors.has('progeam')">{{ errors.first('progeam') }}</span>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md12>
                                <span>
                                    Date
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
                                        v-model="adslot.playout_date"
                                        name="date"
                                        v-validate="'required|date_format:yyyy-MM-dd'"
                                        placeholder="DD/MM/YYYY"
                                        v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker v-model="adslot.playout_date" no-title @input="dateMenu = false"></v-date-picker>
                                </v-menu>
                                <!-- <input type="date" required v-validate="'required'" name="date" v-model="adslot.playout_date" class="form-control"> -->
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
                                name="unit_price" v-model="adslot.unit_rate"></v-text-field>
                                <span class="text-danger" v-show="errors.has('unit_price')">{{ errors.first('unit_price') }}</span>
                            </v-flex>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Volume Discount (%)
                                </span>
                                <v-text-field v-validate="'required|min:1'" 
                                type="number" placeholder="Unit Price"
                                name="volume_discount" v-model="adslot.volume_discount"></v-text-field>
                                <span class="text-danger" v-show="errors.has('volume_discount')">{{ errors.first('volume_discount') }}</span>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md12>
                                <span>
                                    Media Asset
                                </span>
                                <v-select
                                    v-model="adslot.asset_id"
                                    :items="filterAssetByDuration(assets, adslot.duration)"
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
                                    v-model="adslot.time_belt_start_time"
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
                                name="insertion" v-model="adslot.ad_slots"></v-text-field>
                                <span class="text-danger" v-show="errors.has('insertion')">{{ errors.first('insertion') }}</span>
                            </v-flex>
                        </v-layout>
                    </v-form>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="red" class="default-vue-btn" dark @click="dialog = false">Close</v-btn>
                <v-btn class="default-vue-btn" dark @click="updateSlot()">Update</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    props : {
        adslot : {
            require : true,
            type : Object
        },
        assets : {
            required : true,
            type : Array
        },
        timeBeltRange : {
            required : true,
            type : Array
        },
        publisher : Object,
        group : String,
        index : Number,
        selectedAdslots : Array,
        campaign : Object,
        adVendorList: Array
    },
    data () {
        return {
            dialog : false,
            media_asset_id : null,
            dateMenu: false,
            publisherData : [this.publisher],
            adVendors : []
        }
    },
    mounted() {
        console.log(this.group);
        const dictionay = {
        custom: {
                media_asset: {
                    required: 'please choose a video file'
                },
                ad_vendor: {
                    required: 'Ad vendor is required'
                }
            }
        };
        this.$validator.localize('en', dictionay);
        this.getVendorByPublisher()
    },
    computed : {
        newTotalInsertions : function (){
            let new_insertion = this.inputed_insertions.reduce((prev, cur) => prev + parseInt(cur.insertion), 0);
            return !isNaN(new_insertion) ? new_insertion : this.adslot.ad_slots
        }
    },
    methods : {
        updateSlot : async function(event) {
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
                method: 'patch',
                url: `/campaigns/${this.adslot.campaign_id}/adslots/${this.adslot.id}`,
                data: {
                    id : [this.adslot.id],
                    program : this.adslot.program,
                    playout_date : this.adslot.playout_date,
                    asset_id : this.adslot.asset_id,
                    unit_rate : this.adslot.unit_rate,
                    time_belt_start_time : this.adslot.time_belt_start_time,
                    ad_slots: this.adslot.ad_slots,
                    volume_discount : this.adslot.volume_discount,
                    day : this.dayName(this.adslot.playout_date),
                    ad_vendor_id : this.adslot.ad_vendor_id,
                    publisher_id : this.adslot.publisher_id,
                    group : this.group
                }
            }).then((res) => {
                if (res.data.status === 'success') {
                    Event.$emit('updated', true)
                    Event.$emit('updated-campaign', res.data.data)
                    if(this.group){
                        Event.$emit('updated-adslots-from-group', res.data.data.grouped_time_belts[this.index].time_belts)
                    }else{
                        Event.$emit('updated-group-adslots', res.data.data.grouped_time_belts)
                    }
                    Event.$emit('updated-adslots', res.data.data.time_belts)
                    this.sweet_alert(res.data.message, 'success');
                    this.dialog = false;
                } else {
                    this.sweet_alert(res.data.message, 'error');
                    this.isHidden = true
                }
            }).catch((error) => {
                if (error.response && (error.response.status == 422)) {
                    this.displayServerValidationErrors(error.response.data.errors);
                } else {
                    this.sweet_alert('An unknown error has occurred. Please try again', 'error');
                }
                this.isHidden = true
            });
        },
        filterAssetByDuration : function(assets, duration) {
            return assets.filter(item => item.duration === duration);
        },
        getVendorByPublisher : function() {
            var self = this
            this.adVendors = this.adVendorList.filter((element) => 
                                element.publishers.some((publisher) => publisher.id === self.adslot.publisher_id)
                            );
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
    .theme--dark.v-icon.v-icon--disabled {
        color: grey !important;
    }
    .position-icon {
        padding-top: 12px;
    }
</style>


