<template>
    <v-app>
        <v-card>
            <v-card-title>
                Adslots
            <v-spacer></v-spacer>
            <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
            </v-card-title>
            <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="groupedAdslots" :search="search">
            <template v-slot:items="props">
                <td>{{ props.item.day }} ({{ props.item.playout_date }})</td>
                <td class="text-xs-left">{{ props.item.program }}</td>
                <td class="text-xs-left">{{ props.item.duration }} Seconds</td>
                <td class="text-xs-left">{{ props.item.ad_slots }} </td>
                <td class="text-xs-left">{{ props.item.time_belt_start_time }}</td>
                <td class="justify-center layout px-0">
                    <v-btn fab dark small color="cyan">
                        <v-icon dark>edit</v-icon>
                    </v-btn>
                    <delete-slots-modal
                    :adslot="props.item"
                    ></delete-slots-modal>
                </td>
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

<script>
  export default {
        props : {
            adslots : {
                required : true,
                type : Array
            }
        },
        data () {
            return {
                search: '',
                headers: [
                    { text: 'Day', align: 'left', value: 'day' },
                    { text: 'Program', value: 'program' },
                    {text: 'Duration', value: 'duration'},
                    {text: 'Exposures', value: 'exposures'},
                    { text: 'Time Belt', value: 'time_belt_start_time' },
                    { text: 'Actions', value: 'name', sortable: false }
                ],
                groupedAdslots : [],
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
        }
    }
</script>