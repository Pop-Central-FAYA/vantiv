<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="600px">
            <template v-slot:activator="{ on }">
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-icon color="success" dark left v-on="on" @click="dialog = true">fa-clipboard-list</v-icon>
                    </template>
                    <span>Submit MPO</span>
                </v-tooltip>
            </template>
            <v-card>
                <v-card-title>
                    <span class="headline"> {{ mpo.station }}</span>
                </v-card-title>
                <v-card-text>
                    <v-container px-0>
                        <v-layout row wrap>
                            <v-flex xs12 md12 lg12 mb-3>
                                <v-expansion-panel popout>
                                    <v-expansion-panel-content v-for="(asset,key) in groupedAssets" v-bind:key="key" expand-icon="remove_red_eye">
                                        <template v-slot:header>
                                            <div class="text-left">{{ asset.duration }} Secs</div>
                                            <div class="text-center">{{ asset.file_name }} </div>
                                            <div class="text-right">
                                                <v-btn color="info" @click="copyToClipboard(asset.asset_url)" small dark>copy url</v-btn>
                                            </div>
                                        </template>
                                        <v-card>
                                            <v-card-text><video :src="asset.asset_url" controls></video></v-card-text>
                                        </v-card>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap v-if="Object.keys(groupedAssets).length === 0" >
                            <v-flex xs12 sm12 md12>
                                <v-card-text>You have not attached a file on this Vendor</v-card-text>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap v-if="Object.keys(shareLink).length > 0">
                            <v-flex xs12 sm12 md12>
                                <v-btn color="info" @click="copyToClipboard(shareLink.url)" small dark>copy share link</v-btn>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-text>
                    <v-container grid-list-md>
                        <p>Generate Share Link</p>
                        <v-form>
                            <v-layout wrap>
                                <v-flex xs12 sm12 md12>
                                    <span>
                                        Email
                                    </span>
                                    <v-text-field v-validate="'required|email'" 
                                    type="text" placeholder="Vendors Email" 
                                    name="email" v-model="email"></v-text-field>
                                    <span class="text-danger" v-show="errors.has('email')">{{ errors.first('email') }}</span>
                                </v-flex>
                            </v-layout>
                        </v-form>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" class="default-vue-btn" dark @click="dialog = false">Close</v-btn>
                    <v-btn class="default-vue-btn" dark @click="addShareLink()">Generate Link</v-btn>
                </v-card-actions>
            </v-card>
            
        </v-dialog>
    </v-layout>
</template>
<script>
export default {
    props : {
        mpo: {
            required : true,
            type : Object
        },
        assets: Array
    },
    data() {
        return {
            dialog: false,
            groupedAssets: [],
            shareLink : {},
            email : '',
            assetData : this.assets
        }
    },
    mounted() {
        this.getShareLink()
        this.filterAssetByDuration(this.assetData, this.mpo.campaign_mpo_time_belts);
    },
    methods : {
        getDistinctAssetId(time_belts) {
            return [...new Set(time_belts.map(x => x.asset_id))];
        },
        filterAssetByDuration(assets, time_belts) {
            const time_belt = this.getDistinctAssetId(time_belts);
            this.groupedAssets = assets.filter(item => time_belt.some(resultItem => resultItem === item.id));
        },
        copyToClipboard : function(url){
            let copyListener = event => {
                document.removeEventListener("copy", copyListener, true);
                event.preventDefault();
                let clipboardData = event.clipboardData;
                clipboardData.clearData();
                clipboardData.setData("text/plain", url);
            };
            document.addEventListener("copy", copyListener, true);
            try {
                document.execCommand("copy")
                console.log(document)
                var message = 'copied '+url+' to clipboard'
                this.sweet_alert(message, 'success');
            } catch (error) {
                this.sweet_alert('Oops, unable to copy '+error, 'error');
                return false;
            }
        },
        getShareLink : function() {
            axios({
                method: 'get',
                url: `/mpos/${this.mpo.id}/share-links`
            }).then((res) => {
                let result = res.data.data;
                if (result != null) {
                    this.shareLink = result;                
                }
            }).catch((error) => {
                this.assetData = [];
                this.sweet_alert('An unknown error has occurred, link cannot be retrieved. Please try again', 'error');
            });
        },
        addShareLink : async function (event) {
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
            var msg = "Processing request, please wait...";
            this.sweet_alert(msg, 'info');
            axios({
                method: 'POST',
                url: `/mpos/${this.mpo.id}/share-links`,
                data: {email : this.email}
            }).then((res) => {
                if (res.data.status === 'success') {
                    this.sweet_alert(res.data.message, 'success');
                    this.email = ''
                    this.shareLink = res.data.data
                } else {
                    this.sweet_alert(res.data.message, 'error');
                }
            }).catch((error) => {
                this.sweet_alert(error.response.data.message, 'error');
            });
        }
    }
}
</script>
<style>
    .modal {
    overflow-y: auto;
    }

    .modal-open {
    overflow: auto;
    }
</style>


