<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="600px">
        <template v-slot:activator="{ on }">
            <a dark v-on="on">{{ asset.file_name}}</a>
        </template>
        <v-card>
            <v-card-text>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex xs12 sm12 md12>
                            <video class="video" :src="asset.asset_url" controls></video>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="" class="default-vue-btn" dark @click="dialog = false">Close</v-btn>
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
    export default {
        props: {
            asset: Object
        },
        data() {
            return {
                dialog: false
            };
        },
        mounted() {
            console.log('Play Video Component mounted.');
        }
    }
</script>