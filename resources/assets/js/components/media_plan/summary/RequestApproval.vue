<template>
        <v-dialog v-model="dialog" persistent max-width="600px" data-app>
        <template v-slot:activator="{ on }">
               <button v-on="on" class="btn block_disp uppercased">Request Approval</button>
        </template>
        <v-card>
            <v-card-text class="px-2 pt-2 pb-0">
                <v-container grid-list-md class="pa-0">
                     <v-card-text>
                        <v-form>
                            <v-layout wrap>
                                <v-flex xs12 sm12 md12>
                                    
                                    <v-select
                                        label="Select Finance Admin"
                                        v-model="user"
                                        :items="users"
                                        item-text="name"
                                        item-value="id"
                                        v-validate="'required'"
                                        name="user"
                                        solo
                                    ></v-select>
                                </v-flex>
                            </v-layout>
                        </v-form>
                     </v-card-text>
                     <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="red" dark @click="dialog = false">Close</v-btn>
                            <v-btn color="" class="default-vue-btn" dark @click="process_form()">Request</v-btn>
                      </v-card-actions>
                </v-container>
            </v-card-text>
        </v-card>
        </v-dialog>
</template>

<style>
    .v-text-field {
        padding-top: 2px;
        margin-top: 0px;
    }
</style>

<script>
    export default {
        props: {
        users:Array,
        mediaPlan:String,
        actionLink:String
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
            process_form: async function(event) {
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
                    console.log(res.data);
                    if (res.data.status === 'success') {
                        this.dialog = false;
                        Event.$emit('updated-mediaPlan', res.data.data);
                        this.sweet_alert('Request sent successfully', 'success');
                    } else {
                        this.sweet_alert('Something went wrong, Try again!', 'error');
                    }
                    }).catch((error) => {
                     console.log(error.response.data);
                     this.sweet_alert(error.response.data.message, 'error');
                  });
            
            },
        }
    }
</script>