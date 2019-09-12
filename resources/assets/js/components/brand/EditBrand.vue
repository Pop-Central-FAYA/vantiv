<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="800px">
            <template v-slot:activator="{ on }">
            </template>
            <v-card>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-subheader>Brand Information</v-subheader>
                            <v-divider></v-divider>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true"   
                                                :label="'Brand Name'" :placeholder="'Brand Name'" 
                                                :hint="'Enter the name of your brand'" :solo="true" :single-line="true" 
                                                v-validate="'required|max:255'" :error-messages="errors.collect('name')"
                                                v-model="brand.name" data-vv-name="name">
                                    </v-text-field>
                                </v-flex>
                                   <v-flex xs12 sm5 md5>
                                     <v-text-field solo
                                               v-model="brand_logo_input_label" 
                                               prepend-icon="attach_file" name="certificate"  
                                               :error-messages="errors.collect('brand_image_url')"  
                                               @click="chooseFile()"></v-text-field>
                                     <input type="file" 
                                               style="display: none" 
                                               ref="brand_logo" 
                                               accept=".png,.jpeg,.jpg" 
                                               v-validate="'ext:jpg,png,jpeg'"  
                                               data-vv-name="brand_image_url" 
                                               @change="onFileChange($event)">
                                </v-flex>
                                 <v-flex xs12 sm1 md1>
                                             <b-img thumbnail fluid :src="brand.image_url"  style="width: 50px; height: 50px" id="brand_logo" alt="Image 1"></b-img>
                      
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
                    <v-btn color="" class="default-vue-btn" dark @click="editClient()">Save</v-btn>
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
                brand: this.setupModel(),
                logo:'',
                show_brand_logo: false,
                brand_logo_input_label: 'Choose brand logo',
                brand_logo_file: '',
                s3_presigned_url: '',
                show_progress_bar: false,
                upload_percentage: 0,
                current_upload_title: '',
                brand_logo_url: '',
                upload_image: false,   
                dictionary: {
                    custom: {
                        brand_name: {
                            required: () => 'Brand name cannot be empty',
                            max: () => 'The brand name field may not be greater than 255 characters'
                        },
                        brand_image_url: {
                            required: () => 'Brand logo cannot be empty',
                            numeric: () => 'The brand logo must be a picture'
                        }
                    }
                }
            };
        },
        created() {
            var self = this;
            Event.$on('edit-brand', function(brand) {
                self.openDialog(brand);
                self.brand_logo_input_label = brand.image_url;
            });
          
        },
        mounted() {
            console.log('Edit brand Component mounted.');
            this.$validator.localize('en', this.dictionary);
        },
        methods: {
            setupModel: function() {
                 return {
                        name: '',
                        image_url:'',
                     }
            },
            openDialog: function(item) {
                this.brand = item;
                this.dialog = true;
            },
            closeDialog: function() {
                this.brand = this.setupModel();
                this.dialog = false;
            },
            editClient:async function(event) {
                self = this;
                this.$validator.validateAll().then((result) => {
                    if (result) {
                       return;
                    }
                });

                  try {
                    // generate presigned url for ciient logo upload
                    if(this.upload_image){
                       await this.generatePresignedUrl(this.brand_logo_file, 'brand-images/');
                       await this.uploadFile(this.brand_logo_file, this.s3_presigned_url);
                    }
                    // make axios call to edit created brand to db
                       self.makeUpdateRequest();
                } catch (error) {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, logo upload failed. Please try again', 'error');
                }
            },
            makeUpdateRequest: function() {

                 this.sweet_alert('Saving brand information', 'info');
                 axios({
                    method: 'patch',
                    url: '/brands/'+this.brand.id,
                    data: this.brand
                }).then((res) => {
                    this.sweet_alert('Brand was successfully created', 'success');
                    Event.$emit('brand-updated', res.data.data);
                    this.closeDialog();
                }).catch((error) => {
                    this.sweet_alert('An unknown error has occurred, brand cannot be updated. Please try again', 'error');
                });
 
            },
             chooseFile() {
                    this.$refs.brand_logo.click();
            },
            onFileChange(event) {
                    this.upload_image = true;
                    this.brand_logo_file = event.target.files[0];
                    this.brand_logo_input_label = this.brand_logo_file.name;
                     this.show_client_logo = true;
                    const output = $('#client_logo');
                    output.attr('src', URL.createObjectURL(event.target.files[0]));
            },
             generatePresignedUrl: async function(file, folder_name) {
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
             uploadFile: async function(file, presigned_url) {
                this.show_progress_bar = true;
                this.upload_percentage = 0;
                this.current_upload_title = "Uploading brand logo";
             
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
                     this.brand.image_url = `https:${presigned_url.split('?')[0].substr(6)}`;
                }).catch((error) => {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, upload failed. Please try again', 'error');
                });
            }

        }
    }
</script>