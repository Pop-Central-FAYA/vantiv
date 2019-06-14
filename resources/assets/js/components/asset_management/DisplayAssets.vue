<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header py-3">
                        <h4><b>ALL MEDIA ASSETS</b></h4>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">File Name</th>
                                    <th scope="col">Media Type</th>
                                    <th scope="col">Duration</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">brand</th>
                                    <th scope="col">Last Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(asset, key) in assets" v-bind:key="key">
                                    <td>{{ asset.file_name }}</td>
                                    <td>{{ asset.media_type }}</td>
                                    <td>{{ asset.duration }} secs</td>
                                    <td>{{ asset.client.company_name }}</td>
                                    <td>{{ asset.brand.name }}</td>
                                    <td>{{ asset.updated_at }}</td>
                                    <td>
                                        <button @click="delete_asset(asset.id)" class="btn btn-sm btn-danger bg-danger" type="button">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                assets: []
            };
        },
        mounted() {
            console.log('Display Assets Component mounted.');
            this.get_assets();
        },
        methods: {
            get_assets() {
                axios({
                    method: 'get',
                    url: '/agency/media-assets/all'
                }).then((res) => {
                    let result = res.data.data;
                    if (result.length === 0) {
                        this.sweet_alert('No Media asset was found', 'info');
                    } else {
                        this.assets = result;
                    }
                }).catch((error) => {
                    this.assets = [];
                    this.sweet_alert('An unknown error has occurred, assets cannot be retrieved. Please try again', 'error');
                });
            },
            delete_asset(assetID) {
                axios({
                    method: 'get',
                    url: '/agency/media-assets/delete/'+assetID
                }).then((res) => {
                    console.log(res.data);
                    if (res.data.status == 'success') {
                        this.assets = res.data.data;
                        this.sweet_alert("Media asset was successfully deleted", 'info');
                    } else {
                        this.sweet_alert('Media asset cannot be deleted, try again', 'error');
                    }
                }).catch((error) => {
                    this.assets = [];
                    this.sweet_alert('An unknown error has occurred, media assets cannot be delete. Please try again', 'error');
                });
            }
        }
    }
</script>