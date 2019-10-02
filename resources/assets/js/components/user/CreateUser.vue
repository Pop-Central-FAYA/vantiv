<template>
        <v-dialog v-model="dialog" scrollable persistent max-width="600px" data-app>
        <template v-slot:activator="{ on }">
               <button v-on="on" class="btn block_disp uppercased">Invite User</button>
        </template>
         <v-card>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-subheader>Create New User</v-subheader>
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
                                        v-validate="'required'"
                                        data-vv-name="roles"
                                    >
                                  </multiselect>
                                </v-flex>
                            </v-layout>
                        </v-form>
                    <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" dark @click="closeDialog()">Close</v-btn>
                    <v-btn color="" class="default-vue-btn" dark @click="inviteUser()">Invite</v-btn>
                    </v-card-actions>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-dialog>
</template>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>
      .multiselect__tags {
        min-height: 45px;
        border: 1px solid #ccc;
        margin-top: 16px;
    }
     .default-vue-btn {
        color: #fff;
        cursor: pointer;
        background: #44C1C9 !important;
        -webkit-appearance: none;
        font-family: "Roboto", sans-serif;
        font-weight: 500;
        border: 0;
        font-size: 15px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
        -webkit-box-shadow: 9px 10px 20px 1px rgba(0,159,160,0.21);
        -moz-box-shadow: 9px 10px 20px 1px rgba(0,159,160,0.21);
        box-shadow: 9px 10px 20px 1px rgba(0,159,160,0.21);
        position: relative;
        display: inline-block;
        text-transform: uppercase;
    }
  
</style>

<script>
    export default {
        props: {
        roles:Array,
        permissionList:Array,
        routes:Object,
        },
        data() {
            return {
                role:'',
                dialog: false,
                user: this.setupModel(),
            };
        },
        mounted() {
            console.log('Add user Component mounted.');
        },
        methods:{
             setupModel: function() {
                return {
                       roles: [],
                       email: '',
                         }  
             },
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
                        url: this.routes.create,
                        data:  this.user
                    }).then((res) => {
                         this.sweet_alert('User Invited Successfully', 'success');
                        this.dialog = false;
                        Event.$emit('user-created', res.data.data);
                        this.setupModel()
                    }).catch((error) => {
                        if (error.response && (error.response.status == 422)) {
                            this.displayServerValidationErrors(error.response.data.errors);
                        } else {
                            this.sweet_alert('An unknown error has occurred. Please try again', 'error');
                        }
                    });

                }
            },
             closeDialog: function() {
                this.dialog = false;
            }
        }
    }
</script>