<template>
        <v-dialog v-model="dialog" scrollable persistent max-width="600px" data-app>
        <template v-slot:activator="{ on }">
               <button v-on="on" class="btn block_disp uppercased">Invite User</button>
        </template>
         <v-card>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-subheader>User Information</v-subheader>
                            <v-divider></v-divider>
                            <v-layout wrap>
                                <v-flex xs12 sm12 md12>
                                    <v-text-field required :clearable="true"  :label="'Email'" 
                                                :placeholder="'Email'" :hint="'Enter the email of the user'" 
                                                :single-line="true"
                                                v-validate="'required|email'"
                                                 solo
                                                :error-messages="errors.collect('email')"
                                                v-model="user.email"
                                                data-vv-name="email">
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                             <v-layout row wrap>
                                <v-flex xs12 sm12 md12 text-left>
                                    <multiselect 
                                        v-model="user.roles" 
                                        :options="roles" 
                                        :close-on-select="true" 
                                        :hide-selected="true" 
                                        :preserve-search="true" 
                                        placeholder="Pick role(s)" 
                                        label="label" 
                                        track-by="label" 
                                        :searchable="false" 
                                        :multiple="true" 
                                    >
                                  </multiselect>
                                </v-flex>
                            </v-layout>
                        </v-form>
                        <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" dark @click="closeDialog()">Close</v-btn>
                    <v-btn color="" class="default-vue-btn" dark @click=" inviteUser()">Invite</v-btn>
                    </v-card-actions>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-dialog>
</template>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>
      .v-text-field .v-input__slot {
        padding: 0px 12px;
        min-height: 45px;
        margin-bottom: 0px;
        border-radius: 5px;
        /* box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12); */
    }
    .v-text-field>.v-input__control>.v-input__slot:after, .v-text-field>.v-input__control>.v-input__slot:before {
        content: none;
    }
      .multiselect__tags {
        min-height: 45px;
        border: 1px solid #ccc;
        margin-top: 16px;
    }
  
</style>

<script>
    export default {
        props: {
        roles:Array,
        permissionList:Array
        },
        data() {
            return {
                role:'',
                dialog: false,
                 user: {
                   roles: [],
                   email: '',
                   },
                  

            };
        },
        mounted() {
            console.log('finance Component mounted.');
        },
        methods:{
            inviteUser: async function(event) {
                if(this.hasPermissionAction(this.permissionList, ['create.user'])){
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
                     this.sweet_alert('Saving user information and sending invite', 'info');
                    axios({
                        method: 'post',
                        url: '/users/invite',
                        data:  this.user
                    }).then((res) => {
                    if (res.data.status === 'success') {
                        this.dialog = false;
                        Event.$emit('user-created', res.data.data);
                        this.sweet_alert('Request sent successfully', 'success');
                    } else {
                        this.sweet_alert('Something went wrong, Try again!', 'error');
                    }
                    }).catch((error) => {
                        if (error.response && (error.response.status == 422)) {
                            this.displayServerValidationErrors(error.response.data.errors);
                        } else {
                            this.sweet_alert('An unknown error has occurred, vendor cannot be created. Please try again', 'error');
                        }
                    });

                }
            },
             closeDialog: function() {
                this.dialog = false;
            },
             setRole() {
             this.role_list[0] = this.roles;
              },
        }
    }
</script>