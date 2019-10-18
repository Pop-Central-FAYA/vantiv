<template>
        <v-dialog v-model="dialog" scrollable persistent max-width="600px" data-app>
        <template v-slot:activator="{ on }">
         </template>
         <v-card>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-subheader>Edit User</v-subheader>
                            <v-divider></v-divider>
                             <v-layout row wrap>
                                 <v-flex xs12 sm12 md12>
                                    <v-text-field required :label="'Email'" 
                                                :placeholder="'Email'" :hint="'Enter the email of the user'" 
                                                :single-line="true"
                                                solo
                                                v-validate="'required|email'"
                                                :error-messages="errors.collect('email')"
                                                v-model="user.email"
                                                data-vv-name="email"
                                                disabled>
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                             <v-layout row wrap>
                                <v-flex xs12 sm12 md12 text-left>
                                    <multiselect 
                                        v-model="user.role_name" 
                                        :options="roles" 
                                        :close-on-select="true" 
                                        :hide-selected="true" 
                                        :preserve-search="true" 
                                        placeholder="Pick role(s)" 
                                        label="label" 
                                        track-by="label" 
                                        solo
                                        :searchable="false" 
                                        :multiple="true" 
                                    >
                                  </multiselect>
                                </v-flex>
                            </v-layout>
                              <v-layout v-if="user.status != 'Unconfirmed'" row wrap>
                                 <v-flex xs12 sm6 md6 text-left>
                                    <v-switch v-model="status_switch" flat :label="`User status:  ${user.status}`"></v-switch>
                                </v-flex>
                            </v-layout>
                        </v-form>
                        <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" dark @click="closeDialog()">Close</v-btn>
                    <v-btn color="" class="default-vue-btn" dark @click="updateUser()">Update</v-btn>
                    </v-card-actions>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-dialog>
</template>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>

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
                user: {},
                status_switch: true,
                status:  ''

            };
        },
         created() {
            var self = this;
            Event.$on('edit-user', function(user) {
               self.openDialog(user);
            }); 

        },
        mounted() {
            console.log('edit user Component mounted.');
        },
        methods:{
            openDialog: function(item) {
                this.user = item;
                this.dialog = true;
                if(this.user.status =='Active'){
                    this.status_switch = true 
                }else{
                     this.status_switch = false 
                }
            },
            updateUser: async function(event) {
               if(this.hasPermissionAction(this.permissionList, ['update.user'])){
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
                    if(this.status_switch){
                        this.status ='Active'
                    }else{
                        this.status ='Inactive'
                    }
                    if(this.user.status != 'Unconfirmed'){
                       this.user.status =  this.status
                    }
                    this.sweet_alert('Updating User Information', 'info');
                    axios({
                        method: 'patch',
                        url: this.user.links.update,
                        data:  this.user
                    }).then((res) => {
                        this.dialog = false;
                        this.sweet_alert('User updated successfully', 'success');
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
            },
        }
    }
</script>