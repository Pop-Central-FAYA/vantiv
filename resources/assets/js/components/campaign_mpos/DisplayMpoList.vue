<template>
    <v-card>
        <v-card-title>
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
        </v-card-title>
        <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="mpos" :search="search" :pagination.sync="pagination">
        <template v-slot:items="props">
            <tr>
                <td @click="adslotList(props.item.id)">
                    <a @click="adslotList(props.item.id)" class="default-vue-link">{{ props.item.station }}</a>
                </td>
                <td @click="adslotList(props.item.id)" class="text-xs-left">{{ format_audience(sumNetTotalInCampaignMpoTimeBelt(props.item.campaign_mpo_time_belts)) }}</td> 
                <td @click="adslotList(props.item.id)" class="text-xs-left">{{ sumAdslotsInCampaignMpoTimeBelt(props.item.campaign_mpo_time_belts) }}</td>
                <td @click="adslotList(props.item.id)" class="text-xs-left">{{ props.item.status }}</td>
                <td class="justify-center layout px-0">
                    <v-container grid-list-md class="container-action-btn">
                        <v-layout wrap>
                            <v-flex xs12 sm6 md4>
                                <v-layout>
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                            <v-icon color="primary" v-on="on" dark left @click="exportMpo(props.item.id)">fa-file-excel</v-icon>
                                        </template>
                                        <span>Export MPO</span>
                                    </v-tooltip>
                                </v-layout>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                 <file-modal :mpo="props.item" :assets="assets"></file-modal>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <mpo-file-manager :mpo="props.item" :assets="assets" :client="client" :brand="brand"></mpo-file-manager>
                            </v-flex>
                        </v-layout>
                    </v-container>
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
</template>

<style>
 .container-action-btn {
     padding: 0px 24px;
 }
</style>


<script>
  export default {
        props : {
            mpos : {
                required : true,
                type : Array
            },
            assets: Array,
            client: String,
            brand: String
        },
        data () {
            return {
                search: '',
                headers: [
                    { text: 'Station', align: 'left', value: 'station',  width: '30%' },
                    { text: 'Budget', value: 'budget',  width: '20%' },
                    { text: 'Exposures', value: 'ad_slots', width: '15%' },
                    { text: 'Status', value: 'status', width: '20%' },
                    { text: 'Actions', value: 'name', sortable: false , width: '15%'}
                ],
                pagination: {
                    rowsPerPage: 10
                },
            }
        },
        methods : {
            adslotList : function(mpo_id) {
                return window.location.href = '/campaigns/mpo/details/'+mpo_id
            },
            exportMpo : function(mpo_id) {
                var msg = "Generating Excel Document, Please wait";
                this.sweet_alert(msg, 'info');
                window.location = '/campaigns/mpo/export/'+mpo_id
                // window.location.href = '/campaigns/mpo/export/'+mpo_id
            },
            sumAdslotsInCampaignMpoTimeBelt : function(campaign_mpo_time_belts) {
                return campaign_mpo_time_belts.reduce((prev, cur) => prev + cur.ad_slots, 0);
            },
            sumNetTotalInCampaignMpoTimeBelt : function(campaign_mpo_time_belts) {
                return campaign_mpo_time_belts.reduce((prev, cur) => prev + cur.net_total, 0);
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

