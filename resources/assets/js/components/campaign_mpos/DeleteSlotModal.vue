<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="400px">
            <template v-slot:activator="{ on }">
                <v-icon color="red" dark v-on="on" right>delete</v-icon>
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
                    <v-btn color="primary" dark @click="dialog = false">Close</v-btn>
                    <v-btn color="error" dark 
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
        }
    },
    methods : {
        deleteSlots : function(){
            var msg = "Deleting adslots, please wait";
            this.sweet_alert(msg, 'info');
            axios({
                method: 'post',
                url: '/campaigns/mpo/details/'+this.adslot.mpo_id+'/adslots/delete',
                data: {
                  program: this.adslot.program,
                  duration: this.adslot.duration,
                  playout_date: this.adslot.playout_date
                }
            }).then((res) => {
                $('#load_this_div').css({opacity: 1});
                if (res.data.status === "error") {
                    this.sweet_alert(res.data.message, 'error');
                } else {
                    this.sweet_alert(res.data.message, 'success');
                    Event.$emit('updated-adslots', this.groupAdslotByProgram(res.data.data));
                    this.dialog = false;
                }
            }).catch((error) => {
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

