<template>
    <v-container grid-list-md class="pt-0 pb-3 px-0">
        <v-data-table :hide-actions="true" :headers="headers" :items="mediaPlanList">
            <template v-slot:items="props">
                <tr>
                    <td>{{ formatAmount(props.item.total_insertions) }}</td>
                    <td>{{ formatAmount(props.item.gross_impressions) }}</td>
                    <td>{{ formatAmount(props.item.total_grp) }}</td>
                    <td>{{ formatAmount(props.item.net_reach) }}</td>
                    <td>{{ formatAmount(props.item.avg_frequency) }}</td>
                    <td>{{ numberFormat(props.item.net_media_cost) }}</td>
                    <td>{{ numberFormat(props.item.cpm) }}</td>
                    <td>{{ numberFormat(props.item.cpp) }}</td>
                </tr>
            </template>
        </v-data-table>
    </v-container>
</template>

<script>
    export default {
        props: {
            mediaPlan: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                headers: [
                    { text: 'Total Insertions', align: 'left', value: 'total_insertions', sortable: false },
                    { text: 'Gross Impressions', align: 'left', value: 'gross_impressions', sortable: false },
                    { text: 'Total GRP', value: 'total_grp', sortable: false },
                    { text: 'Net Reach', value: 'net_reach', sortable: false },
                    { text: 'Avg. Frequency', value: 'avg_frequency', sortable: false },
                    { text: 'Net Media Cost (₦)', value: 'net_media_cost', sortable: false },
                    { text: 'CPM (₦)', value: 'cpm', sortable: false },
                    { text: 'CPP (₦)', value: 'cpp', sortable: false },
                ],
            }
        },
        computed: {
            mediaPlanList: function() {
                return [this.mediaPlan]
            }
        },
        mounted() {
            console.log('Media Plan Deliverables Component mounted.');
        }
    }
</script>