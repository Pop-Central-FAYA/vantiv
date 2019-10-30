<template>
    <v-layout wrap>
        <v-flex xs1 >
            <v-layout v-if="!isPublic">
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-btn color="#01c4ca" v-on="on" small dark left @click="back()"> Back</v-btn>
                    </template>
                    <span>Go back</span>
                </v-tooltip>
            </v-layout>
        </v-flex>
        <v-flex v-if="isPublic" xs9></v-flex>
        <v-flex v-else xs8></v-flex>
        <v-flex xs1 v-if="isPublic && mpoData.mpo_details.status.toLowerCase() != 'accepted' ">
            <accept-mpo
                :mpo="mpoData"
            ></accept-mpo>
        </v-flex>
        <v-flex xs1 v-if="!isPublic && hasPermission(mpoData.permissions, ['approve.mpo'])">
            <v-tooltip top>
                <template v-slot:activator="{ on }">
                    <v-btn color="#01c4ca" v-on="on" dark small right :disabled="!isMpoReview()" 
                    @click="changeStatus(mpoData.links.approve_mpo, 'approved')">Approve</v-btn>
                </template>
                <span>Appove Mpo</span>
            </v-tooltip>
        </v-flex>
        <v-flex xs1 v-if="!isPublic && hasPermission(mpoData.permissions, ['decline.mpo'])">
            <v-tooltip top>
                <template v-slot:activator="{ on }">
                    <v-btn color="red" v-on="on" :disabled="!isMpoReview()" dark small right 
                    @click="changeStatus(mpoData.links.decline_mpo, 'declined')">Decline</v-btn>
                </template>
                <span>Decline Mpo</span>
            </v-tooltip>
        </v-flex>
        <v-flex xs1>
            <v-tooltip top>
                <template v-slot:activator="{ on }">
                    <v-btn color="#01c4ca" v-on="on" dark small right @click="exportMpo()">Export</v-btn>
                </template>
                <span>Export MPO as PDF</span>
            </v-tooltip>
        </v-flex>
    </v-layout>
</template>
<script>
export default {
    props : {
        campaignId : String,
        mpo : Object,
        isPublic : Boolean
    },
    data() {
        return {
            mpoData : this.mpo 
        }
    },
    mounted() {
        console.log(this.mpo)
    },
    methods : {
        exportMpo : function() {
            window.location = this.mpo.links.export
        },
        back : function() {
            window.location = this.mpoData.links.campaign_details
        },
        changeStatus : function(url, action) {
            axios({
                method: 'POST',
                url: url,
                data: {}
            }).then((res) => {
                this.mpoData = res.data
                this.dialog = false
                this.sweet_alert('Mpo has been '+action, 'success');
            }).catch((error) => {
                this.sweet_alert(error.response.data.message, 'error');
            });
               
        },
        isMpoReview : function() {
            return this.mpoData.mpo_details.status.toLowerCase() === 'in review'
        }
    }
}
</script>