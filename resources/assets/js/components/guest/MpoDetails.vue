<template>
    <div>
        <mpo-header v-if="!isPublic"
            :mpo-details="mpoDetails.mpo_details"
            :previous-reference="mpoDetails.previous_reference"
            :previous-mpo="mpoDetails.previous_mpo"
            :company="mpoDetails.company"
            :vendor="mpoDetails.mpo_details.vendor"
            :is-public="isPublic"
        ></mpo-header>
        <mpo-time-belts
            :day-numbers="mpoDetails.day_numbers"
            :time-belts="mpoDetails.time_belts"
        ></mpo-time-belts>
        <mpo-summary
            :time-belt-summary="mpoDetails.time_belt_summary"
            :total-budget="mpoDetails.total_budget"
            :net-total="mpoDetails.net_total"
            :mpo-details="mpoDetails.mpo_details"
        >
        </mpo-summary>
        <mpo-terms
            :media-buyer="mpoDetails.mpo_details.campaign.creator"
            :company="mpoDetails.company"
            :net-total-word="mpoDetails.net_total_word"
            :mpo="mpoDetails"
        ></mpo-terms>
        <mpo-action
            :campaign-id="mpoDetails.mpo_details.campaign_id"
            :mpo="mpoDetails"
            :is-public="isPublic"
        ></mpo-action>
    </div>
</template>
<script>
export default {
    props : {
        mpoDetails : Object,
        isPublic : Boolean
    }
}
</script>