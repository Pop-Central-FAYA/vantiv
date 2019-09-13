<template>
    <v-card>
        <v-card-title>
            <v-spacer></v-spacer>
            <v-spacer></v-spacer>
            <v-spacer></v-spacer>
            <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
        </v-card-title>
        <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="fileArr" :search="search" :pagination.sync="pagination">
        <template v-slot:items="props">
            <tr v-if="props.item.length != 0">
                <td>
                    <media-asset-play-video :asset="props.item[0]['media_asset']"></media-asset-play-video>
                </td>
                <td class="text-xs-left">{{ props.item[0]['duration'] }}</td> 
                <td class="text-xs-left">{{ props.item.length }}</td>
                <td class="text-xs-left" v-if="is_public">
                    <v-tooltip top >
                        <template v-slot:activator="{ on }">
                            <v-icon color="#01c4ca" v-on="on" dark left 
                            @click="downloadFile(props.item[0]['media_asset'].asset_url,
                            props.item[0]['media_asset'].file_name)" 
                            >fa-file-download</v-icon>
                        </template>
                        <span>Download Asset</span>
                    </v-tooltip>
                    <v-tooltip top>
                        <template v-slot:activator="{ on }">
                            <v-icon color="red" v-on="on" dark left 
                            @click="downloadCertificate(props.item[0]['media_asset'].regulatory_cert_url)">
                            fa-file-download</v-icon>
                        </template>
                        <span>Download certificate</span>
                    </v-tooltip>
                </td>
            </tr>
        </template>
        <template v-slot:no-results>
            <v-alert :value="true" color="error" icon="warning">
            Your search for "{{ search }}" found no results.
            </v-alert>
        </template>
        </v-data-table>
    </v-card>
</template>

<script>
  export default {
        props : {
            files : {
                required : true,
                type : [Object, Array]
            },
            is_public : {
                type : Boolean
            }
        },
        data () {
            return {
                search: '',
                headers: [
                            { text: 'File Name', align: 'left', value: 'station' },
                            { text: 'Duration', value: 'budget' },
                            { text: 'Insertions', value: 'ad_slots' },
                            { text: 'Action', value: 'action', sortable: false , width: '20%'}
                        ],
                pagination: {
                    rowsPerPage: 10
                },
                fileArr: [],
            }
        },
        mounted() {
            this.convertFilesObjToArr();
        },
        methods : {
            convertFilesObjToArr : function(){
                this.fileArr = Object.values(this.files);
            },
            downloadCertificate : function(url) {
                if(url === ""){
                    this.sweet_alert('No regulatory certificate uploaded for this file', 'info');
                    return
                }
                window.open(url, '_blank')
            },
            downloadFile : function(url, file_name) {
                this.sweet_alert('Processing request, please wait...', 'info');
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', file_name); //or any other extension
                document.body.appendChild(link);
                link.click();
            }
        }
    }
</script>
<style>
    tbody tr:hover {
        background-color: transparent !important;
        cursor: pointer;
    }
    tbody:hover {
    background-color: rgba(0, 0, 0, 0.12);
    }
</style>

