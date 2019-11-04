<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="600px">
            <template v-slot:activator="{ on }">
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <div v-on="on" class="d-inline-block position-icon">
                            <v-icon color="#01c4ca" dark left v-on="on" :disabled="!isCampaignOpen(campaign.status) || !isMpoPending()" 
                            @click="dialog = true">fa-sync</v-icon>
                        </div>
                    </template>
                    <span v-if="isCampaignOpen(campaign.status)">Send MPO for internal approval</span>
                    <span v-else>Action is disabled while campaign is {{ campaign.status.toLowerCase() }}</span>
                </v-tooltip>
            </template>
            <v-card>
                <v-card-title class="justify-center">
                    <span class="headline"> Request Approval</span>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-layout wrap>
                                <v-flex xs12 sm12 md12>
                                    <span>Choose the user to review MPO: </span>
                                    <v-select
                                        placeholder="Select user"
                                        v-model="user"
                                        :items="users"
                                        item-text="name"
                                        item-value="id"
                                        v-validate="'required'"
                                        name="user"
                                    ></v-select>
                                </v-flex>
                            </v-layout>
                        </v-form>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" class="default-vue-btn" dark @click="dialog = false">Close</v-btn>
                    <v-btn color="green" class="default-vue-btn" dark @click="makeRequest()">Proceed</v-btn>
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
            users : [],
            user:'',
        }
    },
    mounted() {
        this.getUserList()
    },
    methods : {
        getUserList : function() {
            axios({
                method: 'get',
                url: this.mpo.links.user_list
            }).then((res) => {
                this.users = res.data.data
            }).catch((error) => {
                console.log(error)
            });
        },
        makeRequest: function(event) {
            self = this;
            this.$validator.validateAll().then((result) => {
                if (result) {
                    self.requestReview();
                }
            });
        },
        requestReview : function () {
            axios({
                method: 'POST',
                url: this.mpo.links.request_approval,
                data: {user_id : this.user}
            }).then((res) => {
                Event.$emit('mpo-updated', res.data.data);
                this.dialog = false
                this.sweet_alert('Mpo successfully sent for approval', 'success');
            }).catch((error) => {
                this.sweet_alert(error.response.data.message, 'error');
            });
        },
        isMpoPending : function() {
            return this.mpo.status.toLowerCase() === 'pending'
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


