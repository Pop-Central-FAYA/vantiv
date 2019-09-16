<template>
  <div>
    <v-tabs>
      <v-tabs-slider></v-tabs-slider>
      <v-tab href="#summary-tab">Summary</v-tab>
      <v-tab href="#files-tab">Files</v-tab>
      <v-tab href="#grouped-adslots">Grouped Adslots</v-tab>
      <v-tab href="#adslots">Adslots</v-tab>
      <v-tab href="#mpos-tab">Mpos</v-tab>

      <v-tab-item value="summary-tab">
        <campaign-summary :campaign="campaign"></campaign-summary>
      </v-tab-item>

      <v-tab-item value="files-tab">
        <campaign-file-list :files="files"></campaign-file-list>
      </v-tab-item>

      <v-tab-item value="grouped-adslots">
        <list-group-adslot :assets="assets" :grouped-campaign-time-belts="groupedCampaignTimeBelts"
        :time-belt-range="timeBelts" :ad-vendors="adVendors"
        :campaign-id="campaignId"></list-group-adslot>
      </v-tab-item>

      <v-tab-item value="adslots">
        <list-campaign-adslot 
            :assets="assets" 
            :campaign-time-belts="campaignTimeBelts"
            :time-belt-range="timeBelts"
            :ad-vendors="adVendors"
            :campaign-id="campaignId"
            :client="client"
            :brand="brand"
        ></list-campaign-adslot>
      </v-tab-item>
      
      <v-tab-item value="mpos-tab">
        <campaign-mpos-list :client="client" :brand="brand" :campaign-id="campaignId" ></campaign-mpos-list>
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
        files: [Object, Array],
        mpos: Array,
        assets: Array,
        client: String,
        brand: String,
        campaign: Object,
        timeBelts: Array,
        adVendors : Array,
        campaignTimeBelts : Array,
        groupedCampaignTimeBelts : Array,
        campaignId : String,
    },
    data () {
      return {

      }
    }
  }
</script>