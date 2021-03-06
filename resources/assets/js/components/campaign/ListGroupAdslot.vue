<template>
    <v-card>
        <v-card-title>
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <v-form> 
            <v-layout wrap>
                <v-flex xs12 sm12 md12>
                    <v-select
                        v-model="group_adslot"
                        :items="group_parameter"
                        item-text="name"
                        item-value="id"
                        name="group"
                        @change="processGroup()"
                    ></v-select>
                </v-flex>
            </v-layout>
        </v-form>
        </v-card-title>
        <v-data-table class="custom-vue-table elevation-1" :headers="getHeader()" :items="campaignGroupTimeBeltsData" 
        :search="search" 
        :pagination.sync="pagination" 
        :item-key="group_adslot" expand :show-expand="true">
            <template v-slot:items="props">
                <tr>
                    <td @click="props.expanded = !props.expanded" v-if="group_adslot === 'publisher_id'">
                        <a class="default-vue-link">{{ props.item.time_belts[0].publisher.name }}</a>
                    </td>
                    <td @click="props.expanded = !props.expanded" 
                        v-else-if="group_adslot === 'ad_vendor_id' && props.item.time_belts[0].vendor">
                        <a class="default-vue-link" >{{ props.item.time_belts[0].vendor.name }}</a>
                    </td>
                    <td @click="props.expanded = !props.expanded" v-else>
                        <a class="default-vue-link" >No vendor</a>
                    </td>
                    <td @click="props.expanded = !props.expanded" class="text-xs-left">{{ format_audience(props.item.net_total) }}</td> 
                    <td @click="props.expanded = !props.expanded" 
                        class="text-xs-left"
                        >{{ props.item.insertions }}</td>
                    <td class="justify-center layout px-0">
                        <v-container grid-list-md class="container-action-btn">
                            <v-layout wrap>
                                <v-flex xs12 sm6 md3>
                                    <edit-volume-campaign-price 
                                    :campaign="campaign"
                                    :selected-adslots="props.item.time_belts"
                                    :ad-vendors="props.item.time_belts[0].publisher.ad_vendors"
                                    :volume-discount="props.item.time_belts[0].volume_discount"
                                    :group="group_adslot"
                                    :index="props.index"
                                    ></edit-volume-campaign-price>
                                </v-flex>
                                <v-flex xs12 sm6 md3>
                                    <mpo-file-manager 
                                        :client="client" 
                                        :brand="brand"
                                        :campaign="campaign"
                                        :assets="assets"
                                        :selected-adslots="props.item.time_belts"
                                        :group="group_adslot"
                                    ></mpo-file-manager>
                                </v-flex>
                                <v-flex xs12 sm6 md3>
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                            <div v-on="on" class="d-inline-block position-icon">
                                                <v-icon color="#01c4ca" dark left v-on="on" :disabled="!isCampaignOpen(campaign.status)" @click="generateMpo(props.item)">save</v-icon>
                                            </div>
                                        </template>
                                        <span v-if="isCampaignOpen(campaign.status)">Generate MPO</span>
                                        <span v-else>Action is disabled while campaign is {{ campaign.status.toLowerCase() }}</span>
                                    </v-tooltip>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </td>
                </tr>
            </template>
            <template slot="expand" slot-scope="props">
                <list-campaign-adslot 
                    :assets="assets" 
                    :campaign-time-belts="props.item.time_belts"
                    :time-belt-range="timeBeltRange"
                    :ad-vendors="getVendor(props.item.time_belts)"
                    :campaign="campaign"
                    :client="client"
                    :brand="brand"
                    :group="group_adslot"
                    :index="props.index"
                ></list-campaign-adslot>
            </template>
            <template v-slot:no-results>
                <v-alert :value="true" color="error" icon="warning">
                Your search for "{{ search }}" found no results.
                </v-alert>
            </template>
        </v-data-table>
    </v-card>
</template>

<style>
    .container-action-btn {
        padding: 0px 24px;
    }
</style>

<script>
  export default {
        props : {
            assets: Array,
            groupedCampaignTimeBelts : Array,
            timeBeltRange : Array,
            adVendors: Array,
            campaign: Object,
            client: String,
            brand: String,
        },
        data () {
            return {
                group_adslot : 'publisher_id',
                search: '',
                pagination: {
                    rowsPerPage: 10
                },
                campaignGroupTimeBeltsData : this.groupedCampaignTimeBelts,
                group_parameter : [{
                                    'id' : 'publisher_id',
                                    'name' : 'Publisher'
                                },{
                                    'id' : 'ad_vendor_id',
                                    'name' : 'Vendor'
                                }],
            }
        },
        created() {
            console.log(this.campaign)
            this.group_adslot
            var self = this;
            Event.$on('updated-group-adslots', function (adslot) {
                self.campaignGroupTimeBeltsData = adslot;
            });
            Event.$on('group', function(group) {
                self.group_adslot = group
            })
        },
        methods : {
            getHeader : function () {
                var header = []
                if(this.group_adslot === 'publisher_id'){
                    header.push({text: 'Publisher', align: 'left', value: 'publisher.name',  width: '40%'})
                }else if(this.group_adslot === 'ad_vendor_id'){
                    header.push({ text: 'Vendor', align: 'left', value: 'vendor.name',  width: '40%' })
                }
                header.push({ text: 'Budget (₦)', value: 'budget',  width: '20%' },
                    { text: 'Exposures', value: 'ad_slots', width: '20%' },
                    { text: 'Actions', value: 'name', sortable: false , width: '20%'})
                return header
            },
            processGroup : function() {
                this.campaignGroupTimeBeltsData = []
                this.sweet_alert('Filtering...', 'info');
                axios({
                    method: 'get',
                    url: `/campaigns/${this.campaign.id}/groups/${this.group_adslot}`
                }).then((res) => {
                    this.campaignGroupTimeBeltsData = res.data.grouped_time_belts;
                    this.sweet_alert('Time belts filtered successfully', 'success');
                }).catch((error) => {
                    console.log(error)
                    this.sweet_alert('An unknown error has occurred, vendors cannot be retrieved. Please try again', 'error');
                });  
            },
            generateMpo : function (group_time_belt) {
                this.sweet_alert('Processing...', 'info');
                var found = group_time_belt.time_belts.find(function(time_belt) {
                    return time_belt.asset_id === '' || time_belt.asset_id === null
                })
                if(found) {
                    this.sweet_alert('Please attach assets to all your slots', 'error')
                    return
                }
                axios({
                    method: 'POST',
                    url: this.campaign.links.store_campaign_mpo,
                    data : {
                        ad_vendor_id : group_time_belt.time_belts[0].ad_vendor_id,
                        publisher_id : group_time_belt.time_belts[0].publisher_id,
                        insertions : group_time_belt.insertions,
                        net_total : group_time_belt.net_total,
                        adslots : group_time_belt.time_belts,
                        group : this.group_adslot,
                    }
                }).then((res) => {
                    if(res.data === 'exists'){
                        this.sweet_alert('You have just generated an mpo with the exact record', 'error');
                        return
                    }
                    Event.$emit('update-campaign-mpo', res.data.data);
                    Event.$emit('update-share-link', true)
                    this.sweet_alert('Mpo Generated successfully', 'success');
                }).catch((error) => {
                    if (error.response && (error.response.status == 422)) {
                        this.displayServerValidationErrors(error.response.data.errors, true);
                    } else {
                        this.sweet_alert('An unknown error has occurred, vendor cannot be created. Please try again', 'error');
                    }
                });
            },
            getVendor : function (time_belts) {
                if(this.group_adslot === 'ad_vendor_id' && time_belts.ad_vendor_id != null) {
                    return [time_belts[0].vendor]
                }
                return this.adVendors
            }
        }
    }
</script>
<style>
    tbody tr:hover {
        background-color: transparent !important;
        cursor: pointer;
    }
    tbody:hover {
    background-color: rgba(0, 0, 0, 0.12);
    }
    .theme--dark.v-icon.v-icon--disabled {
        color: grey !important;
    }
    .position-icon {
        padding-top: 12px;
    }
</style>

