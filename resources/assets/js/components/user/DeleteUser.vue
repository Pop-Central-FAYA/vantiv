<template>
        <v-dialog v-model="dialog" scrollable persistent max-width="600px" data-app>
        <template v-slot:activator="{ on }">
         </template>
         <v-card>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-subheader>Delete User</v-subheader>
                            <v-divider></v-divider>
                            <v-layout row wrap>
                                 <v-flex xs12 sm12 md12>
                                    <v-text-field required :clearable="true"  :label="'Email'" 
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
                        </v-form>
                        <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" dark @click="closeDialog()">Close</v-btn>
                    <v-btn color="" class="default-vue-btn" dark @click="deletUser()">Delete</v-btn>
                    </v-card-actions>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-dialog>
</template>
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

            };
        },
         created() {
            var self = this;
            Event.$on('delete-user', function(user) {
               self.openDialog(user);
            }); 
        },
        mounted() {
            console.log('reinvite user Component mounted.');
        },
        methods:{
            openDialog: function(item) {
                this.user = item;
                this.dialog = true;
            },
            deletUser: async function(event) {
                if(this.hasPermissionAction(this.permissionList, ['update.user'])){
                     this.sweet_alert('deleting user information', 'info');
                    axios({
                        method: 'delete',
                        url: this.user.links.delete,
                    }).then((res) => {
                        this.dialog = false;
                        Event.$emit('user-created', "success");
                        this.sweet_alert('User deleted successfully', 'success');
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