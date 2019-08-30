<template>
    <!-- Campaign Report -->
    <div class="the_stats the_frame clearfix mb3">
        <div class="column col_3">
            <span class="weight_medium small_faint uppercased">Active Campaigns</span>
            <h3><a :href="redirectUrls.active_campaigns">{{ campaignSummary.count_active_campaigns }}</a></h3>
        </div>

        <div class="column col_3">
            <span class="weight_medium small_faint uppercased">Campaigns On Hold</span>
            <h3><a :href="redirectUrls.campaigns_on_hold" style="color: red;">{{ campaignSummary.count_campaigns_on_hold }}</a></h3>
        </div>

        <div class="column col_3">
            <span class="weight_medium small_faint uppercased">All Clients</span>
            <h3><a :href="redirectUrls.all_clients">{{ campaignSummary.count_all_clients }}</a></h3>
        </div>

        <div class="column col_3">
            <span class="weight_medium small_faint uppercased">All Brands</span>
            <h3><a :href="redirectUrls.all_brands">{{ campaignSummary.count_all_brands }}</a></h3>
        </div>
    </div>
</template>

<script>
  export default {
    props: {
      campaignSummary: Object,
      redirectUrls: Object,
    },
    data () {
      return {
        
      }
    },
    mounted() {
        console.log('Display campaign summary Component mounted.');
    }
  }
</script>