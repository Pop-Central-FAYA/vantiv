<template>
    <v-dialog v-model="editDialog" persistent max-width="500px">
        <v-card>
            <v-card-title>
                <span class="headline"> Edit : {{ adslot.program }} 
                                            for {{ adslot.duration }} 
                                            Seconds duration on 
                                            {{ adslot.playout_date }}
                                            </span>
            </v-card-title>
            
            <v-card-text>
                <v-container grid-list-md>
                    <v-form>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md12>
                                <span>
                                    Program
                                </span>
                                <input type="text" required v-validate="'required'" name="progeam" v-model="adslot.program" class="form-control">
                                <span class="text-danger" v-show="errors.has('progeam')">{{ errors.first('progeam') }}</span>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md12>
                                <span>
                                    Date
                                </span>
                                <input type="date" required v-validate="'required'" name="date" v-model="adslot.playout_date" class="form-control">
                                <span class="text-danger" v-show="errors.has('date')">{{ errors.first('date') }}</span>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Unit Price
                                </span>
                                <input type="number" required v-validate="'required|min:1'" name="unit_price" v-model="adslot.unit_rate" class="form-control">
                                <span class="text-danger" v-show="errors.has('unit_price')">{{ errors.first('unit_price') }}</span>
                            </v-flex>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Total Insertions
                                </span>
                                <input type="number" required disabled name="exposures" v-model="newTotalInsertions" class="form-control">
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md12>
                                <span>
                                    Media Asset
                                </span>
                                <select name="media_asset" required v-validate="'required'" v-model="adslot.asset_id">
                                    <option value="">Select Media Asset</option>
                                    <option :value="asset.id" v-for="asset in filterAssetByDuration(assets, adslot.duration)" :key="asset.id">
                                        {{ asset.file_name }}
                                    </option>
                                </select>
                                <span class="text-danger" v-show="errors.has('media_asset')">{{ errors.first('media_asset') }}</span>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap v-for="(input, index) in selected_time_belts" :key="index">
                            <v-flex xs12 sm12 md5>
                                <span v-if="index === 0">
                                    Time Belt
                                </span>
                                <select name="time_belt" required v-validate="'required'" v-model="selected_time_belts[index].time_belt">
                                    <option :value="slot.start_time" v-for="(slot, key) in time_belts" :key="key">
                                        {{ slot.start_time }}
                                    </option>
                                </select>
                                <span class="text-danger" v-show="errors.has('time_belt')">{{ errors.first('time_belt') }}</span>
                            </v-flex>

                            <v-flex xs12 sm12 md5>
                                <span v-if="index === 0">
                                    Insertion
                                </span>
                                <input type="number" required name="insertion" v-validate="'required|min:1'" class="form-control" v-model="inputed_insertions[index].insertion">
                                <span class="text-danger" v-show="errors.has('insertion')">{{ errors.first('insertion') }}</span>
                            </v-flex>

                            <v-flex xs12 sm12 md2>
                                <span v-if="index == 0">
                                    Action
                                </span>
                                <v-icon v-if="index > 0" color="red" dark right @click="deleteRow(index)">delete</v-icon>
                            </v-flex>
                        </v-layout>
                        <v-layout>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Volume Discount
                                </span>
                                <input type="number" required name="volume_discount" v-validate="'required|min:1'" v-model="adslot.volume_discount" class="form-control">
                                <span class="text-danger" v-show="errors.has('volume_discount')">{{ errors.first('volume_discount') }}</span>
                            </v-flex>
                            <v-flex md3></v-flex>
                            <v-flex xs12 sm12 md3>
                                <v-spacer></v-spacer>
                                <v-spacer></v-spacer>
                                <v-btn color="success" class="default-vue-btn" dark right small @click="addRow()">Add Time belt</v-btn>
                            </v-flex>
                        </v-layout>
                    </v-form>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" class="default-vue-btn" dark @click="editDialog = false">Close</v-btn>
                <v-btn color="success" class="default-vue-btn" dark @click="updateSlot()">Update</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    props : {
        adslot : {
            require : true,
            type : Object
        },
        assets : {
            required : true,
            type : Array
        },
        time_belts : {
            required : true,
            type : Array
        }
    },
    data () {
        return {
            editDialog : false,
            selected_time_belts : [],
            inputed_insertions : []
        }
    },
    created () {
        var self = this
        Event.$on('edit-dialog-modal', function(modal) {
            self.editDialog = modal
        })
    },
    mounted () {
        this.selected_time_belts.push({
            time_belt : '00:00'
        })
        this.inputed_insertions.push({
            insertion : ''
        })
    },
    computed : {
        newTotalInsertions : function (){
            let new_insertion = this.inputed_insertions.reduce((prev, cur) => prev + parseInt(cur.insertion), 0);
            return !isNaN(new_insertion) ? new_insertion : this.adslot.ad_slots
        }
    },
    methods : {
        updateSlot : async function(event) {
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

            var msg = "Processing request, please wait...";
            this.sweet_alert(msg, 'info');

            axios({
                method: 'POST',
                url: '/campaigns/mpo/details/'+this.adslot.mpo_id+'/adslots/update',
                data: {
                    id : this.adslot.id,
                    program : this.adslot.program,
                    playout_date : this.adslot.playout_date,
                    asset_id : this.adslot.asset_id,
                    unit_rate : this.adslot.unit_rate,
                    time_belts : this.selected_time_belts,
                    insertions: this.inputed_insertions,
                    volume_discount : this.adslot.volume_discount
                }
            }).then((res) => {
                if (res.data.status === 'success') {
                    this.sweet_alert(res.data.message, 'success');
                    Event.$emit('updated-adslots', this.groupAdslotByProgram(res.data.data))
                    this.editDialog = false;
                } else {
                    this.sweet_alert(res.data.message, 'error');
                    this.isHidden = true
                }
            }).catch((error) => {
                this.sweet_alert(error.response.data.message, 'error');
                this.isHidden = true
            });
        },
        filterAssetByDuration : function(assets, duration) {
            return assets.filter(item => item.duration === duration);
        },
        addRow : function() {
            this.selected_time_belts.push({
            time_belt : '00:00'
            })
            this.inputed_insertions.push({
                insertion : ''
            })
        },
        deleteRow : function(index) {
            this.selected_time_belts.splice(index,1)
            this.inputed_insertions.splice(index,1)
        }
    }
}
</script>

