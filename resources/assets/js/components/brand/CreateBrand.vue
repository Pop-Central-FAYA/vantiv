<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="800px">
            <template v-slot:activator="{ on }">
                <v-btn color="" class="default-vue-btn" dark v-on="on">Create Brand</v-btn>
            </template>
            <v-card>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-subheader>Brand Information</v-subheader>
                            <v-divider></v-divider>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field required :clearable="true" :label="'Name'" 
                                                :placeholder="'Brand Name'" :hint="'Enter the name of your brand'" 
                                                :solo="true" :single-line="true"
                                                v-validate="'required|max:255'"
                                                :error-messages="errors.collect('brand_name')"
                                                v-model="brand.name"
                                                data-vv-name="brand_name">
                                    </v-text-field>
                                </v-flex>
                                  <v-flex xs12 sm5 md5>
                                    <v-text-field solo v-model="brand_logo_input_label" prepend-icon="attach_file" name="certificate" :error-messages="errors.collect('brand_image_url')"  @click="chooseFile()"></v-text-field>
                                    <input type="file" style="display: none" ref="brand_logo" accept=".png,.jpeg,.jpg"  v-validate="'required|ext:jpg,png,jpeg'"  data-vv-name="brand_image_url"  :error-messages="errors.collect('brand_image_url')" @change="onFileChange($event)">
                                   
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
         props: {
         client_id: String
        },
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
        mounted() {
            console.log('Create brand Component mounted.');
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
                    // generate presigned url for brand logo upload
                        await this.generatePresignedUrl(this.brand_logo_file, 'brand-images/');
                        await this.uploadFile(this.brand_logo_file, this.s3_presigned_url, 'brand_logo');
                    // make axios call to store created asset to db
                       await this. storeRequest();
                   
                } catch (error) {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, brand upload failed. Please try again', 'error');
                }
            },
             storeRequest: function() {
                 this.show_progress_bar = false;
                  this.sweet_alert('Saving brand information', 'info');
                 
                   this.brand.client_id= this.client_id;
                axios({
                    method: 'post',
                    url: '/brands',
                    data: this.brand
                }).then((res) => {
                    Event.$emit('brand-created', res.data.data);
                    this.sweet_alert('Client was successfully created', 'success');
                    this.brand = this.setupModel();
                     this.show_brand_logo = false;
                    this.brand_logo_input_label = 'Choose brand logo';
                    this.closeDialog();
                }).catch((error) => {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, brand cannot be created. Please try again', 'error');
                });
                
            },
            setupModel: function() {
                return {
                        client_id:'',
                        name: '',
                        image_url:'',
                   }
            },
            closeDialog: function() {
                this.dialog = false;
            },
            chooseFile() {
                this.$refs.brand_logo.click();
            },
            onFileChange(event) {
               
                    this.brand_logo_file = event.target.files[0];
                    this.brand_logo_input_label = this.brand_logo_file.name;
                    this.show_brand_logo = true;
                    const output = $('#brand_logo');
                    output.attr('src', URL.createObjectURL(event.target.files[0]));

            },
            generatePresignedUrl: async function(file, folder_name, uploadType) {
                await axios({
                        method: 'post',
                        url: '/company/presigned-url',
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
                         this.brand_logo_url = `https:${presigned_url.split('?')[0].substr(6)}`;
                         this.brand.image_url= this.brand_logo_url;
                }).catch((error) => {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, upload failed. Please try again', 'error');
                });
            }
        }
    }
</script>