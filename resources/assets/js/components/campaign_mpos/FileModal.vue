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
                                            <div>{{ asset[0].duration }} Secs</div>
                                            <div>{{ asset[0].file_name }} </div>
                                            <div>
                                                <input type="hidden" id="file-asset" :value="asset[0].asset_url">
                                                <v-btn color="info" @click="copyToClipboard()" small dark>copy url</v-btn>
                                            </div>
                                        </template>
                                        <v-card>
                                            <v-card-text><video :src="asset[0].asset_url" controls></video></v-card-text>
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
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" dark @click="dialog = false">Close</v-btn>
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
        }
    },
    mounted() {
        this.filterAssetByDuration(this.assets, this.mpo.campaign_mpo_time_belts);
    },
    methods : {
        getDistinctAssetId(time_belts) {
            return [...new Set(time_belts.map(x => x.asset_id))];
        },
        filterAssetByDuration(assets, time_belts) {
            const time_belt = this.getDistinctAssetId(time_belts);
            //this.form.duration = duration;
            const filtered_assets = assets.filter(item => time_belt.some(resultItem => resultItem === item.id));
            this.groupedAssets =  _.groupBy(filtered_assets, asset => asset.duration);
        },
        copyToClipboard : function(asset_url){
            let testingCodeToCopy = document.querySelector('#file-asset')
            testingCodeToCopy.setAttribute('type', 'text')
            testingCodeToCopy.select()
            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                this.sweet_alert('File ulr copied to clipboard ' + msg, 'success');
            } catch (err) {
                this.sweet_alert('Oops, unable to copy', 'error');
            }

            testingCodeToCopy.setAttribute('type', 'hidden')
            window.getSelection().removeAllRanges()
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


