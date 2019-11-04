<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="600px">
            <template v-slot:activator="{ on }">
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <div v-on="on" class="d-inline-block position-icon">
                            <v-icon color="#01c4ca" dark left v-on="on" :disabled="!isCampaignOpen(campaign.status) || !isMpoOpen()" 
                            @click="dialog = true">fa-paper-plane</v-icon>
                        </div>
                    </template>
                    <span v-if="isCampaignOpen(campaign.status) && !isMpoOpen()">Mpo requires internal approval and cannot be submitted to vendor/publishers</span>
                    <span v-else-if="isCampaignOpen(campaign.status)">Submit to vendor</span>
                    <span v-else>Action is disabled while campaign is {{ campaign.status.toLowerCase() }}</span>
                </v-tooltip>
            </template>
            <v-card>
                <v-card-title class="justify-center">
                    <span class="headline"> Submit MPO</span>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-layout wrap>
                                <v-flex xs12 sm12 md12>
                                    <p>You are about to submit your mpo to <b>{{ mpo.vendor }}</b> , click submit to continue</p>
                                </v-flex>
                            </v-layout>
                        </v-form>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" class="default-vue-btn" dark @click="dialog = false">Close</v-btn>
                    <v-btn color="green" class="default-vue-btn" dark @click="submitToVendor()"> 
                        <span v-if="status === 'accepted'">Re Submit</span>
                        <span v-else > Submit</span>  
                    </v-btn>
                </v-card-actions>
            </v-card>
            
        </v-dialog>
    </v-layout>
</template>
<script>
export default {
    props : {
        mpo: {
            required : true,
            type : Object
        },
        campaign : Object
    },
    data() {
        return {
            dialog: false,
            shareLink : {},
            status : this.mpo.status.toLowerCase()
        }
    },
    mounted() {
        this.getShareLink()
        var self = this
        Event.$on('update-share-link', function() {
            self.getShareLink()
        })
    },
    methods : {
        getShareLink : function() {
            axios({
                method: 'get',
                url: this.mpo.links.active_share_link
            }).then((res) => {
                let result = res.data.data;
                if (result != null) {
                    this.shareLink = result;                
                }
            }).catch((error) => {
                this.sweet_alert('An unknown error has occurred, link cannot be retrieved. Please try again', 'error');
            });
        },
        submitToVendor : async function () {
            var msg = "Processing request, please wait...";
            this.sweet_alert(msg, 'info');
            if(this.shareLink.is_expired || Object.keys(this.shareLink).length === 0){
                this.addShareLink()
            }else{
                this.submit(this.shareLink.url)
            }
        },
        addShareLink : function () {
            axios({
                method: 'POST',
                url: this.mpo.links.store_share_links,
                data: {}
            }).then((res) => {
                Event.$emit('share-link', res.data.data)
                this.submit(res.data.data.short_url)
            }).catch((error) => {
                this.sweet_alert(error.response.data.message, 'error');
            });
        },
        submit : function(url) {
            axios({
                method: 'POST',
                url: this.mpo.links.submit_to_vendor,
                data: {url : url, email : this.mpo.email}
            }).then((res) => {
                this.sweet_alert('Mpo Submitted to vendor successfully', 'success');
                this.dialog = false
            }).catch((error) => {
                this.sweet_alert(error.response.data.message, 'error');
            });
        },
        isMpoOpen : function() {
            return this.mpo.status.toLowerCase() === 'approved'
        }
    }
}
</script>
<style>
    .modal {
    overflow-y: auto;
    }

    .modal-open {
    overflow: auto;
    }
    .position-icon {
        padding-top: 12px;
    }
</style>


