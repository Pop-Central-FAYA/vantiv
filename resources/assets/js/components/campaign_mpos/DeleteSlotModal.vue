<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="400px">
            <template v-slot:activator="{ on }">
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <div v-on="on" class="d-inline-block position-icon">
                            <v-icon color="red" dark v-on="on" @click="dialog = true" right
                                :disabled="selectedAdslots.length > 0 || !isCampaignOpen(campaign.status)"
                            >delete</v-icon>
                        </div>
                    </template>
                    <span v-if="isCampaignOpen(campaign.status)">Delete adslot</span>
                    <span v-else>You cant perform this action while campaign is {{ campaign.status.toLowerCase() }}</span>
                </v-tooltip>
            </template>
            <v-card>
                <v-card-title>
                    <span class="headline"> Delete {{ adslot.program }} for {{ adslot.duration }} Seconds on {{ adslot.playout_date }}</span>
                </v-card-title>

                <v-card-text>
                    <v-container grid-list-md>
                        <v-card-text>Are you sure you want to continue this operation ?</v-card-text>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn class="default-vue-btn" dark @click="dialog = false">Close</v-btn>
                    <v-btn color="red" dark 
                    @click="deleteSlots()"
                    >Proceed</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
export default {
    data() {
        return {
            dialog: false,
            updatedAdslots : []
        }
    },
    props : {
        adslot : {
            required : true,
            type : Object
        },
        group : String,
        index : Number,
        selectedAdslots : Array,
        campaign : Object
    },
    methods : {
        deleteSlots : function(){
            var msg = "Deleting adslots, please wait";
            this.sweet_alert(msg, 'info');
            axios({
                method: 'delete',
                url: `/campaigns/${this.adslot.campaign_id}/adslots/${this.adslot.id}`,
                data: {
                    group : this.group
                }
            }).then((res) => {
                $('#load_this_div').css({opacity: 1});
                if (res.data.status === "success") {
                    this.sweet_alert(res.data.message, 'success');
                    Event.$emit('updated', true)
                    Event.$emit('updated-campaign', res.data.data)
                    if(this.group){
                        console.log(this.index)
                        Event.$emit('updated-adslots-from-group', res.data.data.grouped_time_belts[this.index].time_belts)
                    }
                    Event.$emit('updated-group-adslots', res.data.data.grouped_time_belts)
                    Event.$emit('updated-adslots', res.data.data.time_belts)
                    this.dialog = false;
                } else {
                    this.sweet_alert(res.data.message, 'error');
                }
            }).catch((error) => {
                console.log(error)
                this.sweet_alert('An unknown error has occurred, media assets cannot be delete. Please try again', 'error');
            });
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
    .theme--dark.v-icon.v-icon--disabled {
        color: grey !important;
    }
    .position-icon {
        padding-top: 12px;
    }
</style>

