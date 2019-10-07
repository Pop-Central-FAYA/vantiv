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
            <tr v-for="(summary, key) in mediaTypeSummaryData.summary_by_duration.data" :key="key">
                <td class="td-border-left-thick" style="height: 21;">{{ summary['length'] }} Seconds</td>
                <td style="height: 21;">{{ summary['duration'] }} {{ (summary['duration'] == 1) ? "Week":"Weeks" }}</td>
                <td style="height: 21;">{{ summary['total_spots'] }}</td>
                <td style="height: 21;">{{ numberFormat(summary['gross_total']) }}</td>
                <td class="td-border-right-thick" style="height: 21;">{{ numberFormat(summary['net_total']) }}</td>
            </tr>

            <tr>
                <td class="td-border-left-thick"></td>
                <td></td>
                <td style="height: 21; font-weight: bold;">{{ mediaTypeSummaryData.summary_by_duration.totals.total_spots }}</td>
                <td style="height: 21; font-weight: bold;">{{ numberFormat(mediaTypeSummaryData.summary_by_duration.totals.gross_total, 2) }}</td>
                <td class="td-border-right-thick" style="height: 21; font-weight: bold;">{{ numberFormat(mediaTypeSummaryData.summary_by_duration.totals.net_total, 2) }}</td>
            </tr>
            <tr>
                <td class="td-border-top-thick td-border-left-thick td-border-bottom-thick" style="font-size: 16;">VAT on Media Cost (5%)</td>
                <td class="td-border-top-thick td-border-bottom-thick"></td>
                <td class="td-border-top-thick td-border-bottom-thick"></td>
                <td class="td-border-top-thick td-border-bottom-thick"></td>
                <td class="td-border-top-thick td-border-right-thick td-border-bottom-thick" style="font-weight: bold">{{ numberFormat(mediaTypeSummaryData.summary_by_duration.totals.vat, 2) }}</td>
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
            <tr v-for="(summary, key) in mediaTypeSummaryData.summary_by_station_type.data" :key="key">
                <td class="td-border-left-thick" style="height: 21;">{{ summary.duration }} Seconds</td>
                <td style="height: 21;">{{ (summary.station_type).toUpperCase() }}</td>
                <td style="height: 21; text-align: left;">{{ summary.total_spots }}</td>
                <td class="td-border-right-thick" style="height: 21; text-align: left;">{{ numberFormat(summary.net_total, 2) }}</td>
            </tr>
            <tr>
                <td class="td-border-top-thick td-border-left-thick td-border-bottom-thick" style="height: 21; font-weight: bold">Total</td>
                <td class="td-border-top-thick td-border-bottom-thick"></td>
                <td class="td-border-top-thick td-border-bottom-thick" style="height: 21; font-weight: bold; text-align: left;">{{ mediaTypeSummaryData.summary_by_station_type.totals.total_spots }}</td>
                <td class="td-border-top-thick td-border-right-thick td-border-bottom-thick" style="height: 21; font-weight: bold; text-align: left;">{{ numberFormat(mediaTypeSummaryData.summary_by_station_type.totals.net_total, 2) }}</td>
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
        console.log("Mounted  media type summary component");
    },
    methods: {
    }
  }
</script>