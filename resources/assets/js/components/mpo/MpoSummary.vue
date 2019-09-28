<template>
    <v-card>
        <v-layout>
            <table>
                <thead>
                    <tr>
                        <th class="text-left">Year</th>
                        <th class="text-left">Media</th>
                        <th class="text-left">Specifications</th>
                        <th class="text-left" >Title of Material</th>
                        <th class="text-left">Spots</th>
                        <th class="text-left">Vol %</th>
                        <th class="text-left">Ag %</th>
                        <th class="text-left">Ag Value</th>
                        <th class="text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in timeBeltSummary" :key="item.id">
                        <td>{{ item.year }}</td>
                        <td>{{ item.publisher_name }}</td>
                        <td v-if="item.material_title">{{ item.material_title }}</td>
                        <td v-else></td>
                        <td>{{ item.duration }} Seconds <br> {{ item.day_parts }} </td>
                        <td>{{ item.total_spot }}</td>
                        <td> {{ item.volume_percent }} </td>
                        <td> 15 </td>
                        <td>{{ numberFormat(item.agency_commission) }}</td>
                        <td>{{ numberFormat(item.total) }}</td>
                    </tr>
                </tbody>
            </table>
        </v-layout>
        <v-layout>
            <v-flex>
                <v-card-text class="text-right">
                    <div class="outer">
                        <div class="left-item"><h4>Total Budget : </h4></div>
                        <div class="right-item" ><h4> {{ numberFormat(totalBudget) }}</h4></div>
                    </div>
                    <div class="outer">
                        <div class="left-item"><h4>VAT : </h4></div>
                        <div class="right-item" ><h4> 5%</h4></div>
                    </div>
                    <div class="outer">
                        <div class="left-item"><h4>Net Total : </h4></div>
                        <div class="right-item" ><h4> {{ numberFormat(netTotal) }}</h4></div>
                    </div>
                </v-card-text>
            </v-flex>
        </v-layout>
    </v-card>
</template>
<script>
export default {
    props : {
        timeBeltSummary : Array,
        totalBudget : Number,
        netTotal : Number,
        mpoDetails : Object
    },
}
</script>
<style >
    .layout  {
        margin-top : 20px !important;
    }
    .left-item {
        width: 140px; 
        display: inline-block;
        text-align: right
    }
    .right-item {
        display: inline-block;
        width: 150px;
        text-align: left;
    }
    .outer {
        display: block
    }
</style>