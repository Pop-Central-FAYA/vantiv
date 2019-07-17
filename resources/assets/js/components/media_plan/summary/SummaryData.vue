<template>
     <div class="the_frame client_dets mb4">
                    <!-- Suggestions table -->
                    <table class="display dashboard_campaigns">
                        <thead>
                        <tr>
                            <th>Medium</th>
                            <th>Material Duration</th>
                            <th>Number of Spots/units</th>
                            <th>Gross Media Cost</th>
                            <th>Net Media Cost</th>
                            <th>Savings</th>
                        </tr>
                        </thead>
                        <tbody>
                                <tr v-for="(sumData,key) in sumData" v-bind:key="key">
                                    <td>{{ sumData.medium }}</td>
                                    <td> <span v-for="(list, index) in sumData.material_durations" v-bind:key="index"> <span>{{list}}</span><span v-if="index+1 < sumData.material_durations.length">, </span>  </span> </td>
                                    <td> {{ sumData.total_spots }} </td>
                                    <td>{{ nunberformat(sumData.gross_value) }} </td>
                                    <td> {{ nunberformat(sumData.net_value) }}</td>
                                    <td> {{ nunberformat(sumData.savings) }}</td>
                                </tr>
                          
                            <tr>
                                <td>Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- end -->
                </div>

</template>

<script>
    export default {
        props: {
            summaryData: Array,
 
        },
        data() {
            return {
                sumData: this.summaryData
            };
        },
        mounted() {
            console.log(this.sumData)
            console.log('Suggestions Table Component mounted.')
        },
         methods: {
            nunberformat(n) {
                  return n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '');
               },
           
         }
        }
</script>
