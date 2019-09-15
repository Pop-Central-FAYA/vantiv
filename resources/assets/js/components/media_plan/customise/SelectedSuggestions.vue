<template>
    <v-card px-0 pt-0 style="width: 100%">
        <v-card-title>
            <h5>SELECTED STATIONS AND TIMES</h5>
        </v-card-title>
        <v-card-text class="px-0 pt-0 pb-0" style="height:45vh; overflow: auto">
            <v-data-table class="custom-vue-table elevation-1" :headers="headers" hide-actions :items="timeBeltsArr" :pagination.sync="pagination">
                <template v-slot:items="props">
                    <tr>
                        <td class="text-xs-left">{{ props.item.station }}</td>
                        <td class="text-xs-left">{{ props.item.day }}</td>
                        <td class="text-xs-left">{{ format_time(props.item.start_time) }} - {{ format_time(props.item.end_time) }}</td>
                        <td class="text-xs-left">{{ props.item.program }}</td>
                        <td class="text-xs-left">{{ format_audience(props.item.total_audience) }}</td>
                        <td class="text-xs-left">{{ props.item.rating }}</td>
                        <td class="text-xs-left">
                            <v-icon color="danger" style="color: red !important" dark @click="deleteTimebelt(props.item, props.index)">delete</v-icon>
                        </td>
                    </tr>
                </template>
            </v-data-table>
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
                timeBeltsArr: this.selectedTimeBelts,
                headers: [
                    { text: 'Station', align: 'left', value: 'station', width: '20%' },
                    { text: 'Day', align: 'left', value: 'day', width: '20%' },
                    { text: 'Time Belt', value: 'end_time', width: '25%' },
                    { text: 'Program', value: 'program', width: '25%' },
                    { text: 'Audience', value: 'total_audience', width: '24%' },
                    { text: 'Rating', value: 'rating', width: '5%' },
                    { text: 'Actions', value: 'name', width: '1%', sortable: false }
                ],
                pagination: {
                    rowsPerPage: -1
                },
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
