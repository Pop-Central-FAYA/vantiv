<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="600px">
            <template v-slot:activator="{ on }">
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <div v-on="on" class="d-inline-block position-icon">
                            <v-icon color="#01c4ca" dark left v-on="on" :disabled="!isCampaignOpen(campaign.status)" @click="dialog = true">edit</v-icon>
                        </div>
                    </template>
                    <span v-if="group === 'publisher_id' && isCampaignOpen(campaign.status)">Edit Adslots</span>
                    <span v-else>Action is disabled while campaign is {{ campaign.status.toLowerCase() }}</span>
                </v-tooltip>
            </template>
            <v-card>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-layout wrap>
                                <v-flex xs12 sm12 md12>
                                    <span>
                                        Volume Discount (%)
                                    </span>
                                    <v-text-field v-validate="'required'" 
                                    type="number" placeholder="Volume Discount" 
                                    name="volume_discount" v-model="volume_discount"></v-text-field>
                                    <span class="text-danger" v-show="errors.has('volume_discount')">{{ errors.first('volume_discount') }}</span>
                                </v-flex>
                                <v-flex xs12 sm12 md12 v-if="group === 'publisher_id'">
                                    <span>
                                        Ad Vendor
                                    </span>
                                    <v-tooltip slot="append" bottom v-if="adVendors.length === 0">
                                        <v-icon slot="activator" color="primary" dark>info</v-icon>
                                        <span>You need to assign a vendor to this publisher</span>
                                    </v-tooltip>
                                    <v-select
                                        v-model="ad_vendor"
                                        :items="adVendors"
                                        item-text="name"
                                        item-value="id"
                                        name="ad_vendor"
                                        placeholder="Select Ad Vendor"
                                        solo
                                        v-validate="'nullable'"
                                    ></v-select>
                                    <span class="text-danger" v-show="errors.has('ad_vendor')">{{ errors.first('ad_vendor') }}</span>
                                </v-flex>
                                
                            </v-layout>
                        </v-form>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" class="default-vue-btn" dark @click="dialog = false">Close</v-btn>
                    <v-btn color="green" class="default-vue-btn" dark @click="update()"> Update</v-btn>
                </v-card-actions>
            </v-card>
            
        </v-dialog>
    </v-layout>
</template>
<script>
export default {
    props : {
        campaign : Object,
        selectedAdslots : Array,
        adVendors : Array,
        group : String,
        index : Number,
        volumeDiscount : Number
    },
    data() {
        return {
            dialog: false,
            volume_discount : this.volumeDiscount,
            ad_vendor : this.getVendor(),
        }
    },
    methods : {
        update : async function() {
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
            this.sweet_alert('Processing request, please wait...', 'info');
            axios({
                method: 'patch',
                url: this.campaign.links.update_adslots,
                data: {
                    id : _.map(this.selectedAdslots, 'id'),
                    volume_discount : this.volume_discount,
                    ad_vendor_id : this.ad_vendor,
                    group : this.group
                }
            }).then((res) => {
                if (res.data.status === 'success') {
                    this.sweet_alert(res.data.message, 'success')
                    Event.$emit('updated', true)
                    Event.$emit('updated-campaign', res.data.data)
                    if(this.group){
                        Event.$emit('updated-adslots-from-group', res.data.data.grouped_time_belts[this.index].time_belts)
                    }
                    Event.$emit('updated-group-adslots', res.data.data.grouped_time_belts)
                    Event.$emit('updated-adslots', res.data.data.time_belts)
                    this.dialog = false
                } else {
                    this.sweet_alert(res.data.message, 'error')
                }
            }).catch((error) => {
                console.log(error)
                this.sweet_alert(error.response.data.message, 'error')
            });
        },
        getVendor : function () {
            if(this.group === 'publisher_id') {
                if(this.adVendors.length > 0){
                    return this.adVendors[0].id
                }
            }
        }
    }
}
</script>
<style>
    .modal {
    overflow-y: auto;
    }

    .modal-open {
    overflow: auto;
    }
    .theme--dark.v-icon.v-icon--disabled {
        color: grey !important;
    }
    .position-icon {
        padding-top: 12px;
    }
</style>


