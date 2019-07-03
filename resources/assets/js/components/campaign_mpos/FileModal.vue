<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="600px">
            <template v-slot:activator="{ on }">
                <v-btn color="success" small dark v-on="on">Submit</v-btn>
            </template>
            <v-card>
                <v-card-title>
                    <span class="headline"> {{ mpo.station }}</span>
                </v-card-title>
                
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-layout wrap v-for="(asset,key) in groupedAssets" v-bind:key="key">
                                <v-flex xs12 sm6 md2>
                                    <v-card-text>{{ asset[0].duration }} Seconds</v-card-text>
                                </v-flex>
                                <v-flex xs12 sm6 md3>
                                    <v-card-text>{{ asset[0].file_name }} </v-card-text>
                                </v-flex>
                                <v-flex xs12 sm6 md5>
                                    <video :src="asset[0].asset_url"></video>
                                </v-flex>
                                <v-flex xs12 sm6 md2>
                                    <input type="hidden" id="file-asset" :value="asset[0].asset_url">
                                    <v-btn color="info" @click="copyToClipboard()" small dark>copy url</v-btn>
                                </v-flex>
                                 <!-- v-if="Object.keys(groupedAssets).length" -->
                            </v-layout>
                            <v-layout wrap v-if="Object.keys(groupedAssets).length === 0" >
                                <v-flex xs12 sm12 md12>
                                    <v-card-text>You have not attached a file on this Vendor</v-card-text>
                                </v-flex>
                            </v-layout>
                        </v-form>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="success" dark @click="dialog = false">Close</v-btn>
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
        console.log(this.getDistinctAssetId(this.mpo.campaign_mpo_time_belts))
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


