<template>
    <table>
        <p>({{ sumDurationInAdBreak(event_group) }} seconds used from {{ event_group[0].ad_pattern }})</p>
        <tr v-for="event in event_group" :key="event.time_belt_transaction_id">
            <td >
                <div class="card_table">
                    {{ event.campaign_name }} <br>
                    {{ event.client_name }} <br>
                    {{ event.duration }}
                </div>
            </td>
        </tr>
    </table>
</template>
<script>
    export default {
        props : {
            event_group : {
                required : true,
                type : Array
            }
        },
        methods : {
            sumDurationInAdBreak : function(ad_break) {
                return ad_break.reduce((prev,next) => prev + next.duration,0);
            }
        }
    }
</script>

