<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="800px">
             <template v-slot:activator="{ on }">
                <v-btn color="" class="default-vue-btn" dark v-on="on">Edit</v-btn>
            </template>
            <v-card>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-subheader>Company Information</v-subheader>
                            <v-divider></v-divider>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true"   
                                                :label="'Company Name'" :placeholder="'Company Name'" 
                                                :hint="'Enter the name of your company'" :solo="true" :single-line="true" 
                                                v-validate="'required|max:255'" :error-messages="errors.collect('name')"
                                                v-model="company.name" data-vv-name="name" disabled="disabled">
                                    </v-text-field>
                                </v-flex>
                                   <v-flex xs12 sm5 md5>
                                     <v-text-field solo
                                               v-model="company.logo" 
                                               prepend-icon="attach_file" name="certificate"  
                                               :error-messages="errors.collect('compnay_image_url')"  
                                               @click="chooseFile()"></v-text-field>
                                     <input type="file" 
                                               style="display: none" 
                                               ref="company_logo" 
                                               accept=".png,.jpeg,.jpg" 
                                               v-validate="'ext:jpg,png,jpeg'"  
                                               data-vv-name="company_image_url" 
                                               @change="onFileChange($event)">
                                </v-flex>
                                 <v-flex xs12 sm1 md1>
                                             <b-img thumbnail fluid :src="company.logo"  style="width: 50px; height: 50px" id="company_logo" alt="Image 1"></b-img>
                      
                                </v-flex>
                            </v-layout>

                            <v-subheader>Primary Contact</v-subheader>
                            <v-divider></v-divider>
                            <v-layout wrap>
                                  <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true"   :label="'Street Address'" 
                                                :placeholder="'Street Address'" :hint="'Enter the street address of your company'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('street_address')"
                                                v-model="company.address" data-vv-name="street_address">
                                    </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm3 md3>
                                    <v-text-field required :clearable="true"   :label="'City'" 
                                                :placeholder="'City'" :hint="'Enter the city of your company'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('city')"
                                                v-model="company.city" data-vv-name="city">
                                    </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm3 md3 >
                                    <v-text-field required :clearable="true"   :label="'State'" 
                                                :placeholder="'State'" :hint="'Enter the state of your company'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('state')"
                                                v-model="company.state" data-vv-name="state">
                                    </v-text-field>
                                </v-flex>
                               
                            </v-layout>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true"   :label="'Email'" 
                                                :placeholder="'Email'" :hint="'Enter the email of your company'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|email'"
                                                :error-messages="errors.collect('email')"
                                                v-model="company.email" data-vv-name="email">
                                    </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field  required :clearable="true"   :label="'Phone Number'" 
                                                :placeholder="'Phone Number'" :hint="'Enter the phone number of your company'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|numeric'"
                                                :error-messages="errors.collect('phone_number')"
                                                v-model="company.phone_number" data-vv-name="phone_number">
                                    </v-text-field>
                                </v-flex>
                            </v-layout>

                              <v-layout wrap>
                                <v-flex xs12 sm4 md4>
                                    <v-text-field  required :clearable="true"   :label="'Website'" 
                                                :placeholder="'Websire'" :hint="'Enter the website of your company'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('website')"
                                                v-model="company.website" data-vv-name="website">
                                    </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm4 md4>
                                    <v-text-field  required :clearable="true"   :label="'Company RC'" 
                                                :placeholder="'Company RC'" :hint="'Enter the company rc of your company'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('last_name')"
                                                v-model="company.company_rc" data-vv-name="last_name">
                                    </v-text-field>
                                </v-flex>

                                 <v-flex xs12 sm3 md3>
                                    <v-text-field  required :clearable="true"   :label="'Company Color'" 
                                                :placeholder="'Company RC'" :hint="'Enter the color of your company'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('color')"
                                                v-model="company.color" data-vv-name="color">
                                    </v-text-field>

                                 </v-flex>

                                <v-flex xs12 sm1 md1>
                                   <input  v-model="company.color" type="color">
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
                    <v-btn color="" class="default-vue-btn" dark @click="editCompany()">Save</v-btn>
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
            companyData: Object,
            permissionList:Array,
            routes:Object,
        },
        data() {
            return {
                dialog: false,
                company: this.setupModel(),
                editMode: false,
                company_logo_file: '',
                company_logo_url: '',
                company_logo_input_label: '',
                s3_presigned_url: '',
                show_progress_bar: false,
                upload_percentage: 0,
                current_upload_title: '',
                upload_image: false,   
                dictionary: {
                    custom: {
                        name: {
                            required: () => 'Company name cannot be empty',
                            max: 'The company name field may not be greater than 255 characters',
                        },
                        street_address: {
                            required: () => 'Company street address cannot be empty',
                            max: 'The street address field may not be greater than 255 characters'
                        },
                        city: {
                            required: () => 'Company city cannot be empty',
                            max: 'The city field may not be greater than 255 characters'
                        },
                        state: {
                            required: () => 'Company state cannot be empty',
                            max: 'The state field may not be greater than 255 characters'
                        },
                        first_name: {
                            required: () => 'Website of company cannot be empty',
                            max: () => 'The first name field may not be greater than 255 characters'
                        },
                        last_name: {
                            required: () => 'Company Registration Certificate of company cannot be empty',
                            max: () => 'The last name field may not be greater than 255 characters'
                        },
                        last_name: {
                            required: () => 'Email address of company cannot be empty',
                            email: () => 'This must be a valid email address'
                        },
                        phone_number: {
                            required: () => 'Phone number of company cannot be empty',
                            numeric: () => 'Phone number must be all digits'
                        },
                         color: {
                            required: () => 'Phone number of company cannot be empty',
                            numeric: () => 'Phone number must be all digits'
                        },
                        logo: {
                            numeric: () => 'The company logo must be a picture'
                        },
                    }
                }
            };
        },
        created() {
            var self = this;
             self.company= this.companyData,
            Event.$on('view-company', function(company) {
                self.openDialog(company);
                self.company_logo_input_label = company.logo;
            });
          
        },
        mounted() {
            console.log('Edit company Component mounted.');
            this.$validator.localize('en', this.dictionary);
        },
        methods: {
            setupModel: function() {
                return {
                    name: '',
                    address: '',
                    logo:'',
                    website: '',
                    company_rc: '',
                    email: '',
                    phone_number: '',
                    city: '',
                    state: '',
                    country: 'Nigeria', 
                    color: '#ffff00'
                }
            },
            openDialog: function(item) {
                if (item['contacts'].length == 0) {
                    item['contacts'] = [{}];
                }
                this.company = item;
                this.dialog = true;
            },
            closeDialog: function() {
                this.dialog = false;
            },
            editCompany:async function(event) {
                self = this;
               this.$validator.validateAll().then((result) => {
                    if (result) {
                       return;
                    }
                });

                  try {
                    // generate presigned url for ciient logo upload
                    if(this.upload_image){
                       await this.generatePresignedUrl(this.company_logo_file, 'company-images/');
                       await this.uploadFile(this.company_logo_file, this.s3_presigned_url);
                    }
                    // make axios call to edit created company to db
                       self.makeUpdateRequest();
                } catch (error) {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, logo upload failed. Please try again', 'error');
                }

              
            
            },
            makeUpdateRequest: function() {
                 this.sweet_alert('Updating company information', 'info');
               this.company.country= 'Nigeria', 
                  console.log(this.company);
                 axios({
                    method: 'patch',
                     url: this.routes.company_update,
                     data: this.company
                }).then((res) => {
                   this.sweet_alert('Company was successfully created', 'success');
                   Event.$emit('company-updated', res.data.data);
                   this.company = res.data.data
                    this.closeDialog();
                }).catch((error) => {
                    this.sweet_alert('An unknown error has occurred, company cannot be updated. Please try again', 'error');
                })
                
            },
             chooseFile() {
                    this.$refs.company_logo.click();
            },
            onFileChange(event) {
                    this.upload_image = true;
                    this.company_logo_file = event.target.files[0];
                    this.company_logo_input_label = this.company_logo_file.name;
                     this.show_company_logo = true;
                    const output = $('#company_logo');
                    output.attr('src', URL.createObjectURL(event.target.files[0]));
            },
             generatePresignedUrl: async function(file, folder_name) {
                await axios({
                        method: 'post',
                        url: this.routes.presigned_url,
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
             uploadFile: async function(file, presigned_url) {
                this.show_progress_bar = true;
                this.upload_percentage = 0;
                this.current_upload_title = "Uploading company logo";
             
                await axios.put(presigned_url, file, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function( progressEvent ) {
                            this.upload_percentage = parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total));
                        }.bind(this)
                    }
                ).then((res) => {
                     this.show_progress_bar = false;
                     this.company.logo = `https:${presigned_url.split('?')[0].substr(6)}`;
                }).catch((error) => {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, upload failed. Please try again', 'error');
                });
            }

        }
    }
</script>