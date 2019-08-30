<template>
    <!-- Media Plan Report -->
    <div class="the_stats the_frame clearfix">
        <div class="column col_4">
            <span class="weight_medium small_faint uppercased">Approved Media Plans</span>
            <h3><a :href="redirectUrls.approved_media_plans">{{ mediaPlanSummary.count_approved_media_plans }}</a></h3>
        </div>

        <div class="column col_4">
            <span class="weight_medium small_faint uppercased">Pending Media Plans</span>
            <h3><a :href="redirectUrls.pending_media_plans" style="color: red;">{{ mediaPlanSummary.count_pending_media_plans }}</a></h3>
        </div>

        <div class="column col_4">
            <span class="weight_medium small_faint uppercased">Declined Media Plans</span>
            <h3><a :href="redirectUrls.declined_media_plans" style="color: red;">{{ mediaPlanSummary.count_declined_media_plans }}</a></h3>
        </div>
    </div>
</template>

<script>
  export default {
    props: {
      mediaPlanSummary: Object,
      redirectUrls: Object,
    },
    data () {
      return {
      }
    },
    mounted() {
        console.log('Display Media plan summary Component mounted.');
    }
  }
</script>