<template>
    <v-card>
        <v-layout>
            <v-flex>
                <h4 class="text-center"><b>Schedule</b></h4>
            </v-flex>
        </v-layout>
        <v-layout>
            <table class="belts">
                <thead>
                    <tr>
                        <th class="text-left">Station</th>
                        <th class="text-left">Programs</th>
                        <th class="text-left position">Positions</th>
                        <th class="text-left">Month</th>
                        <th class="text-left" v-for="(item, index) in dayNumbers" :key="index">
                            {{ item }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="rule" v-for="item in timeBelts" :key="item.id">
                        <td class="rule">{{ item.station }}</td>
                        <td class="rule">{{ item.program }}</td>
                        <td class="rule" width="30%">{{ item.day_range }}  <br> {{ item.time_slot[0] }} - {{ item.time_slot[1] }} </td>
                        <td class="rule">{{ item.month }}</td>
                        <td class="rule" v-for="(day, index) in dayNumbers" :key="index">
                            {{ getDayInsertion(item, day) }}
                        </td>
                        <td class="rule">{{ item.total_slot }}</td>
                    </tr>
                </tbody>
            </table>
        </v-layout>
    </v-card>
</template>
<script>
export default {
    props : {
        dayNumbers : Array,
        timeBelts : Array,
    },
    methods : {
        getDayInsertion : function (time_belt, day) {
            if(time_belt.exposures[day]){
                return time_belt.exposures[day]
            }else{
                return ''
            }
        }
    }
}
</script>
<style >
    .layout  {
        margin-top : 20px !important;
    }
    td.rule {
        border-right: solid 1px;
        border-bottom: 0 !important;
        padding: 1px 24px !important;
        font-size: 11px !important;
    }
    tr.rule {
        border-top : solid 1px;
        border-bottom : solid 1px;
    }
    table.belts {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
</style>