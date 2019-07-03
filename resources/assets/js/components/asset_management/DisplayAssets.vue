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
        <td>{{ props.item.file_name }}</td>
        <td class="text-xs-left">{{ props.item.client.company_name }}</td>
        <td class="text-xs-left">{{ props.item.brand.name }}</td>
        <td class="text-xs-left">{{ props.item.media_type }}</td>
        <td class="text-xs-left">{{ props.item.duration }}</td>
        <td class="justify-center layout px-0">
            <v-btn color="red" small @click="delete_asset(props.item.id)" dark>
                Delete <v-icon dark right>delete</v-icon>
            </v-btn>
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
        delete_asset(assetID) {
          $("#load_this").css({ opacity: 0.2 });
          var msg = "Deleting media asset, please wait";
          this.sweet_alert(msg, 'info');
          axios({
              method: 'get',
              url: '/media-assets/delete/'+assetID
          }).then((res) => {
              console.log(res.data);
              if (res.data.status == 'success') {
                $('#load_this_div').css({opacity: 1});
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