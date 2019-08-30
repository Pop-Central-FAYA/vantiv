<template>
  <div>
    <!-- Campaign Report -->
    <dashboard-campaign-summary 
      :campaign-summary="campaignSummary"
      :redirect-urls="redirectUrls"
    ></dashboard-campaign-summary>
    <!-- client charts -->
    <dashboard-campaign-summary-chart :media-channels="mediaChannels"></dashboard-campaign-summary-chart>
    <!-- Media Plan Report -->
    <dashboard-mediaplan-summary 
      :media-plan-summary="mediaPlanSummary"
      :redirect-urls="redirectUrls"
    ></dashboard-mediaplan-summary>
  </div>
</template>


<script>
  export default {
    props: {
      campaignSummary: Object,
      mediaPlanSummary: Object,
      redirectUrls: Object,
      mediaChannels: Object,
    },
    data () {
      return {
      }
    },
    mounted() {
        console.log('Display dashboard Component mounted.');
    }
  }
</script>