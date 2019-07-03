<template>
    <v-card>
        <v-card-title>
        MPOS
        <v-spacer></v-spacer>
        <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
        </v-card-title>
        <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="mpos" :search="search">
        <template v-slot:items="props">
            <tr @click="adslotList(props.item.id)">
                <td>{{ props.item.station }}</td>
                <td class="text-xs-left">{{ props.item.budget }}</td>
                <td class="text-xs-left">{{ props.item.ad_slots }}</td>
                <td class="text-xs-left">{{ props.item.status }}</td>
                <td class="justify-center layout px-0">
                    <v-btn color="primary" small @click="exportMpo(props.item.id)" dark>
                        Export
                    </v-btn>
                    <file-modal :mpo="props.item" :assets="assets"></file-modal>
                    <mpo-file-manager :mpo="props.item" :assets="assets" :client="client" :brand="brand"></mpo-file-manager>
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
                    { text: 'Station', align: 'left', value: 'station' },
                    { text: 'Budget', value: 'budget' },
                    { text: 'Adslots', value: 'ad_slots' },
                    { text: 'Status', value: 'status' },
                    { text: 'Actions', value: 'name', sortable: false }
                ]
            }
        },
        methods : {
            adslotList : function(mpo_id) {
                return window.location.href = '/campaigns/mpo/details/'+mpo_id
            },
            exportMpo : function(mpo_id) {
                var msg = "Generating Excel Document, Please wait";
                this.sweet_alert(msg, 'info');
                return window.location.href = '/campaigns/mpo/export/'+mpo_id
            }
        }
    }
</script>
