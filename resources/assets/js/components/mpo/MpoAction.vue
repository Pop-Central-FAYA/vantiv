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
        <v-flex xs10 v-else></v-flex>
        <v-flex xs1 v-if="isPublic && mpo.mpo_details.status.toLowerCase() != 'accepted' ">
            <accept-mpo
                :mpo="mpo"
            ></accept-mpo>
        </v-flex>
        <v-flex xs1>
            <v-tooltip top>
                <template v-slot:activator="{ on }">
                    <v-btn color="#01c4ca" v-on="on" dark small right @click="exportMpo()">Export</v-btn>
                </template>
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
    mounted() {
        console.log(this.mpo)
    },
    methods : {
        exportMpo : function() {
            var msg = "Generating Excel Document, Please wait";
            this.sweet_alert(msg, 'info');
            window.location = this.mpo.links.export
        },
        back : function() {
            window.location = this.mpo.links.campaign_details
        }
    }
}
</script>