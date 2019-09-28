<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="assets" :search="search" :loading="loading" :no-data-text="noDataText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr>
          <td class="text-xs-left" @click="openPlayModal(props.item)">{{ props.item.file_name }}</td>
          <td class="text-xs-left" @click="openPlayModal(props.item)">{{ props.item.client.name }}</td>
          <td class="text-xs-left" @click="openPlayModal(props.item)">{{ props.item.brand.name }}</td>
          <td class="text-xs-left" @click="openPlayModal(props.item)">{{ props.item.media_type }}</td>
          <td class="text-xs-left" @click="openPlayModal(props.item)">{{ props.item.duration }}</td>
          <td class="text-xs-left" @click="openPlayModal(props.item)">{{ formatDate(props.item.created_at) }}</td>
          <td class="justify-center layout px-0">
            <media-asset-delete :asset="props.item"></media-asset-delete>
          </td>
        </tr>
      </template>
      <template v-slot:no-results>
        <v-alert :value="true" color="error" icon="warning">
          Your search for "{{ search }}" found no results.
        </v-alert>
      </template>
    </v-data-table>
    <media-asset-play-video :asset="currentAsset"></media-asset-play-video>
  </v-card>
</template>

<script>
  export default {
    data () {
      return {
        search: '',
        headers: [
          { text: 'File Name', align: 'left', value: 'file_name', width: '46%' },
          { text: 'Client', value: 'client.name', width: '25%' },
          { text: 'Brand', value: 'brand.name', width: '25%' },
          { text: 'Media Type', value: 'media_type', width: '1%' },
          { text: 'Duration', value: 'duration', width: '1%' },
          { text: 'Upload Date', value: 'created_at', width: '1%' },
          { text: 'Actions', value: 'name', width: '1%', sortable: false }
        ],
        pagination: {
            rowsPerPage: 10
        },
        loading: true,
        noDataText: 'Processing',
        assets: [],
        currentAsset : {}
      }
    },
    created() {
        var self = this;
        Event.$on('latest-assets', function (assets) {
            self.assets = assets;
        });
    },
    mounted() {
        console.log('Display Assets Component mounted.');
        this.get_assets();
    },
    methods: {
        get_assets() {
          this.loading = true;
          this.noDataLoading = "Processing";
          axios({
              method: 'get',
              url: '/media-assets/all'
          }).then((res) => {
              this.loading = false;
              let result = res.data.data;
              if (result.length === 0) {
                  this.noDataText = "No data available";
                  this.sweet_alert('No Media asset was found', 'info');
                  return;
              } else {
                  this.assets = result;
              }
          }).catch((error) => {
              this.assets = [];
              this.sweet_alert('An unknown error has occurred, assets cannot be retrieved. Please try again', 'error');
          });
        },
        openPlayModal : function(asset) {
          this.currentAsset = asset
          Event.$emit('play-modal', true)
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