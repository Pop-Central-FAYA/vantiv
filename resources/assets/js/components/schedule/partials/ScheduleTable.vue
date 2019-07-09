<template>
    <div class="table-wrapper">
        <table class="table table-earnings table-earnings__challenge">
            <thead>
                <tr>
                    <th class="split_column"></th>
                    <th class="split_column" v-for="(week, key) in current_week" 
                    :key="key"
                    :class="{'active' : week.date === week.current_date}"
                    >{{ week.day_number }} {{ week.name }}</th>
                </tr>                
            </thead>
            <tbody>
                <tr v-for="(time_belt, key) in hours_breakdown" :key="key">
                    <td class="split_column bold">
                        {{ time_belt.start_time }} 
                    </td>
                    <td class="split_column" v-for="(week, key) in current_week" :key="key">
                        <table>
                            <tr v-for="(event_group, key) in groupEventByDateProgramTimeBelt(events, week.date, time_belt)" :key="key">
                                <td style="border : 0;"> 
                                    <ad-hour-table
                                        :event_group="event_group"
                                    ></ad-hour-table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>     

            </tbody>
        </table>
    </div>
</template>
<script>
    export default {
        props : {
            hours_breakdown : {
                required : true,
                type: Array
            },
            current_week : {
                required : true,
                type : Array
            },
            events : {
                required : true,
                type : Array
            }
        },
        methods : {
            filterEventByTimeBelt: function(events, date, time_belt) {
                return events.filter(function(event) {
                    return (event.playout_date === date && event.time_belt === time_belt.start_time);
                });
            },
            groupEventByDateProgramTimeBelt : function(events, date, time_belt){
                var filtered_slots = this.filterEventByTimeBelt(events, date, time_belt);
                var grouped_data = _.groupBy(filtered_slots, function(event) {
                    return event.playout_date+"-"+event.program_name+"-"+event.time_belt
                });
                return grouped_data
            }
        }
    }
</script>

