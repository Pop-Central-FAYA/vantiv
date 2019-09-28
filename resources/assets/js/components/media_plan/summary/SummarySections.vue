<template>
  <div>
    <v-tabs>
      <v-tabs-slider></v-tabs-slider>
      <v-tab href="#summary-tab">Plan Summary</v-tab>
      <v-tab v-for="(durations, durationKey) in fullStationData.station_data" :key="durationKey+'-header'" :href="`#${durationKey}-summary-tab`">
        {{ durationKey }}
      </v-tab>
      <v-tab-item value="summary-tab">
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
                <tr v-for="(summaryData,key) in summaryData" v-bind:key="key">
                    <td>{{ summaryData.medium }}</td>
                    <td> <span v-for="(list, index) in summaryData.material_durations" v-bind:key="index"> <span>{{list}}</span><span v-if="index+1 < summaryData.material_durations.length">, </span>  </span> </td>
                    <td> {{ summaryData.total_spots }} </td>
                    <td>{{ numberFormat(summaryData.gross_value) }}  </td>
                    <td> {{ numberFormat(summaryData.net_value) }}</td>
                    <td> {{ numberFormat(summaryData.savings) }}</td>
                </tr>
                
                <tr>
                    <td>Total</td>
                    <td></td>
                    <td>{{ totalSpots }}</td>
                    <td>{{ numberFormat(totalGrossValue) }}</td>
                    <td>{{ numberFormat( totalNetValue) }}</td>
                    <td>{{ numberFormat(totalSavings) }}</td>
                </tr>
            </tbody>
        </table>
      </v-tab-item>
      <v-tab-item v-for="(durations, durationKey) in fullStationData.station_data" :key="durationKey+'-body'"  :value="`${durationKey}-summary-tab`">
        <div v-if="durationKey.includes('summary')">
            <!-- render media type summary component -->
            <media-plan-media-type-summary :media-type-summary-data="durations"></media-plan-media-type-summary>
        </div>
        <div v-else>
            <!-- render media type summary component -->
            <media-plan-media-type-duration-summary :data="durations"></media-plan-media-type-duration-summary>
        </div>
      </v-tab-item>
    </v-tabs>
  </div>
</template>

<style>
    .v-tabs {
        background: #fafafa;
    }
    a.v-tabs__item {
        padding: 0px 3rem;
        text-decoration: none !important;
        border: 1px solid #e8e8e8;
        border-left: none !important;
    }
    a.v-tabs__item.v-tabs__item--active {
        background: #01c4ca;
        color: #fff;
    }
    .accent {
        background-color: #01c4ca !important;
        border-color: #01c4ca !important;
    }
</style>


<script>
  export default {
    props: {
        summaryData: Array,
        fullStationData: Object,
    },
    data () {
      return {
        totalSpots: 0,
        totalGrossValue: 0,
        totalNetValue: 0,
        totalSavings: 0,
      }
    },
    mounted() {
        console.log("new summary component mounted");
        this.getSums();
    },
    methods: {
        getSums(){
            let self = this;
            this.summaryData.forEach(function (item, key) {
                self.totalSpots += item['total_spots']
                self.totalGrossValue += item['gross_value']
                self.totalNetValue += item['net_value']
                self.totalSavings += item['savings']
            });
        },
    }
  }
</script>