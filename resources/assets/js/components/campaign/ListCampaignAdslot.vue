<template>
    <v-app>
        <v-card>
            <v-card-title>
                <v-layout>
                    <v-flex xs1>
                    <add-adslot-modal v-if="!group"
                        :assets="assets"
                        :time-belt-range="timeBeltRange"
                        :campaign="campaign"
                        :ad-vendors="adVendors"
                    ></add-adslot-modal>
                    </v-flex>
                    <v-flex xs1>
                    <edit-volume-campaign-price v-if="selectedAdslots.length > 0"
                        :campaign="campaign"
                        :selected-adslots="selectedAdslots"
                        :ad-vendors="adVendors"
                        :group="group"
                    >
                    </edit-volume-campaign-price>
                    </v-flex>
                    <v-flex xs1>
                    <mpo-file-manager v-if="selectedAdslots.length > 0"
                        :client="client" 
                        :brand="brand"
                        :campaign="campaign"
                        :assets="assets"
                        :selected-adslots="selectedAdslots"
                        :group="group"
                    ></mpo-file-manager>
                    </v-flex>
                    <v-flex xs3>
                        <v-select
                            v-model="selected"
                            :items="filterParameters"
                            item-text="name"
                            item-value="id"
                            placeholder="Select a filter criteria"
                            @change="processFilter()"
                        ></v-select>
                    </v-flex>
                    <v-flex xs2>
                    </v-flex>
                    <v-flex xs4>
                        <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
                    </v-flex>
                </v-layout>
            </v-card-title>
            <v-data-table class="custom-vue-table elevation-1" 
                :headers="getHeader()" 
                :items="campaignTimeBeltsData" 
                :search="search" :pagination.sync="pagination" 
                item-key="station" expand :show-expand="true"
                v-model="selectedAdslots"
            >
            <template v-slot:headers="props">
                <tr>
                    <th>
                        <v-checkbox
                        primary
                        hide-details
                        @click.native="toggleAll"
                        :input-value="props.all"
                        :indeterminate="props.indeterminate"
                        ></v-checkbox>
                    </th>
                    <th v-for="header in props.headers" :key="header.text"
                        :class="['column sortable', pagination.descending ? 'desc' : 'asc', header.value === pagination.sortBy ? 'active' : '']"
                        @click="changeSort(header.value)"
                    >
                        <v-icon>arrow_upward</v-icon>
                        {{ header.text }}
                    </th>
                </tr>
            </template>
            <template v-slot:items="props">
                <tr :active="props.selectedAdslots" @click="props.selectedAdslots = !props.selectedAdslots">
                    <td>
                        <v-checkbox
                        primary
                        hide-details
                        :input-value="props.selected"
                        ></v-checkbox>
                    </td>
                    <td  class="text-xs-left">{{ props.item.publisher.name }}
                    </td>
                    <td  class="text-xs-left" v-if="props.item.vendor">{{ props.item.vendor.name }}
                    </td>
                    <td  class="text-xs-left" v-else>
                        No Vendor
                    </td>
                    <td >{{ props.item.day }} ({{ props.item.playout_date }})
                    </td>
                    <td  class="text-xs-left">{{ props.item.time_belt_start_time }}
                    </td>
                    <td  class="text-xs-left">{{ props.item.program }}
                    </td>
                    <td  class="text-xs-left">{{ props.item.duration }}</td>
                    <td  class="text-xs-left">{{ props.item.ad_slots }} 
                    </td>
                    <td  class="text-xs-left">{{ format_audience(props.item.net_total) }}
                    </td>
                    <td class="justify-center layout px-0">
                        <edit-slots-modal
                            :adslot="props.item"
                            :assets="assets"
                            :time-belt-range="timeBeltRange"
                            :ad-vendor="props.item.vendor"
                            :group="group"
                            :publisher="props.item.publisher"
                            :index="index"
                            :selected-adslots="selectedAdslots"
                            :campaign="campaign"
                        ></edit-slots-modal>

                        <delete-slots-modal
                        :adslot="props.item"
                        :group="group"
                        :index="index"
                        :selected-adslots="selectedAdslots"
                        :campaign="campaign"
                        ></delete-slots-modal>
                    </td>
                </tr>
            </template>
            <template v-slot:no-results>
                <v-alert :value="true" color="error" icon="warning">
                Your search for "{{ search }}" found no results.
                </v-alert>
            </template>
            </v-data-table>
        </v-card>
    </v-app>
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
            campaignTimeBelts : Array,
            timeBeltRange : Array,
            adVendors: Array,
            campaign: Object,
            client: String,
            brand: String,
            group : String,
            index : Number
        },
        data () {
            return {
                search: '',
                pagination: {
                    rowsPerPage: 10
                },
                campaignTimeBeltsData : this.campaignTimeBelts,
                currentItem: {},
                isHidden : true,
                selectedAdslots : [],
                adVendorData : [],
                click : 0,
                filterParameters : [
                    {
                        'name' : 'With Asset',
                        'id' : 'with_asset'
                    },
                    {
                        'name' : 'Without Asset',
                        'id' : 'without_asset'
                    }
                ],
                selected : ''
            }
        },
        mounted () {
            var self = this
            if(self.group){
                Event.$on('updated-adslots-from-group', function(adslot) {
                    self.campaignTimeBeltsData = adslot
                })
            }else{
                Event.$on('updated-adslots', function(adslot) {
                    self.campaignTimeBeltsData = adslot
                })
            }
            
        },
        methods : {
            getHeader : function() {
                var header = [{ text: 'Publisher', value: 'publisher.name'},
                    { text: 'Vendor', value: 'vendor.name'},
                    { text: 'Day', align: 'left', value: 'day' },
                    { text: 'Time', value: 'time_belt_start_time', width: "1%" },
                    { text: 'Program', value: 'program' },
                    { text: 'Duration', value: 'duration', width: "1%" },
                    { text: 'Insertions', value: 'ad_slots', width: "1%"},
                    { text : 'Net Total (₦)', value : 'net_total' },
                    { text: 'Actions', value: 'name', sortable: false }]
                return header;
            },
            toggleAll : function() {
                if (this.selectedAdslots.length){
                    this.selectedAdslots = []
                }else {
                    this.selectedAdslots = this.campaignTimeBeltsData.slice()
                }
            },
            changeSort (column) {
                if (this.pagination.sortBy === column) {
                    this.pagination.descending = !this.pagination.descending
                } else {
                    this.pagination.sortBy = column
                    this.pagination.descending = false
                }
            },
            processFilter : function () {
                this.sweet_alert('Processing...', 'info')
                var self = this
                var found = this.campaignTimeBelts.filter(function(time_belt) {
                    if(self.selected === 'with_asset'){
                        return time_belt.asset_id != null
                    }else{
                        return time_belt.asset_id === null
                    }
                });
                this.sweet_alert('Filterec successfully', 'success', 2000)
                this.campaignTimeBeltsData = found
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
</style>

