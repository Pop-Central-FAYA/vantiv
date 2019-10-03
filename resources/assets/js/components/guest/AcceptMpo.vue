<template>
    <v-dialog v-model="dialog" persistent max-width="500px">
        <template v-slot:activator="{ on }">
            <v-btn class="default-vue-btn mx-0" small dark right v-on="on" @click="dialog = true">
                Accept
            </v-btn>
        </template>
        <v-card>
            <v-card-title>
                <span class="headline"> Accept Mpo
                </span>
            </v-card-title>
            <v-card-text>
                <v-container grid-list-md>
                    <v-form>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    First Name
                                </span>
                                <v-text-field v-validate="'required'" 
                                type="text" placeholder="First Name" 
                                name="first_name" v-model="model.first_name"></v-text-field>
                                <span class="text-danger" v-show="errors.has('first_name')">{{ errors.first('first_name') }}</span>
                            </v-flex>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Last Name
                                </span>
                                <v-text-field v-validate="'required'" 
                                type="text" placeholder="Last Name" 
                                name="last_name" v-model="model.last_name"></v-text-field>
                                <span class="text-danger" v-show="errors.has('last_name')">{{ errors.first('last_name') }}</span>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Email
                                </span>
                                <v-text-field v-validate="'required|email'" 
                                type="text" placeholder="Email" 
                                name="email" v-model="model.email"></v-text-field>
                                <span class="text-danger" v-show="errors.has('email')">{{ errors.first('email') }}</span>
                            </v-flex>
                            <v-flex xs12 sm12 md6>
                                <span>
                                    Phone Number
                                </span>
                                <v-text-field v-validate="'required'" 
                                type="text" placeholder="Phone Number" 
                                name="phone_number" v-model="model.phone_number"></v-text-field>
                                <span class="text-danger" v-show="errors.has('phone_number')">{{ errors.first('phone_number') }}</span>
                            </v-flex>
                        </v-layout>
                    </v-form>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="red" class="default-vue-btn" dark @click="dialog = false">Close</v-btn>
                <v-btn class="default-vue-btn" dark @click="acceptMpo()">Accept</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    props : {
        mpo : Object
    },
    data () {
        return {
            model : this.setupModel(),
            dialog : false,
            dictionary: {
                    custom: {
                        first_name: {
                            required: () => 'First name cannot be empty',
                            max: () => 'The first name field may not be greater than 255 characters'
                        },
                        last_name: {
                            required: () => 'Last name cannot be empty',
                            max: () => 'The last name field may not be greater than 255 characters'
                        },
                        last_name: {
                            required: () => 'Phone Number cannot be empty',
                            max: () => 'The last name field may not be greater than 255 characters'
                        },
                        email: {
                            required: () => 'Email cannot be empty',
                            email: () => 'Email address must be valid',
                            max: () => 'The last name field may not be greater than 255 characters'
                        }
                    }
                }
        }
    },
    mounted() {
        this.$validator.localize('en', this.dictionary);
    },
    methods : {
        acceptMpo : function() {
            self = this;
            this.$validator.validateAll().then((result) => {
                if (result) {
                    self.makeCreateRequest();
                }
            });
        },
        makeCreateRequest: function() {
            axios({
                method: 'post',
                url: this.mpo.links.accept,
                data: this.model
            }).then((res) => {
                this.sweet_alert('Mpo was successfully accepted', 'success');
                this.closeDialog();
            }).catch((error) => {
                if (error.response && (error.response.status == 422)) {
                    this.displayServerValidationErrors(error.response.data.errors, true);
                } else {
                    this.sweet_alert('An unknown error has occurred, vendor cannot be created. Please try again', 'error');
                }
            });
        },
        setupModel: function() {
            return {
                first_name: '',
                last_name: '',
                email: '',
                phone_number : '',
                mpo_id: this.mpo.mpo_details.id,
            }
        },
        closeDialog: function() {
            this.dialog = false;
            this.form = this.setupModel();
        }
    }
}
</script>