<template>
    <v-app>
        <v-card>
            <v-card-title>
                Adslots
            <v-spacer></v-spacer>
            <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
            </v-card-title>
            <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="adslots" :search="search">
            <template v-slot:items="props">
                <td>{{ props.item.day }}</td>
                <td class="text-xs-left">{{ props.item.program }}</td>
                <td class="text-xs-left">{{ props.item.time_belt_start_time }}</td>
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
                    { text: 'Time Belt', value: 'time_belt_start_time' },
                ]
            }
        },
    }
</script>