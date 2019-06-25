<template>
    <table>
        <tr v-for="program in playout_hour" :key="program.program_id">
            <div class="row">
                <div class="col-4 program" :style="{ 'background-color' : program.background_color }" 
                        v-if="program.program_name">
                    <td style="border : 0;">
                        <p class="program_name">{{ program.program_name }}</p> 
                    </td>
                </div>
                <div class="col-8">
                    <table>
                        <tr v-for="(playout, key) in program.program_ad_break" :key="key">
                            <td >
                                <p class="center bold"> {{ playout.ad_break }}</p> <br>
                                <p>({{ sumDurationInAdBreak(playout.ads) }} seconds used from {{ ad_pattern_duration }})</p>
                                <ad-break-table
                                    :ads="playout.ads"
                                ></ad-break-table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </tr>
    </table>
</template>
<script>
    export default {
        props : {
            playout_hour : {
                required : true,
                type : Array
            },
            ad_pattern_duration : {
                required : true,
                type : String
            }
        },
        methods : {
            sumDurationInAdBreak : function(ad_break) {
                return ad_break.reduce((prev,next) => prev + next.duration,0);
            }
        }
    }
</script>