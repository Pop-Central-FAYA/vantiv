<template>
    <v-layout row wrap class="white-bg" px-0 pb-0>
        <v-flex xs12 sm12 md12 lg12 px-0 pb-0>
            <v-expansion-panel px-0>
                <v-layout px-4 py-2>
                    <v-flex><h4>STATION</h4></v-flex>
                    <v-flex style="margin-left: -20px;"><h4>AUDIENCE</h4></v-flex>
                </v-layout>
                <v-expansion-panel-content v-for="(timebelts, key) in suggestions" v-bind:key="key">
                    <template v-slot:header>
                        <div style="width: 40px">{{ key }}</div>
                        <div style="width: 40px">{{ totalAudiencePerStation(timebelts) }}</div>
                    </template>
                    <v-card>
                        <v-card-text>
                            <v-layout wrap style="border-bottom: 1px solid #e8e8e8;">
                                <v-flex md12 px-0 style="overflow-x:auto;  height: 40vh;">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th scope="col">DAY</th>
                                                <th scope="col">TIME BELT</th>
                                                <th scope="col">PROGRAM</th>
                                                <th scope="col">AUDIENCE</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(timebelt,key) in timebelts" v-bind:key="key">
                                                <th scope="row">{{ timebelt.day}}</th>
                                                <td>{{ format_time(timebelt.start_time) +" - "+ format_time(timebelt.end_time) }}</td>
                                                <td>{{ timebelt.program}}</td>
                                                <td>{{ format_audience(timebelt.total_audience) }}</td>
                                                <td>
                                                    <button class="plus-btn" @click="addTimebelt(timebelt)" type="button"><i class="material-icons">add</i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </v-flex>
                            </v-layout>
                        </v-card-text>
                    </v-card>
                </v-expansion-panel-content>
            </v-expansion-panel>
        </v-flex>
    </v-layout>
</template>

<style>
.v-expansion-panel__header {
    /* padding: 0 !important; */
    font-size: 14px;
}
</style>


<script>
    export default {
        props: {
            suggestions: Object,
        },
        data() {
            return {
                accordionCounter: 0,
                newTimeBelt: {}
            };
        },
        mounted() {
            console.log('Suggestions Table Component mounted.')
        },
        methods: {
            totalAudiencePerStation(time_belts) {
                var total_audience = 0;
                time_belts.forEach(function (element) {
                    total_audience += element.total_audience;
                });
                return this.format_audience(total_audience);
            },
            addTimebelt(time_belt) {
                this.newTimeBelt = time_belt;
                Event.$emit('timebelt-to-add', time_belt);
                var time = `${this.format_time(time_belt.start_time)} - ${this.format_time(time_belt.end_time)}`;
                var successMsg = `${time_belt.station} - ${time_belt.program}  showing on  ${time_belt.day}  ${time} added successfully`;
                this.sweet_alert(successMsg, 'success');
            }
        }
    }
</script>
