<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
    
    <ad-vendor-view></ad-vendor-view>

    <v-data-table class="custom-vue-table elevation-1" 
                :headers="headers" :items="vendors" :search="search" :loading="loading" :no-data-text="noDataText" 
                :no-results-text="noResultsText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr @click="showDetails(props.item)">
            <td class="text-xs-left">{{ props.item.name }}</td>
            <td class="text-xs-left">{{ props.item.street_address }}, {{ props.item.city }}, {{ props.item.state }}</td>
            <td class="text-xs-left">{{ formatDate(props.item.created_at) }}</td>
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
    data() {
      return {
        //@todo fix this computed values issue, the value in headers should match what is in the template
        headers: [
          { text: 'Name', align: 'left', value: 'name', width: '20%' },
          { text: 'Address', align: 'left', value: 'street_address', width: '60%' },
          { text: 'Creation Date', value: 'created_at', width: '20%' }
        ],
        vendors: [],
        search: '',
        loading: false,
        noDataText: 'No Vendors Available',
        noResultsText: 'No Vendors Available',
        pagination: {
            sortBy: 'created_at',
            descending: true,
            rowsPerPage: 10
        },
        vendorVisible: false,
        currentVendor: null
      }
    },
    created() {
        var self = this;
        Event.$on('vendor-created', function (vendor) {
            self.vendors.push(vendor);
        });
        Event.$on('vendor-updated', function (vendor) {
          idx = self.vendors.findIndex(x => x.id === vendor.id);
          self.vendors[idx] = vendor;
        });
    },
    mounted() {
        console.log('Display Vendors Component mounted.');
        this.getVendors();
    },
    methods: {
        getVendors() {
          this.loading = true;
          axios({
              method: 'get',
              url: '/ad-vendors'
          }).then((res) => {
              this.vendors = res.data.data;
          }).catch((error) => {
              this.vendors = [];
              this.sweet_alert('An unknown error has occurred, vendors cannot be retrieved. Please try again', 'error');
          }).finally(() => this.loading = false);
        },
        showDetails(idx, item) {
          Event.$emit('view-vendor', idx, item);
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