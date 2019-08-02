<template>
        <v-dialog v-model="dialog" persistent max-width="600px" data-app>
        <template v-slot:activator="{ on }">
               <button v-on="on" class="btn block_disp uppercased">Request Approval</button>
        </template>
        <v-card>
            <v-card-text class="px-2 pt-2 pb-0">
                <v-container grid-list-md class="pa-0">
                     <v-card-text>
                            <v-layout row wrap>
                                <v-flex xs12 sm12 md12 text-left>
                                    <span>Select user: </span>
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
                     </v-card-text>
                     <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="red" dark @click="dialog = false">Close</v-btn>
                            <v-btn color="" class="default-vue-btn" dark @click="processForm()">Request</v-btn>
                      </v-card-actions>
                </v-container>
            </v-card-text>
        </v-card>
        </v-dialog>
</template>

<style>
      .v-text-field .v-input__slot {
        padding: 0px 12px;
        min-height: 45px;
        margin-bottom: 0px;
        border: 1px solid #ccc;
        border-radius: 5px;
        /* box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12); */
    }
    .v-text-field>.v-input__control>.v-input__slot:after, .v-text-field>.v-input__control>.v-input__slot:before {
        content: none;
    }
  
</style>

<script>
    export default {
        props: {
        users:Array,
        mediaPlan:String,
        actionLink:String,
        permissionList:Array
        },
        data() {
            return {
                user:'',
                dialog: false
            };
        },
        mounted() {
            console.log('finance Component mounted.');
        },
        methods:{
            processForm: async function(event) {
                // Validate inputs using vee-validate plugin 
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
                axios({
                    method: 'post',
                    url: this.actionLink,
                    data: {
                        user_id: this.user,
                        media_plan_id: this.mediaPlan,
                    }
                }).then((res) => {
                if (res.data.status === 'success') {
                    this.dialog = false;
                    Event.$emit('updated-media-plan', res.data.data);
                    this.sweet_alert('Request sent successfully', 'success');
                } else {
                    this.sweet_alert('Something went wrong, Try again!', 'error');
                }
                }).catch((error) => {
                    this.sweet_alert(error.response.data.message, 'error');
                });
            },
        }
    }
</script>