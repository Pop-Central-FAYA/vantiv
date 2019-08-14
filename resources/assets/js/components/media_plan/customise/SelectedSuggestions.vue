<template>
    <v-card px-0 pt-0 style="width: 100%">
        <v-card-title>
            <h5>SELECTED STATIONS AND TIMES</h5>
        </v-card-title>
        <v-card-text class="px-0 pt-0 pb-0">
            <table class="table mb-0">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">STATION</th>
                        <th scope="col">DAY</th>
                        <th scope="col">TIME BELT</th>
                        <th scope="col">PROGRAM</th>
                        <th scope="col">AUDIENCE</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(timebelt,key) in timeBeltsArr" v-bind:key="key">
                        <th scope="row">{{ timebelt.station}}</th>
                        <td>{{ timebelt.day}}</td>
                        <td>{{ format_time(timebelt.start_time) +" - "+ format_time(timebelt.end_time) }}</td>
                        <td>{{ timebelt.program}}</td>
                        <td>{{ format_audience(timebelt.total_audience) }}</td>
                        <td>
                            <button class="plus-btn" @click="deleteTimebelt(timebelt, key)" type="button"><i class="material-icons" style="color: red">delete</i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </v-card-text>
    </v-card>
</template>

<script>
    export default {
        props: {
            selectedTimeBelts: Array,
            planStatus: String,
            planId: String
        },
        data() {
            return {
                accordionCounter: 0,
                timeBeltsArr: this.selectedTimeBelts
            };
        },
        watch: {
            timeBeltsArr() {
                Event.$emit('selected-timebelts', this.timeBeltsArr);
            }
        },
        created() {
            var self = this;
            Event.$on('timebelt-to-add', function (timeBelt) {
                self.timeBeltsArr.push(timeBelt);
            });
        },
        mounted: function mounted() {
            console.log('Selected Suggetions Component mounted.');
            this.$nextTick(function () {
                Event.$emit('selected-timebelts', this.timeBeltsArr);
            })
        },
        methods: {
            deleteTimebelt(timeBelt, index) {
                this.timeBeltsArr.splice(index, 1);
                Event.$emit('timebelt-unselected', timeBelt);
                var time = `${this.format_time(timeBelt.start_time)} - ${this.format_time(timeBelt.end_time)}`;
                var success_msg = `${timeBelt.station} - ${timeBelt.program}  showing on  ${timeBelt.day}  ${time} was removed`;
                this.sweet_alert(success_msg, 'error');
            }
        }
    }
</script>
