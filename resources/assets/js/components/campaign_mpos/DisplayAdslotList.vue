<template>
    <v-app>
        <v-card>
            <v-card-title>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
            </v-card-title>
            <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="groupedAdslots" :search="search" :pagination.sync="pagination">
                <template v-slot:items="props">
                    <tr @click="openEditDialog(props.item)">
                        <td>{{ props.item.day }} ({{ props.item.playout_date }})
                        </td>
                        <td class="text-xs-left">{{ props.item.program }}
                        </td>
                        <td class="text-xs-left">{{ props.item.duration }} Seconds</td>
                        <td class="text-xs-left">{{ props.item.ad_slots }} 
                        </td>
                        <td class="text-xs-left">{{ props.item.time_belt_start_time }}
                        </td>
                        <td class="text-xs-left">{{ format_audience(props.item.net_total) }}</td>
                        <td class="justify-center layout px-0">
                            <delete-slots-modal
                            :adslot="props.item"
                            ></delete-slots-modal>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <edit-slots-modal
        :adslot="currentItem"
        :assets="assets"
        :time_belts="time_belts"
        ></edit-slots-modal>
        <template v-slot:no-results>
            <v-alert :value="true" color="error" icon="warning">
                Your search for "{{ search }}" found no results.
            </v-alert>
        </template>
    </v-app>
</template>

<script>
  export default {
        props : {
            adslots : {
                required : true,
                type : Array
            },
            assets : {
                required : true,
                type : Array
            },
            time_belts : {
                required : true,
                type : Array
            }
        },
        data () {
            return {
                search: '',
                editDialog: false,
                headers: [
                    { text: 'Day', align: 'left', value: 'day' },
                    { text: 'Program', value: 'program' },
                    {text: 'Duration', value: 'duration'},
                    {text: 'Insertions', value: 'insertions'},
                    { text: 'Program Time', value: 'program_time' },
                    {text : 'Net Total', value : 'net_total'},
                    { text: 'Actions', value: 'name', sortable: false }
                ],
                pagination: {
                    rowsPerPage: 10
                },
                groupedAdslots : [],
                isHidden : true,
                currentItem: {},
            }
        },
        created() {
            var self = this;
            Event.$on('updated-adslots', function (adslot) {
                self.groupedAdslots = adslot;
            });
        },
        mounted () {
            this.groupedAdslots = this.groupAdslotByProgram(this.adslots)            
        },
        methods : {
            openEditDialog : function(item) {
                this.currentItem = item
                Event.$emit('edit-dialog-modal', true)
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