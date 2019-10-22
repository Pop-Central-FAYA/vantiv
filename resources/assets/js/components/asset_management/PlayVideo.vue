<template>
    <v-layout>
        <v-dialog v-model="playDialog" persistent max-width="600px">
        <template v-slot:activator="{ on }" v-if="playDialog">
            <a v-on="on" class="default-vue-link">{{ asset.file_name}}</a>
            <v-spacer></v-spacer>
        </template>
        <v-card>
            <v-card-text class="px-2 pt-2 pb-0">
                <v-container grid-list-md class="pa-0">
                    <v-layout wrap>
                        <v-flex xs12 sm12 md12>
                            <video ref="video" class="video" :src="asset.expiry_url" controls></video>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="" class="default-vue-btn" dark @click="stopVideo()">Close</v-btn>
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
</style>

<script>
    export default {
        props: {
            asset: Object
        },
        data() {
            return {
                playDialog: false,
            };
        },
        mounted() {
            console.log('Play Video Component mounted.');
            var self = this
            Event.$on('play-modal', function(modal) {
                self.playDialog = modal
            })
        },
        methods: {
            stopVideo() {
                var video = this.$refs.video; 
                video.pause(); 
                this.playDialog = false;
            }
        }
    }
</script>