<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="600px">
            <template v-slot:activator="{ on }">
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-icon color="#01c4ca" dark left v-on="on" @click="dialog = true">edit</v-icon>
                    </template>
                    <span>Edit campaign volume prices</span>
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
                                <v-flex xs12 sm12 md12 v-if="group === 'publisher_id' && adVendors.length > 0">
                                    <span>
                                        Ad Vendor
                                    </span>
                                    <v-select
                                        v-model="ad_vendor"
                                        :items="adVendors"
                                        item-text="name"
                                        item-value="id"
                                        name="ad_vendor"
                                        placeholder="Select Ad Vendor"
                                        solo
                                        v-validate="'required'"
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
        campaignId : String,
        selectedAdslots : Array,
        adVendors : Array,
        group : String,
        index : Number
    },
    data() {
        return {
            dialog: false,
            volume_discount : '',
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
                url: `/campaigns/${this.campaignId}`,
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
                    this.volume_discount = ''
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
</style>


