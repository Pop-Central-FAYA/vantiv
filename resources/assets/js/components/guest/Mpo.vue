<template>
    <div>
        <mpo-header
            :mpo-details="mpoDetails.mpo_details"
            :previous-reference="mpoDetails.previous_reference"
            :previous-mpo="mpoDetails.previous_mpo"
            :company="mpoDetails.company"
            :vendor="mpoDetails.mpo_details.vendor"
            :is-public="isPublic"
        ></mpo-header>
        <v-tabs>
            <v-tabs-slider></v-tabs-slider>
            <v-tab href="#mpo-details-tab">Details</v-tab>
            <v-tab href="#adslot-tab">Spots</v-tab>
            <v-tab href="#files-tab">Files</v-tab>

            <v-tab-item value="mpo-details-tab">
                <guest-mpo-details :mpo-details="mpoDetails" :is-public="isPublic"></guest-mpo-details>
            </v-tab-item>

            <v-tab-item value="adslot-tab">
                <guest-adslot-list :adslots="campaign_mpo_time_belts"></guest-adslot-list>
            </v-tab-item>

            <v-tab-item value="files-tab">
                <campaign-file-list :files="files" :is_public="true"></campaign-file-list>
            </v-tab-item>
        </v-tabs>
    </div>
</template>
<script>
export default {
    props : {
        mpoDetails : {
            required : true,
            type : Object
        },
        campaign_mpo_time_belts : {
            required : true,
            type : [Array, Object]
        },
        files : {
            required : true,
            type : [Array, Object]
        }
    },
    data () {
        return {
            isPublic : true
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

