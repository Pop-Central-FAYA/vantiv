<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="800px">
            <template v-slot:activator="{ on }">
                <v-btn color="" class="default-vue-btn" dark v-on="on">Create Client</v-btn>
            </template>
            <v-card>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-subheader>Client Information</v-subheader>
                            <v-divider></v-divider>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :label="'Client Name'" 
                                                :placeholder="'Client Name'" :hint="'Enter the name of your Client'" 
                                                :solo="true" :single-line="true" 
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('name')"
                                                v-model="client.name"
                                                data-vv-name="name">
                                    </v-text-field>
                                </v-flex>
                             
                                 <v-flex xs12 sm5 md5>
                                   <v-text-field solo v-model="client_logo_input_label" prepend-icon="attach_file" name="certificate"  :error-messages="errors.collect('client_image_url')"  @click="chooseFile('client_logo')"></v-text-field>
                                   <input type="file" style="display: none" ref="client_logo" accept=".png,.jpeg,.jpg" v-validate="'required|ext:jpg,png,jpeg'"  data-vv-name="client_image_url" @change="onFileChange($event, 'client_logo')">
                                 </v-flex>
                                 <v-flex xs12 sm1 md1>
                                    <b-img thumbnail fluid v-show="show_client_logo" :src="logo"  style="width: 50px; height: 50px" id="client_logo_one" alt="Image 1"></b-img>
                                  </v-flex>

                            </v-layout>
                            <v-layout wrap>
                                <v-flex xs12 sm3 md3>
                                    <v-text-field required :clearable="true" :label="'City'" 
                                                :placeholder="'City'" :hint="'Enter the city of your client'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('city')"
                                                v-model="client.city"
                                                data-vv-name="city">
                                    </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm3 md3>
                                    <v-text-field required :clearable="true" :label="'State'" 
                                                :placeholder="'State'" :hint="'Enter the state of your client'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('state')"
                                                v-model="client.state"
                                                data-vv-name="state">
                                    </v-text-field>
                                </v-flex>
                                   <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :label="'Street Address'" 
                                                :placeholder="'Street Address'" :hint="'Enter the street address of your client'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('street_address')"
                                                v-model="client.street_address"
                                                data-vv-name="street_address">
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-subheader>Primary Contact</v-subheader>
                            <v-divider></v-divider>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :label="'First Name'" 
                                                :placeholder="'First Name'" :hint="'Enter the first name of your contact'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('first_name')"
                                                v-model="client.contacts[0].first_name"
                                                data-vv-name="first_name">
                                    </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :label="'Last Name'" 
                                                :placeholder="'Last Name'" :hint="'Enter the last name of your contact'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('last_name')"
                                                v-model="client.contacts[0].last_name"
                                                data-vv-name="last_name">
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true"  :label="'Email'" 
                                                :placeholder="'Email'" :hint="'Enter the email of your contact'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|email'"
                                                :error-messages="errors.collect('email')"
                                                v-model="client.contacts[0].email"
                                                data-vv-name="email">
                                    </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :label="'Phone Number'" 
                                                :placeholder="'Phone Number'" :hint="'Enter the phone number of your contact'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|numeric'"
                                                :error-messages="errors.collect('phone_number')"
                                                v-model="client.contacts[0].phone_number"
                                                data-vv-name="phone_number">
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-subheader>Brand Information</v-subheader>
                            <v-divider></v-divider>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :label="'Name'" 
                                                :placeholder="'Brand Name'" :hint="'Enter the name of your brand'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('brand_name')"
                                                v-model="client.brands[0].name"
                                                data-vv-name="brand_name">
                                    </v-text-field>
                                </v-flex>
                                  <v-flex xs12 sm5 md5>
                                    <v-text-field solo v-model="brand_logo_input_label" prepend-icon="attach_file" name="certificate" :error-messages="errors.collect('brand_image_url')"  @click="chooseFile('brand_logo')"></v-text-field>
                                    <input type="file" style="display: none" ref="brand_logo" accept=".png,.jpeg,.jpg"  v-validate="'required|ext:jpg,png,jpeg'"  data-vv-name="brand_image_url"  :error-messages="errors.collect('brand_image_url')" @change="onFileChange($event, 'brand_logo')">
                                   
                                </v-flex>
                                  <v-flex xs12 sm1 md1>
                                            <b-img thumbnail fluid v-show="show_brand_logo" :src="logo"  style="width: 50px; height: 50px"  id="brand_logo" alt="Image 1"></b-img>

                                </v-flex>
                            </v-layout>
                              <v-layout wrap>
                                <v-flex xs12 sm12 md12 v-show="show_progress_bar">
                                    <p class="text-muted">{{ current_upload_title }}</p>
                                    <v-progress-linear v-model="upload_percentage" color="green"></v-progress-linear>
                                </v-flex>
                              </v-layout>
                        </v-form>
                        <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" dark @click="closeDialog()">Close</v-btn>
                    <v-btn color="" class="default-vue-btn" dark @click=" createClient()">Save</v-btn>
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
        data() {
            return {
                dialog: false,
                client: this.setupModel(),
                logo:'',
                show_brand_logo: false,
                show_client_logo: false,
                client_logo_input_label: 'Choose client logo',
                brand_logo_input_label: 'Choose brand logo',
                client_logo_file: '',
                brand_logo_file: '',
                s3_presigned_url: '',
                show_progress_bar: false,
                upload_percentage: 0,
                current_upload_title: '',
                client_logo_url: '',
                brand_logo_url: '',

                dictionary: {
                    custom: {
                        name: {
                            required: () => 'Client name cannot be empty',
                            max: 'The client name field may not be greater than 255 characters',
                        },
                        street_address: {
                            required: () => 'Client street address cannot be empty',
                            max: () => 'The street address field may not be greater than 255 characters'
                        },
                        city: {
                            required: () => 'Client city cannot be empty',
                            max: () => 'The city field may not be greater than 255 characters'
                        },
                        state: {
                            required: () => 'Client state cannot be empty',
                            max: 'The state field may not be greater than 255 characters'
                        },
                        first_name: {
                            required: () => 'First name of client contact cannot be empty',
                            max: () => 'The first name field may not be greater than 255 characters'
                        },
                        last_name: {
                            required: () => 'Last name of client contact cannot be empty',
                            max: () => 'The last name field may not be greater than 255 characters'
                        },
                        email: {
                            required: () => 'Email address of client contact cannot be empty',
                            email: () => 'This must be a valid email address'
                        },
                        phone_number: {
                            required: () => 'Phone number of client contact cannot be empty',
                            numeric: () => 'Phone number must be all digits'
                        },
                        brand_name: {
                            required: () => 'Brand name cannot be empty',
                            max: () => 'The brand name field may not be greater than 255 characters'
                        },
                        client_image_url: {
                            required: () => 'Client logo cannot be empty',
                            numeric: () => 'The brand logo must be a picture'
                        },
                        brand_image_url: {
                            required: () => 'Brand logo cannot be empty',
                            numeric: () => 'The brand logo must be a picture'
                        }
                    }
                }
            };
        },
        mounted() {
            console.log('Create client Component mounted.');
            this.$validator.localize('en', this.dictionary);
        },
        methods: {
             createClient:async function(event) {

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

               try {
                    // generate presigned url for ciient logo upload
                       await this.generatePresignedUrl(this.client_logo_file, 'client-images/');
                       await this.uploadFile(this.client_logo_file, this.s3_presigned_url, 'client_logo');
                       console.log(this.client_logo_url);
                    // generate presigned url for brand logo upload
                    if(this.brand_logo_file) {
                          await this.generatePresignedUrl(this.brand_logo_file, 'brand-images/');
                          await this.uploadFile(this.brand_logo_file, this.s3_presigned_url, 'brand_logo');
                      
                    }
                    // make axios call to store created asset to db
                       await this. storeRequest();
                   
                } catch (error) {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, asset upload failed. Please try again', 'error');
                }
            },
             storeRequest: function() {
                 this.show_progress_bar = false;
                  this.sweet_alert('Saving client information', 'info');
                axios({
                    method: 'post',
                    url: '/clients',
                    data: this.client
                }).then((res) => {
                    console.log(res.data.data);
                     Event.$emit('client-created', res.data.data);
                    this.sweet_alert('Client was successfully created', 'success');
                    this.client = this.setupModel();
                    this.show_client_logo = false;
                     this.show_brand_logo = false;
                    this.client_logo_input_label= 'Choose client logo';
                    this.brand_logo_input_label = 'Choose brand logo';
                    this.closeDialog();
                }).catch((error) => {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, client cannot be created. Please try again', 'error');
                });
            },
            setupModel: function() {
                return {
                    name: '',
                    image_url:'',
                    street_address: '',
                    city: '',
                    state: '',
                    nationality: 'Nigeria',
                    contacts: [
                        {
                            first_name: '',
                            last_name: '',
                            email: '',
                            is_primary: true,
                            phone_number: ''
                        }
                    ],
                    brands: [
                        {
                            name: '',
                            image_url:'',
                        }
                    ]
                }
            },
            closeDialog: function() {
                this.dialog = false;
            },
            chooseFile(upload_type) {
                if(upload_type == 'client_logo') {
                    this.$refs.client_logo.click();
                }
                if(upload_type == 'brand_logo') {
                    this.$refs.brand_logo.click();
                }
            },
            onFileChange(event, uploadType) {
                if (uploadType === 'client_logo') {
                    this.client_logo_file = event.target.files[0];
                    this.client_logo_input_label = this.client_logo_file.name;
                     this.show_client_logo = true;
                    const output = $('#client_logo_one');
                    output.attr('src', URL.createObjectURL(event.target.files[0]));

                } else if (uploadType === 'brand_logo') {
                    this.brand_logo_file = event.target.files[0];
                    this.brand_logo_input_label = this.brand_logo_file.name;
                    this.show_brand_logo = true;
                    const output = $('#brand_logo');
                    output.attr('src', URL.createObjectURL(event.target.files[0]));
                }
            },
            generatePresignedUrl: async function(file, folder_name, uploadType) {
                await axios({
                        method: 'post',
                        url: '/presigned-url',
                        data: {
                            filename: file.name,
                            folder: folder_name
                        }
                    }).then((res) => {
                        this.s3_presigned_url = res.data;
                    }).catch((error) => {
                        console.log(error.response.data);
                        this.s3_presigned_url = "";
                        this.sweet_alert('An unknown error has occurred, upload failed. Please try again', 'error');
                    });
                return this.s3_presigned_url;
            },
             uploadFile: async function(file, presigned_url, upload_type) {
                this.show_progress_bar = true;
                this.upload_percentage = 0;

                if (upload_type === 'client_logo') {
                    this.current_upload_title = "Uploading client logo";
                } else {
                    this.current_upload_title = "Uploading brand logo";
                }

                await axios.put(presigned_url, file, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function( progressEvent ) {
                            this.upload_percentage = parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total));
                        }.bind(this)
                    }
                ).then((res) => {
                    if (upload_type === 'client_logo'){
                        this.client_logo_url = `https:${presigned_url.split('?')[0].substr(6)}`;
                        this.client.image_url =  this.client_logo_url;
                    }
                    else if (upload_type === 'brand_logo'){
                        this.brand_logo_url = `https:${presigned_url.split('?')[0].substr(6)}`;
                         this.client.brands[0].image_url= this.brand_logo_url;
                    }
                }).catch((error) => {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, upload failed. Please try again', 'error');
                });
            }
        }
    }
</script>