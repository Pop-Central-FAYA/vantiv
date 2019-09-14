<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="400px">
            <template v-slot:activator="{ on }">
                <v-icon color="red" dark v-on="on" right
                :disabled="selectedAdslots.length > 0"
                >delete</v-icon>
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
        selectedAdslots : Array
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
                        Event.$emit('updated-adslots-from-group', res.data.data.grouped_time_belts[this.index].time_belts)
                    }
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
</style>

