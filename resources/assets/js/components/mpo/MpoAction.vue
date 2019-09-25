<template>
    <v-layout wrap>
        <v-flex xs1>
            <v-layout>
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-btn color="#01c4ca" v-on="on" dark left @click="back()"> Back</v-btn>
                    </template>
                    <span>Go back</span>
                </v-tooltip>
            </v-layout>
        </v-flex>
        <v-flex xs10>

        </v-flex>
        <v-flex xs1>
            <v-layout>
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-btn color="#01c4ca" v-on="on" dark right @click="exportMpo()">Export</v-btn>
                    </template>
                    <span>Export Mpo</span>
                </v-tooltip>
            </v-layout>
        </v-flex>
    </v-layout>
</template>
<script>
export default {
    props : {
        mpoId : String,
        campaignId : String
    },
    methods : {
        exportMpo : function() {
            var msg = "Generating Excel Document, Please wait";
            this.sweet_alert(msg, 'info');
            window.location = `/mpos/${this.mpoId}/export`
        },
        back : function() {
            window.location = `/campaigns/details/${this.campaignId}`
        }
    }
}
</script>