<template>
    <v-card>
        <v-layout>
            <v-flex>
                <h4 class="text-center"><b>Schedule</b></h4>
            </v-flex>
        </v-layout>
        <v-layout v-for="item in timeBelts" :key="item.id">
            <table class="belts">
                <h6 class="text-center">PLEASE TRANSMIT ({{ item.duration }}SEC) SPOTS AS SCHEDULED BELOW</h6>
                <thead>
                    <tr class="rule">
                        <th class="text-left rule">Months
                        </th>
                        <th class="text-center rule" colspan="31">Insertion Schedule</th>
                        <th class="text-center rule" rowspan="2">Monthly Total</th>
                        <th class="text-center rule" rowspan="2">Descriptions</th>  
                    </tr>
                    <tr class="rule">
                        <th class="text-left rule"> Dates</th>
                        <th class="text-center rule" v-for="(day, index) in dayNumbers" :key="index">
                            {{ day }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="rule" v-for="(adslot, index) in item.slots" :key="index">
                        <td class="rule">{{ item.month }}</td>
                        <td class="rule" v-for="(day, index) in dayNumbers" :key="index">
                            {{ getDayInsertion(adslot, day) }}
                        </td>
                        <td class="rule">
                            {{ adslot.total_spots }}
                        </td>
                        <td class="rule">
                            Station : {{ item.station }} <br>
                            Program : {{ item.program }} <br>
                            <!-- Program Time : <br>  -->
                            Daypart : {{ item.daypart }} <br>
                            Media Asset : {{ adslot.asset }} <br>
                        </td>
                    </tr>
                    <tr class="rule">
                        <td class="rule" colspan="32">
                            <h5>Total Number of Insertions</h5> 
                        </td>
                        <td class="rule">
                            <h5>{{ item.total_insertions }}</h5>
                        </td>
                        <td class="rule"></td>
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
    th.rule {
        border-top : solid 1px;
        border-bottom : solid 1px;
        border-right: solid 1px;
    }
    table.belts {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
</style>