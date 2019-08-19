<template>
<div>
 <v-container grid-list-md>
        <v-form>
            <v-layout wrap>
                <v-flex xs12 sm4 md4>
                    <b-img thumbnail fluid :src="companyData.logo"  @click="chooseFile()" id="output-img" alt="Image 1"></b-img>
                    <input type="file" style="display: none" ref="logo" accept="image/*" v-validate="'required|ext:png,jpeg'" name="asset" @change="on_file_change($event, 'ASSET')">
                </v-flex>
                <v-flex xs12 sm8 md8>
                    <span>Campaign Name:</span>
                    <v-flex xs12 sm6 md6>
                    <v-text-field v-validate="'required'" name="name" readonly placeholder="Enter Name" :value="companyData.name" ></v-text-field>
                    <span>Campaign Address:</span>
                    <v-text-field v-validate="'required'" v-model="address" name="address" placeholder="Enter Address" :value="companyData.address" ></v-text-field>
                   
                     <v-btn color="" class="default-vue-btn" dark @click="process_form()">Update</v-btn>
                     </v-flex>
                </v-flex>
          </v-layout>
        </v-form>
 </v-container>  
</div>       
</template>
<style>
    .v-text-field .v-input__slot {
        padding: 0px 12px;
        min-height: 45px;
        margin-bottom: 0px;
        border: 1px solid #ccc;
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
    .theme--dark.v-btn.v-btn--disabled:not(.v-btn--icon):not(.v-btn--flat):not(.v-btn--outline) {
        background-color: hsl(184, 55%, 53%)!important;
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
                assetInputLabel: 'Choose Photo file',
                 name: '',
                 address: this.companyData.address,
                 assetFile: '',
                 s3PresignedUrl: '',
                 logoUrl: this.companyData.logo,
               
            };
        },
        mounted() {
             console.log(this.routes.company_update);
        },  
        created() {    
          }, 
         methods: {   
            chooseFile() {
                    this.$refs.logo.click();
            },
            on_file_change: async function(event) {
                this.assetFile = event.target.files[0];
                  try {
                        // generate presigned url for logo upload and upload
                       await this.generate_presigned_url(this.assetFile, 'company-logo/');
                       await this.upload_file(this.assetFile, this.s3PresignedUrl);
                  
                } catch (error) {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, company not updated. Please try again', 'error');
                }
            },

            process_form: async function(event) {
                try {
                    // make axios call to update compnay details
                   await this.update_company();
                } catch (error) {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, company not updated. Please try again', 'error');
                }
            },
            generate_presigned_url: async function(file, folderName, uploadType) {
                await axios({
                        method: 'post',
                        url: this.routes.presigned_url,
                        data: {
                            filename: file.name,
                            folder: folderName
                        }
                    }).then((res) => {
                        this.s3PresignedUrl = res.data;
                    }).catch((error) => {
                        console.log(error.response.data);
                        this.s3PresignedUrl = "";
                        this.sweet_alert('An unknown error has occurred, company not updated. Please try again', 'error');
                    });
                return this.s3PresignedUrl;
            },
            upload_file: async function(file, presignedUrl, uploadType) {
                this.showProgressBar = true;
                this.uploadPercentage = 0;
                this.currentUploadTitle = "Uploading Media Asset";

                await axios.put(presignedUrl, file, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function( progressEvent ) {
                            this.uploadPercentage = parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total));
                        }.bind(this)
                    }
                ).then((res) => {
                        this.logoUrl = `https:${presignedUrl.split('?')[0].substr(6)}`;
                         const output = $('#output-img');
                         output.attr('src', this.logoUrl);
                }).catch((error) => {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred,  company not updated. Please try again', 'error');
                });
            },
            update_company: async function(event) {
                 axios({
                     method: 'patch',
                     url: this.routes.company_update,
                     data: {
                         address: this.address,
                         logo: this.logoUrl
                     }
                 }).then((res) => {
                  console.log(res.data);
                    if (res.data.status === 'success') {
                       // Event.$emit('latest-assets', res.data.data);
                        this.sweet_alert('Company was successfully updated', 'success');
                    } else {
                        this.sweet_alert('Something went wrong, company not updated. Try again!', 'error');
                    }
                 }).catch((error) => {
                     this.sweet_alert(error.response.data.message, 'error');
                 });
            },
        }
    }
</script>
