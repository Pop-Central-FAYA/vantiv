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
                <tr v-for="(hour, key) in hours" :key="key">
                    <td class="split_column bold">
                        {{ hour }} 
                    </td>
                    <td class="split_column" v-for="(week, key) in current_week" :key="key">
                        <table>
                            <tr v-for="(event, key) in filterEventByHour(hour, week.date)" :key="key">
                                <td style="border : 0;"> 
                                    <ad-hour-table
                                        :playout_hour="event.playout_hour"
                                        :ad_pattern_duration="event.ad_pattern"
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
            hours : {
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
            filterEventByHour: function(hour, date) {
                return this.events.filter(function(event) {
                    return (event.hour === hour && event.playout_date === date);
                });
            }
        }
    }
</script>

