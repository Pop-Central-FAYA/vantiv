<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="800px">
            <template v-slot:activator="{ on }">
                <v-btn color="" class="default-vue-btn" dark v-on="on">Create Vendor</v-btn>
            </template>
            <v-card>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-subheader>Vendor Information</v-subheader>
                            <v-divider></v-divider>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :full-width="true" :label="'Vendor Name'" 
                                                :placeholder="'Vendor Name'" :hint="'Enter the name of your vendor'" 
                                                :solo="true" :single-line="true" 
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('name')"
                                                v-model="vendor.name"
                                                data-vv-name="name">
                                    </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :full-width="true" :label="'Street Address'" 
                                                :placeholder="'Street Address'" :hint="'Enter the street address of your vendor'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('street_address')"
                                                v-model="vendor.street_address"
                                                data-vv-name="street_address">
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :full-width="true" :label="'City'" 
                                                :placeholder="'City'" :hint="'Enter the city of your vendor'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('city')"
                                                v-model="vendor.city"
                                                data-vv-name="city">
                                    </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :full-width="true" :label="'State'" 
                                                :placeholder="'State'" :hint="'Enter the state of your vendor'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('state')"
                                                v-model="vendor.state"
                                                data-vv-name="state">
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-subheader>Primary Contact</v-subheader>
                            <v-divider></v-divider>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :full-width="true" :label="'First Name'" 
                                                :placeholder="'First Name'" :hint="'Enter the first name of your contact'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('first_name')"
                                                v-model="vendor.contacts[0].first_name"
                                                data-vv-name="first_name">
                                    </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :full-width="true" :label="'Last Name'" 
                                                :placeholder="'Last Name'" :hint="'Enter the last name of your contact'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('last_name')"
                                                v-model="vendor.contacts[0].last_name"
                                                data-vv-name="last_name">
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :full-width="true" :label="'Email'" 
                                                :placeholder="'Email'" :hint="'Enter the email of your contact'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|email'"
                                                :error-messages="errors.collect('email')"
                                                v-model="vendor.contacts[0].email"
                                                data-vv-name="email">
                                    </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :full-width="true" :label="'Phone Number'" 
                                                :placeholder="'Phone Number'" :hint="'Enter the phone number of your contact'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|numeric'"
                                                :error-messages="errors.collect('phone_number')"
                                                v-model="vendor.contacts[0].phone_number"
                                                data-vv-name="phone_number">
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-subheader>Publishers</v-subheader>
                            <v-divider></v-divider>
                            <ad-vendor-publisher-list :publishers="publishers"></ad-vendor-publisher-list>
                        </v-form>
                        <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" dark @click="closeDialog()">Close</v-btn>
                    <v-btn color="" class="default-vue-btn" dark @click="createVendor()">Save</v-btn>
                    </v-card-actions>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<style>
    .v-text-field {
        padding-top: 2px;
        margin-top: 0px;
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
            routes: Object,
            publishers: Array
        },
        data() {
            return {
                dialog: false,
                vendor: this.setupModel(),
                dictionary: {
                    custom: {
                        name: {
                            required: () => 'Vendor name cannot be empty',
                            max: 'The vendor name field may not be greater than 255 characters',
                        },
                        street_address: {
                            required: () => 'Vendor street address cannot be empty',
                            max: 'The street address field may not be greater than 255 characters'
                        },
                        city: {
                            required: () => 'Vendor city cannot be empty',
                            max: 'The city field may not be greater than 255 characters'
                        },
                        state: {
                            required: () => 'Vendor state cannot be empty',
                            max: 'The state field may not be greater than 255 characters'
                        },
                        first_name: {
                            required: () => 'First name of vendor contact cannot be empty',
                            max: () => 'The first name field may not be greater than 255 characters'
                        },
                        last_name: {
                            required: () => 'Last name of vendor contact cannot be empty',
                            max: () => 'The last name field may not be greater than 255 characters'
                        },
                        last_name: {
                            required: () => 'Email address of vendor contact cannot be empty',
                            email: () => 'This must be a valid email address'
                        },
                        phone_number: {
                            required: () => 'Phone number of vendor contact cannot be empty',
                            numeric: () => 'Phone number must be all digits'
                        }
                    }
                }
            };
        },
        created() {
            var self = this;
            Event.$on('vendor-publisher-updated', function(publisher_list) {
                self.vendor.publishers = publisher_list;
            });
        },
        mounted() {
            console.log('Create vendor component mounted.');
            this.$validator.localize('en', this.dictionary);
        },
        methods: {
            createVendor: function(event) {
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
                    url: this.routes.create,
                    data: this.vendor
                }).then((res) => {
                    Event.$emit('vendor-created', res.data.data);
                    this.sweet_alert('Vendor was successfully created', 'success');
                    this.closeDialog();
                }).catch((error) => {
                    this.sweet_alert('An unknown error has occurred, vendor cannot be created. Please try again', 'error');
                });
            },
            setupModel: function() {
                return {
                    name: '',
                    street_address: '',
                    city: '',
                    state: '',
                    country: 'Nigeria',
                    contacts: [
                        {
                            first_name: '',
                            last_name: '',
                            email: '',
                            phone_number: ''
                        }
                    ],
                    publishers: []
                }
            },
            closeDialog: function() {
                this.dialog = false;
                this.vendor = this.setupModel();
                Event.$emit('dialog-closed', []);
            }
        }
    }
</script>