<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="600px">
        <template v-slot:activator="{ on }">
            <v-btn color="" class="default-vue-btn" dark v-on="on">Upload Asset</v-btn>
        </template>
        <v-card>
            <v-card-text>
            <v-container grid-list-md>
                <v-form>
                    <v-layout wrap>
                        <v-flex xs12 sm6 md6>
                            <span>Client</span>
                            <v-select
                                v-model="client"
                                :items="clients"
                                item-text="company_name"
                                item-value="id"
                                v-validate="'required'"
                                name="client"
                                solo
                                @change="get_brands"
                            ></v-select>
                            <span class="text-danger" v-show="errors.has('client')">{{ errors.first('client') }}</span>
                        </v-flex>
                        <v-flex xs12 sm6 md6>
                            <span>Brand</span>
                            <v-select
                                v-model="brand"
                                :items="filteredBrands"
                                item-text="name"
                                item-value="id"
                                v-validate="'required'"
                                name="brand"
                                solo
                            ></v-select>
                            <span class="text-danger" v-show="errors.has('brand')">{{ errors.first('brand') }}</span>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex xs12 sm6 md3>
                            <span>Duration</span>
                            <v-select
                                v-model="duration"
                                :items="durations"
                                v-validate="'required'"
                                name="duration"
                                solo
                            ></v-select>
                            <span class="text-danger" v-show="errors.has('duration')">{{ errors.first('duration') }}</span>
                        </v-flex>
                        <v-flex xs12 sm6 md9>
                            <span>
                                Video File
                                <v-tooltip right>
                                    <template v-slot:activator="{ on }">
                                        <v-icon small color="blue-grey darken-2" dark v-on="on">info</v-icon>
                                    </template>
                                    <span>Only video extension are allowed; mp4,3gp,ogg,avi</span>
                                </v-tooltip>
                            </span>
                            <v-text-field solo v-model="assetInputLabel" prepend-icon="attach_file" accept="video/*" @click="chooseFile('ASSET')"></v-text-field>
                            <input type="file" style="display: none" ref="video" accept="video/*" v-validate="'required|ext:mp4,3gp,ogg,avi'" name="asset" @change="on_file_change($event, 'ASSET')">
                            <span class="text-danger" v-show="errors.has('asset')">{{ errors.first('asset') }}</span>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex xs12 sm12 md12>
                            <span>
                                Regulatory Certificate
                                <v-tooltip right>
                                <template v-slot:activator="{ on }">
                                    <v-icon small color="blue-grey darken-2" dark v-on="on">info</v-icon>
                                </template>
                                <span>Ceriticate is optional. Following extensions are allowed; txt,pdf,docx,doc,png,jpeg,jpg,gif</span>
                                </v-tooltip>
                            </span>
                            <v-text-field solo v-model="regCertInputLabel" prepend-icon="attach_file" name="certificate" @click="chooseFile('REG_CERT')"></v-text-field>
                            <input type="file" style="display: none" ref="certificate" accept=".txt,.pdf,.docx,.doc,.png,.jpeg,.jpg,.gif" @change="on_file_change($event, 'REG_CERT')">
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex xs12 sm12 md12 v-show="showProgressBar">
                            <p class="text-muted">{{ currentUploadTitle }}</p>
                            <v-progress-linear v-model="uploadPercentage" color="success"></v-progress-linear>
                        </v-flex>
                    </v-layout>
                </v-form>
            </v-container>
            </v-card-text>
            <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="red" dark @click="dialog = false">Close</v-btn>
            <v-btn color="" class="default-vue-btn" dark @click="process_form()">Upload</v-btn>
            </v-card-actions>
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
    const DECREASE_DURATION = 2;
    export default {
        props: {
            clients: [Array, Object],
            brands: [Array, Object]
        },
        data() {
            return {
                dialog: false,
                client: '',
                brand: '',
                duration: '',
                durations: ['15', '30', '45', '60'],
                mediaType: 'Tv',
                s3PresignedUrl: '',
                assetFile: '',
                uploadedMediaAssetDuration: 0, 
                assetUrl: '',
                regulatoryCertFile: '',
                regulatoryCertUrl: '',
                uploadPercentage: 0,
                showProgressBar: false,
                currentUploadTitle: '',
                assetInputLabel: 'Choose video file',
                regCertInputLabel: 'Choose regulatory certifcate',
                filteredBrands: []
            };
        },
        mounted() {
            console.log('Media asset upload Component mounted.');
        },
        watch: {
            uploadedMediaAssetDuration: function () {
                this.uploadedMediaAssetDuration = Math.floor(this.uploadedMediaAssetDuration);
            }
        },
        methods: {
            get_brands() {
                this.filteredBrands = this.brands[this.client];
            },
            setFileDuration: function(file) {
                let video = document.createElement('video');
                video.preload = 'metadata';
                video.onloadedmetadata = () => {
                    window.URL.revokeObjectURL(video.src);
                    this.uploadedMediaAssetDuration = video.duration;
                };
                video.src = URL.createObjectURL(file);
            },
            chooseFile(upload_type) {
                if(upload_type == 'ASSET') {
                    this.$refs.video.click();
                }
                if(upload_type == 'REG_CERT') {
                    this.$refs.certificate.click();
                }
            },
            on_file_change(event, uploadType) {
                console.log(event);
                if (uploadType === 'ASSET') {
                    this.assetFile = event.target.files[0];
                    this.setFileDuration(this.assetFile);
                    this.assetInputLabel = this.assetFile.name;
                } else if (uploadType === 'REG_CERT') {
                    this.regulatoryCertFile = event.target.files[0];
                    this.regCertInputLabel = this.regulatoryCertFile.name;
                }
            },
            process_form: async function(event) {
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

                // validate media asset duration
                if (this.uploadedMediaAssetDuration < this.duration - DECREASE_DURATION || this.uploadedMediaAssetDuration > this.duration) {
                    let msg = `You are trying to upload a file of ${this.uploadedMediaAssetDuration} seconds into a duration of ${this.duration} seconds`;
                    this.sweet_alert(msg, 'error');
                    return;
                }

                try {
                    // generate presigned url for asset upload
                    await this.generate_presigned_url(this.assetFile, 'media-assets/');
                    await this.upload_file(this.assetFile, this.s3PresignedUrl, 'ASSET');
                    console.log(this.assetUrl);
                    // generate presigned url for regulatory certificate upload
                    if(this.regulatoryCertFile) {
                        await this.generate_presigned_url(this.regulatoryCertFile, 'regulatory-certificates/');
                        await this.upload_file(this.assetFile, this.s3PresignedUrl, 'REG_CERT');
                        console.log(this.regulatoryCertUrl);
                    }
                    // make axios call to store created asset to db
                    await this.store_uploaded_asset();
                } catch (error) {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, asset upload failed. Please try again', 'error');
                }
            },
            store_uploaded_asset: async function(event) {
                 axios({
                     method: 'post',
                     url: '/media-assets/create',
                     data: {
                         client_id: this.client,
                         brand_id: this.brand,
                         media_type: this.mediaType,
                         duration: this.duration,
                         asset_url: this.assetUrl,
                         regulatory_cert_url: this.regulatoryCertUrl,
                         file_name: this.assetFile.name
                     }
                 }).then((res) => {
                    console.log(res.data.data);
                    if (res.data.status === 'success') {
                        this.dialog = false;
                        Event.$emit('latest-assets', res.data.data);
                        this.sweet_alert('Media asset was successfully created', 'success');
                    } else {
                        this.sweet_alert('Something went wrong, media assets cannot be created. Try again!', 'error');
                    }
                 }).catch((error) => {
                     console.log(error.response.data);
                     this.sweet_alert(error.response.data.message, 'error');
                 });
            },
            generate_presigned_url: async function(file, folderName, uploadType) {
                await axios({
                        method: 'post',
                        url: '/presigned-url',
                        data: {
                            filename: file.name,
                            folder: folderName
                        }
                    }).then((res) => {
                        this.s3PresignedUrl = res.data;
                    }).catch((error) => {
                        console.log(error.response.data);
                        this.s3PresignedUrl = "";
                        this.sweet_alert('An unknown error has occurred, asset upload failed. Please try again', 'error');
                    });
                return this.s3PresignedUrl;
            },
            upload_file: async function(file, presignedUrl, uploadType) {
                this.showProgressBar = true;
                this.uploadPercentage = 0;

                if (uploadType === 'ASSET') {
                    this.currentUploadTitle = "Uploading Media Asset";
                } else {
                    this.currentUploadTitle = "Uploading Regulatory Certificate";
                }

                await axios.put(presignedUrl, file, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function( progressEvent ) {
                            this.uploadPercentage = parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total));
                        }.bind(this)
                    }
                ).then((res) => {
                    if (uploadType === 'ASSET'){
                        this.assetUrl = `https:${presignedUrl.split('?')[0].substr(6)}`;
                    }
                    else if (uploadType === 'REG_CERT'){
                        this.regulatoryCertUrl = `https:${presignedUrl.split('?')[0].substr(6)}`;
                    }
                }).catch((error) => {
                    console.log(error);
                    this.sweet_alert('An unknown error has occurred, asset upload failed. Please try again', 'error');
                });
            }
        }
    }
</script>