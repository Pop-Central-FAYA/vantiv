<template>
        <v-dialog v-model="dialog" scrollable persistent max-width="600px" data-app>
        <template v-slot:activator="{ on }">
         </template>
         <v-card>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-subheader>Resend Invitation</v-subheader>
                            <v-divider></v-divider>
                            <v-layout wrap>
                                <v-flex xs12 sm3 md3>
                                     <p class="weight_medium">
                                      {{ user.email }}
                                     </p>
                                  </v-flex>
                                      </v-layout>
                        </v-form>
                        <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" dark @click="closeDialog()">Close</v-btn>
                    <v-btn color="" class="default-vue-btn" dark @click="updateUser()">Resend</v-btn>
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
                user: this.setupModel(),

            };
        },
         created() {
            var self = this;
            Event.$on('reinvite-user', function(user) {
               self.openDialog(user);
            }); 
        },
        mounted() {
            console.log('reinvite user Component mounted.');
        },
        methods:{
            setupModel: function() {
                return {
                            role_name: [],
                            email: '',
                            name: '',
                            id: ''
                         }  
             },
            openDialog: function(item) {
                this.user = item;
                this.dialog = true;
            },
            updateUser: async function(event) {
                if(this.hasPermissionAction(this.permissionList, ['update.user'])){
                     this.sweet_alert('Saving user information', 'info');
                    axios({
                        method: 'get',
                        url: '/users/invite/'+this.user.id,
                    }).then((res) => {
                        this.dialog = false;
                        Event.$emit('user-created', res.data.data);
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