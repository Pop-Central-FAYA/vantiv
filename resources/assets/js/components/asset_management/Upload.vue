<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 text-right">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal">Upload Asset</button>
            </div>
        </div>
        <div class="row">
            <!-- Modal -->
            <div class="modal" id="exampleModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Media Asset</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client">Client:</label>
                                        <select class="form-control" v-validate="'required'" v-model="client" name="client" @change="get_brands($event)">
                                            <option value="">select client</option>
                                            <option v-for="(client, key) in clients" v-bind:key="key" :value="client.id">{{ client.company_name}}</option>
                                        </select>
                                    </div>
                                    <span class="text-danger" v-show="errors.has('client')">{{ errors.first('client') }}</span>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="brand">Brand:</label>
                                        <select class="form-control" v-validate="'required'" v-model="brand" name="brand">
                                            <option value="">select brand</option>
                                            <option v-for="(brand, key) in brands" v-bind:key="key" :value="brand.id">{{ brand.name }}</option>
                                        </select>
                                    </div>
                                    <span class="text-danger" v-show="errors.has('brand')">{{ errors.first('brand') }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="mediaType">Media Type:</label>
                                        <select v-validate="'required'" name="media_type" class="form-control" v-model="mediaType">
                                            <option value="Tv" selected>Tv</option>
                                        </select>
                                    </div>
                                    <span class="text-danger" v-show="errors.has('media_type')">{{ errors.first('media_type') }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mediaType">File Duration:</label>
                                        <select v-validate="'required'" name="duration" class="form-control" v-model="duration">
                                            <option value="">select duration</option>
                                            <option value="15">15 Seconds</option>
                                            <option value="30">30 Seconds</option>
                                            <option value="45">45 Seconds</option>
                                            <option value="60">60 Seconds</option>
                                        </select>
                                    </div>
                                    <span class="text-danger" v-show="errors.has('duration')">{{ errors.first('duration') }}</span>
                                </div>
                                <div class="col-md-8">
                                    <label for="mediaType">Upload Asset:</label>
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <input v-validate="'required|ext:mp4,3gp,ogg,avi'" name="asset" type="file" class="custom-file-input" @change="on_file_change($event, 'ASSET')">
                                            <label class="custom-file-label" for="asset">{{ this.assetInputLabel }}</label>
                                        </div>
                                    </div>
                                    <span class="text-danger" v-show="errors.has('asset')">{{ errors.first('asset') }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="mediaType">Upload Regulatory Certificate:</label>
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <input v-validate="'required|ext:txt'" name="certificate" type="file" class="custom-file-input" @change="on_file_change($event, 'REG_CERT')">
                                            <label class="custom-file-label" for="regCert">{{ this.regCertInputLabel }}</label>
                                        </div>
                                    </div>
                                    <span class="text-danger" v-show="errors.has('certificate')">{{ errors.first('certificate') }}</span>
                                </div>
                            </div>

                            <div class="row mb-3" v-show="showProgressBar">
                                <div class="col-md-12">
                                    <p class="text-muted">{{ currentUploadTitle }}</p>
                                    <progress class="w-100" max="100" :value.prop="uploadPercentage"></progress>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="process_form()">Create Media Asset</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            clients: Array,
        },
        data() {
            return {
                client: '',
                brand: '',
                duration: '',
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
                assetInputLabel: 'Choose media file',
                regCertInputLabel: 'Choose regulatory certifcate',
                brands: []
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
            get_brands(event) {
                if (event.target.value === '') {
                    return;
                }
                axios({
                    method: 'get',
                    url: '/client/get-brands/'+event.target.value,
                    data: {
                        clients: event.target.value
                    }
                }).then((res) => {
                    this.brands = res.data.brands;
                }).catch((error) => {
                    this.brands = [];
                });
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
            on_file_change(event, uploadType) {
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
                if (this.uploadedMediaAssetDuration != this.duration) {
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
                    await this.generate_presigned_url(this.regulatoryCertFile, 'regulatory-certificates/');
                    await this.upload_file(this.assetFile, this.s3PresignedUrl, 'REG_CERT');
                    console.log(this.regulatoryCertUrl);
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
                     url: '/agency/media-assets/create',
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
                    console.log(res.data);
                    if (res.data.status === 'success') {
                        this.sweet_alert(res.data.data, 'success');
                        window.location = '/agency/media-assets/';
                    } else {
                        this.sweet_alert(res.data.data, 'error');
                    }
                 }).catch((error) => {
                     console.log(error.response.data);
                     this.sweet_alert(error.response.data.message, 'error');
                 });
            },
            generate_presigned_url: async function(file, folderName, uploadType) {
                await axios({
                        method: 'post',
                        url: 'media-assets/presigned-url',
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