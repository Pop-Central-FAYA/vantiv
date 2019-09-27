<template>
    <v-app>
        <v-card class="p-2" style="height: 35rem;overflow-y: scroll;">
            <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="adslotData" hide-actions :pagination.sync="pagination">
                <template v-slot:items="props">
                    <tr>
                        <td>
                            {{ props.item.publisher.name }}
                        </td>
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
        }
    },
    data () {
        return {
            search: '',
            editDialog: false,
            headers: [
                { text : 'Publisher', value: 'publisher.name' },
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
    }
}
</script>

