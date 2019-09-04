<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="600px">
            <template v-slot:activator="{ on }">
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-icon color="#01c4ca" dark left v-on="on" @click="dialog = true">fa fa-edit</v-icon>
                    </template>
                    <span>Edit campaign volume prices</span>
                </v-tooltip>
            </template>
            <v-card>
                <v-card-title>
                    <span class="headline"> {{ mpo.station }}</span>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-layout wrap>
                                <v-flex xs12 sm12 md12>
                                    <span>
                                        Volume Discount (%)
                                    </span>
                                    <v-text-field v-validate="'required'" 
                                    type="number" placeholder="Volume Discount" 
                                    name="volume_discount" v-model="volume_discount"></v-text-field>
                                    <span class="text-danger" v-show="errors.has('volume_discount')">{{ errors.first('volume_discount') }}</span>
                                </v-flex>
                            </v-layout>
                        </v-form>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" class="default-vue-btn" dark @click="dialog = false">Close</v-btn>
                    <v-btn color="green" class="default-vue-btn" dark @click="update()"> Update</v-btn>
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
        }
    },
    data() {
        return {
            dialog: false,
            volume_discount : '',
        }
    },
    methods : {
        update : async function() {
            let isValid = await this.$validator.validate().then(valid => {
                if (!valid) {
                    return false;
                } else {
                    return true;
                }
            });
            if (!isValid) {
                return false;
            }
            this.sweet_alert('Processing request, please wait...', 'info');
            axios({
                method: 'patch',
                url: `/mpos/${this.mpo.id}`,
                data: {
                    id : _.map(this.mpo.campaign_mpo_time_belts, 'id'),
                    volume_discount : this.volume_discount
                }
            }).then((res) => {
                if (res.data.status === 'success') {
                    this.sweet_alert(res.data.message, 'success');
                    Event.$emit('updated-adslots',this.filterMpo(
                        res.data.data.campaign_mpos, this.mpo.id
                    ).campaign_mpo_time_belts)
                    Event.$emit('updated-mpos', res.data.data.campaign_mpos)
                    Event.$emit('updated-campaign', res.data.data)
                    this.volume_discount = ''
                } else {
                    this.sweet_alert(res.data.message, 'error');
                }
            }).catch((error) => {
                console.log(error)
                this.sweet_alert(error.response.data.message, 'error');
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


