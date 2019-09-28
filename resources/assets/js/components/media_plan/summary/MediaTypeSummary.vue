<template>
  <div>
    <table class="display dashboard_campaigns">
        <thead>
            <tr>
                <th class="td-border-top-thick td-border-left-thick" style="height: 21; font-weight: bold">Campaign</th>
                <th class="td-border-top-thick" style="height: 21; font-weight: bold">Duration</th>
                <th class="td-border-top-thick" style="height: 21; font-weight: bold">Total Spots</th>
                <th class="td-border-top-thick" style="height: 21; font-weight: bold">Gross Total</th>
                <th class="td-border-top-thick td-border-right-thick" style="height: 21; font-weight: bold">Net Total</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(summary, key) in mediaTypeSummaryData.durations.data" :key="key">
                <td class="td-border-left-thick" style="height: 21;">{{ summary['length'] }} Seconds</td>
                <td style="height: 21;">{{ summary['duration'] }} Weeks</td>
                <td style="height: 21; text-align: right;">{{ summary['total_spots'] }}</td>
                <td style="height: 21; text-align: right;">{{ numberFormat(summary['gross_total']) }}</td>
                <td class="td-border-right-thick" style="height: 21; text-align: right;">{{ numberFormat(summary['net_total']) }}</td>
            </tr>

            <tr>
                <td class="td-border-left-thick"></td>
                <td></td>
                <td style="height: 21; font-weight: bold; text-align: right;">{{ mediaTypeSummaryData.durations.totals.total_spots }}</td>
                <td style="height: 21; font-weight: bold; text-align: right;">{{ numberFormat(mediaTypeSummaryData.durations.totals.gross_total, 2) }}</td>
                <td class="td-border-right-thick" style="height: 21; font-weight: bold; text-align: right;">{{ numberFormat(mediaTypeSummaryData.durations.totals.net_total, 2) }}</td>
            </tr>
            <tr>
                <td class="td-border-top-thick td-border-left-thick td-border-bottom-thick" style="font-size: 16;">VAT on Media Cost (5%)</td>
                <td class="td-border-top-thick td-border-bottom-thick"></td>
                <td class="td-border-top-thick td-border-bottom-thick"></td>
                <td class="td-border-top-thick td-border-bottom-thick"></td>
                <td class="td-border-top-thick td-border-right-thick td-border-bottom-thick" style="text-align: right; font-weight: bold">{{ numberFormat(mediaTypeSummaryData.durations.totals.vat, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="display dashboard_campaigns my-3">
        <thead>
           <tr>
                <th class="td-border-top-thick td-border-left-thick" style="height: 21; font-weight: bold">Campaign</th>
                <th class="td-border-top-thick"></th>
                <th class="td-border-top-thick" style="height: 21; font-weight: bold;">Total Spots</th>
                <th class="td-border-top-thick td-border-right-thick" style="height: 21; font-weight: bold">Net Total</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(summary, key) in mediaTypeSummaryData.station_types.data" :key="key">
                <td class="td-border-left-thick" style="height: 21; font-weight: bold">{{ summary.duration }} Seconds</td>
                <td style="height: 21; font-weight: bold">{{ summary.station_type }}</td>
                <td style="height: 21; font-weight: bold; text-align: left;">{{ summary.total_spots }}</td>
                <td class="td-border-right-thick" style="height: 21; font-weight: bold; text-align: left;">{{ numberFormat(summary.net_total, 2) }}</td>
            </tr>
            <tr>
                <td class="td-border-top-thick td-border-left-thick td-border-bottom-thick" style="height: 21; font-weight: bold">Total</td>
                <td class="td-border-top-thick td-border-bottom-thick"></td>
                <td class="td-border-top-thick td-border-bottom-thick" style="height: 21; font-weight: bold; text-align: left;">{{ mediaTypeSummaryData.station_types.totals.total_spots }}</td>
                <td class="td-border-top-thick td-border-right-thick td-border-bottom-thick" style="height: 21; font-weight: bold; text-align: left;">{{ numberFormat(mediaTypeSummaryData.station_types.totals.net_total, 2) }}</td>
            </tr>
        </tbody>
    </table>

  </div>
</template>

<style>
    td {
        border: 1px solid #cccccc;
    }
    /* .td-border-top-thick {
        border-top: 2px solid #000000;
    }
    .td-border-left-thick {
        border-left: 2px solid #000000;
    }
    .td-border-right-thick {
        border-right: 2px solid #000000;
    }
    .td-border-bottom-thick {
        border-bottom: 2px solid #000000;
    } */
</style>


<script>
  export default {
    props: {
        mediaTypeSummaryData: Object,
    },
    data () {
      return {
        summaryByDuration: [],
        summaryByDurationStationType: [],
      }
    },
    mounted() {
        console.log(this.mediaTypeSummaryData);
    },
    methods: {
    }
  }
</script>