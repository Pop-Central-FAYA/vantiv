<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="assets" :search="search" :loading="loading" :no-data-text="noDataText">
      <template v-slot:items="props">
        <td><media-asset-play-video :asset="props.item"></media-asset-play-video></td>
        <td class="text-xs-left">{{ props.item.client.company_name }}</td>
        <td class="text-xs-left">{{ props.item.brand.name }}</td>
        <td class="text-xs-left">{{ props.item.media_type }}</td>
        <td class="text-xs-left">{{ props.item.duration }}</td>
        <td class="justify-center layout px-0">
          <media-asset-delete :asset-id="props.item.id"></media-asset-delete>
        </td>
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
    data () {
      return {
        search: '',
        headers: [
          { text: 'File Name', align: 'left', value: 'file_name' },
          { text: 'Client', value: 'client.company_name' },
          { text: 'Brand', value: 'brand.name' },
          { text: 'Media Type', value: 'media_type' },
          { text: 'Duration (secs)', value: 'duration' },
          { text: 'Actions', value: 'name', sortable: false }
        ],
        loading: true,
        noDataText: 'Processing',
        assets: []
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
        }
    }
  }
</script>