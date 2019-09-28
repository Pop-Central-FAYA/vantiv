<template>
    <v-card>
        <v-card-title>
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
        </v-card-title>
        <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="mposData" :search="search" :pagination.sync="pagination" item-key="station" expand :show-expand="true">
        <template v-slot:items="props">
            <tr>
                <td @click="getMpo(props.item.links.details)">
                    <a class="default-vue-link">{{ props.item.vendor }}</a>
                </td>
                <td @click="getMpo(props.item.links.details)" class="text-xs-left">{{ format_audience(props.item.net_total) }}</td> 
                <td @click="getMpo(props.item.links.details)" 
                    class="text-xs-left"
                    >{{ props.item.insertions }}</td>
                <td @click="getMpo(props.item.links.details)" 
                    class="text-xs-left transform"
                    :style="{ 'color' : statusColor(props.item.status)}"
                    >{{ props.item.status }}</td>    
                <td class="justify-center layout px-0">
                    <v-container grid-list-md class="container-action-btn">
                        <v-layout wrap>
                            <v-flex xs12 sm6 md3>
                                <v-layout>
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                            <v-icon color="#01c4ca" v-on="on" dark left @click="getMpo(props.item.links.details)">fa-file-excel</v-icon>
                                        </template>
                                        <span>Preview Mpo</span>
                                    </v-tooltip>
                                </v-layout>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <share-link-modal :mpo="props.item"></share-link-modal>
                            </v-flex>
                            <v-flex xs12 sm6 md2>
                                 <submit-mpo-modal :mpo="props.item"></submit-mpo-modal>
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
            client: String,
            brand: String,
            campaign : Object
        },
        data () {
            return {
                search: '',
                headers: [
                    { text: 'Vendor', align: 'left', value: 'vendor',  width: '30%' },
                    { text: 'Net Total (₦)', value: 'net_total',  width: '20%' },
                    { text: 'Exposures', value: 'insertions', width: '15%' },
                    { text: 'Status', value: 'status', width : '15%'},
                    { text: 'Actions', value: 'name', sortable: false , width: '15%'}
                ],
                pagination: {
                    rowsPerPage: 10
                },
                mposData : []
            }
        },
        mounted () {
            this.fetchMpo()
            var self = this
            Event.$on('update-campaign-mpo', function(mpo) {
                self.mposData = mpo
            })
        },
        methods : {
            statusColor : function(status) {
                var status = status.toLowerCase()
                switch (status) {
                    case 'accepted':
                        return 'green'
                        break;
                    case 'submitted':
                        return 'orange'
                        break;
                    default :
                        return 'grey'
                }
            },
            getMpo : function(details_link) {
                var msg = "Generating document for preview, Please wait";
                this.sweet_alert(msg, 'info');
                window.location = details_link
            },
            fetchMpo : function() {
                axios({
                    method: 'get',
                    url: this.campaign.links.mpos
                }).then((res) => {
                    this.mposData = res.data.data
                }).catch((error) => {
                    console.log(error)
                });
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
    .transform {
        text-transform: capitalize
    }
</style>

