<template>
    <table>
        <tr>
            <div class="row">
                <div class="col-4 program" :style="{ 'background-color' : event_group[0].background_color }" 
                        v-if="event_group[0].program_name">
                    <td style="border : 0;">
                        <p class="program_name">{{ event_group[0].program_name }}</p> 
                    </td>
                </div>
                <div class="col-8">
                    <ad-break-table
                    :event_group="event_group"
                    ></ad-break-table>
                </div>
            </div>
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
        }
    }
</script>