<template>
    <div>
    <v-tabs>
        <v-tabs-slider></v-tabs-slider>
        <v-tab href="#adslot-tab">Adslot</v-tab>
        <v-tab href="#files-tab">Files</v-tab>

        <v-tab-item value="adslot-tab">
            <guest-adslot-list :adslots="adslots" :mpo_id="mpo_id"></guest-adslot-list>
        </v-tab-item>

        <v-tab-item value="files-tab">
            <campaign-file-list :files="campaign_mpo_time_belts" :is_public="true"></campaign-file-list>
        </v-tab-item>
    </v-tabs>
  </div>
</template>
<script>
export default {
    props : {
        adslots : {
            required : true,
            type : Array
        },
        mpo_id : {
            required : true,
            type : String
        },
        campaign_mpo_time_belts : {
            required : true,
            type : [Array, Object]
        }
    }
}
</script>
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

