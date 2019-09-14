<template>
    <v-app>
        <v-card class="p-2" style="height: 35rem;overflow-y: scroll;">
            <v-card-title class="px-0 py-0">
                <v-btn class="default-vue-btn mx-0" small dark @click="exportMpo()">
                    Export Mpo
                </v-btn>
            </v-card-title>
            <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="adslotData" hide-actions :pagination.sync="pagination">
                <template v-slot:items="props">
                    <tr>
                        <td>{{ props.item.day }} ({{ props.item.playout_date }})
                        </td>
                        <td class="text-xs-left">{{ props.item.time_belt_start_time }}
                        </td>
                        <td class="text-xs-left">{{ props.item.program }}
                        </td>
                        <td class="text-xs-left">{{ props.item.duration }} Seconds</td>
                        <td class="text-xs-left">{{ props.item.ad_slots }} 
                        </td>
                        <td class="text-xs-left">{{ format_audience(props.item.net_total) }}</td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
    </v-app>
</template>
<script>
export default {
    props : {
        adslots : {
            type : Array,
            required : true
        },
        mpo_id : {
            type :String,
            required : true 
        }
    },
    data () {
        return {
            search: '',
            editDialog: false,
            headers: [
                { text: 'Day', align: 'left', value: 'day' },
                { text: 'Program Time', value: 'program_time' },
                { text: 'Program', value: 'program' },
                { text: 'Duration', value: 'duration' },
                { text: 'Insertions', value: 'insertions' },
                { text: 'Net Total (₦)', value: 'net_total' },
            ],
            pagination: {
                rowsPerPage: -1
            },
            adslotData : [],
        }
    },
    mounted () {
        this.adslotData = this.adslots            
    },
    methods : {
        exportMpo : function() {
            this.sweet_alert('Exporting the mpo, please wait..', 'info');
            axios({
                method: 'get',
                url: `/public/mpos/${this.mpo_id}/temporary-url`
            }).then((res) => {
                let temporary_url = res.data;
                this.sweet_alert('Export completed', 'success');
                window.location = temporary_url
            }).catch((error) => {
                this.assetData = [];
                this.sweet_alert('An unknown error has occurred, mpo cannot be exported. Please try again', 'error');
            });
        }
    }
}
</script>

