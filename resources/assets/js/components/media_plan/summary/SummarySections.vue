<template>
  <div>
    <v-tabs>
      <v-tabs-slider></v-tabs-slider>
      <v-tab href="#summary-tab">Plan Summary</v-tab>
      <template v-for="(medium, mediumKey) in formattedPlanData.summary_by_medium">
        <template v-for="(data, dataKey) in medium">
          <v-tab v-if="dataKey == 'summary'" :href="`#${mediumKey}${dataKey}-summary-tab`" :key="dataKey">{{ mediumKey }} {{ dataKey }}</v-tab>
          <v-tab v-else :href="`#${mediumKey}${dataKey}-summary-tab`" :key="dataKey">{{ mediumKey }} {{ dataKey }}"</v-tab>
        </template>
      </template>
      <v-tab-item value="summary-tab">
        <media-plan-deliverables :media-plan="formattedPlanData.plan"></media-plan-deliverables>
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
                <tr v-for="(summaryData,key) in formattedPlanData.summary" v-bind:key="key">
                    <td>{{ (summaryData.medium).toUpperCase() }}</td>
                    <td> <span v-for="(list, index) in summaryData.material_durations" v-bind:key="index"> <span>{{list}}</span><span v-if="index+1 < summaryData.material_durations.length">, </span>  </span> </td>
                    <td> {{ summaryData.total_spots }} </td>
                    <td> {{ formatAmount(summaryData.gross_value) }}  </td>
                    <td> {{ formatAmount(summaryData.net_value) }} </td>
                    <td> {{ formatAmount(summaryData.savings) }} </td>
                </tr>
                
                <tr>
                    <td>Total</td>
                    <td></td>
                    <td>{{ totalSpots }}</td>
                    <td>{{ formatAmount(totalGrossValue) }}</td>
                    <td>{{ formatAmount( totalNetValue) }}</td>
                    <td>{{ formatAmount(totalSavings) }}</td>
                </tr>
            </tbody>
        </table>
      </v-tab-item>

      <template v-for="(medium, mediumKey) in formattedPlanData.summary_by_medium">
        <v-tab-item v-for="(data, dataKey) in medium" :key="dataKey+'-body'"  :value="`${mediumKey}${dataKey}-summary-tab`">
          <template v-if="dataKey == 'summary'">
            <!-- render media type summary component -->
            <media-plan-media-type-summary :media-type-summary-data="data"></media-plan-media-type-summary>
          </template>
          <template v-else>
            <!-- render media type summary component -->
            <media-plan-media-type-duration-summary :duration="dataKey" :monthly-weeks="formattedPlanData.table_header_monthly_weeks" :data="data"></media-plan-media-type-duration-summary>
          </template>
        </v-tab-item>
      </template>
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
  import MediaTypeSummary from "./MediaTypeSummary.vue";
  import MediaTypeDuration from "./MediaTypeDuration.vue";

  export default {
    props: {
        formattedPlanData: Object,
    },
    components: {
        'media-plan-media-type-summary': MediaTypeSummary,
        'media-plan-media-type-duration-summary': MediaTypeDuration,
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
            this.formattedPlanData.summary.forEach(function (item, key) {
                self.totalSpots += item['total_spots']
                self.totalGrossValue += item['gross_value']
                self.totalNetValue += item['net_value']
                self.totalSavings += item['savings']
            });
        },
    }
  }
</script>