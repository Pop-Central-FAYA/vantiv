<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div id="suggestions-accordion">
                    <div class="card border-0">
                        <div class="card-header border-top rounded-0" id="headingOne">
                            <div class="row py-1">
                                <div class="col-md-6">
                                    <h6>STATION</h6>
                                </div>
                                <div class="col-md-6">
                                    <h6>AUDIENCE</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-0" v-for="(timebelts,key,index) in suggestions" v-bind:key="key">
                        <div class="card-header card-header bg-white py-2 border-bottom clickable" :id="'heading-'+index" data-toggle="collapse" :data-target="'#collapse-'+index" aria-expanded="true" :aria-controls="'collapse-'+index">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="h6"><small>{{ key }}</small></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="h6"><small>{{ total_audience_per_station(timebelts) }}</small></p>
                                </div>
                            </div>
                        </div>
                        <div :id="'collapse-'+index" class="collapse" :aria-labelledby="'heading-'+index" data-parent="#suggestions-accordion">
                            <div class="card-body card-body pl-3 pr-0 pt-0 pb-1 bg-secondary">
                                <table class="table">
                                    <thead class="thead-light">
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
                                                <button class="plus-btn" @click="add_timebelt(timebelt)" type="button"><i class="material-icons">add</i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

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
            total_audience_per_station(timeBelts) {
                var totalAudience = 0;
                timeBelts.forEach(function (element) {
                    totalAudience += element.total_audience;
                });
                return this.format_audience(totalAudience);
            },
            add_timebelt(timeBelt) {
                this.newTimeBelt = timeBelt;
                Event.$emit('timebelt-to-add', timeBelt);
                var time = `${this.format_time(timeBelt.start_time)} - ${this.format_time(timeBelt.end_time)}`;
                var successMsg = `${timeBelt.station} - ${timeBelt.program}  showing on  ${timeBelt.day}  ${time} added successfully`;
                this.sweet_alert(successMsg, 'success');
            }
        }
    }
</script>
