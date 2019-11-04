<template>
    <v-card>
        <v-card-title>
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <v-form style="padding-right : 10px; padding-top : 11px;">
            <v-layout wrap>
                <v-flex xs12 sm12 md12>
                    <v-select
                        v-model="filter_param"
                        :items="select_items"
                        item-text="name"
                        item-value="id"
                        name="group"
                        @change="filterMpo()"
                    ></v-select>
                </v-flex>
            </v-layout>
        </v-form>
        <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
        </v-card-title>
        <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="mposData" :search="search" :loading="loading" :pagination.sync="pagination" item-key="station" expand :show-expand="true">
        <template v-slot:items="props">
            <tr>
                <td @click="getMpo(props.item.links.details)">
                    <a class="default-vue-link">{{ props.item.vendor }}
                        <span v-if="props.item.is_recent">(Active)</span>
                    </a>
                </td>
                <td @click="getMpo(props.item.links.details)">
                    {{ props.item.version }}
                </td>
                <td @click="getMpo(props.item.links.details)" :class="{ 'active-color' : props.item.is_recent }">
                    {{ props.item.reference }}
                </td>
                <td @click="getMpo(props.item.links.details)" class="text-xs-left" >
                    {{ format_audience(props.item.net_total) }}
                </td> 
                <td @click="getMpo(props.item.links.details)" 
                    class="text-xs-left"
                    >{{ props.item.insertions }}
                </td>
                <td @click="getMpo(props.item.links.details)"
                    class="text-xs-left"
                    >{{ dateToHumanReadable(props.item.created_date) }}
                </td>
                <td @click="getMpo(props.item.links.details)"
                    class="text-xs-left transform"
                    :style="{ 'color' : statusColor(props.item.status)}"
                    >{{ props.item.status }}</td>    
                <td class="justify-center layout px-0">
                    <v-container grid-list-md class="container-action-btn">
                        <v-layout wrap>
                            <v-flex xs12 sm6 md3>
                                <approve-mpo :mpo="props.item" :campaign="campaign"></approve-mpo>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <share-link-modal :mpo="props.item" :campaign="campaign"></share-link-modal>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <submit-mpo-modal :mpo="props.item" :campaign="campaign"></submit-mpo-modal>
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
                    { text: 'Vendor', align: 'left', value: 'vendor',  width: '15%' },
                    { text: 'Version', value : 'version', width: '5%'},
                    { text: 'Reference Number', value : 'reference', width: '15%'},
                    { text: 'Net Total (₦)', value: 'net_total',  width: '20%' },
                    { text: 'Exposures', value: 'insertions', width: '5%' },
                    { text: 'Created Date', value : 'created_at', width: '10'},
                    { text: 'Status', value: 'status', width : '10%'},
                    { text: 'Actions', value: 'name', sortable: false , width: '20%'}
                ],
                pagination: {
                    rowsPerPage: 10
                },
                mposData : [],
                filter_param : false,
                select_items : [
                    {'id' : false, 'name' : 'All Versions'},
                    {'id' : true, 'name' : 'Active Versions'}
                ],
                loading: false,
            }
        },
        mounted () {
            this.fetchMpo()
            var self = this
            Event.$on('update-campaign-mpo', function(mpo) {
                self.mposData = mpo
            })
            Event.$on('mpo-updated', function (mpo) {
                self.fetchMpo()
            });
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
                window.location = details_link
            },
            fetchMpo : function() {
                this.loading = true
                axios({
                    method: 'get',
                    url: this.campaign.links.mpos
                }).then((res) => {
                    this.loading = false
                    this.mposData = res.data.data
                }).catch((error) => {
                    console.log(error)
                });
            },
            filterMpo : function() {
                var self = this
                var filtered_mpo = this.mposData.filter(function(mpo) {
                    return mpo.is_recent === self.filter_param
                })
                this.filter_param ? 
                this.mposData = filtered_mpo : 
                this.fetchMpo()
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
    .container-action-btn {
        padding: 0px 24px;
    }
    .active-color {
        color: green;
    }
    .position-icon {
        padding-top: 12px;
    }
</style>

