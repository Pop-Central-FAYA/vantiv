<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
    
    <v-data-table class="custom-vue-table elevation-1" 
                :headers="headers" :items="vendors" :search="search" :loading="loading" :no-data-text="noDataText" 
                :no-results-text="noResultsText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr @click="showDetails(props.item)">
            <td class="text-xs-left">{{ props.item.name }}</td>
            <td class="text-xs-left">{{ props.item.street_address }}, {{ props.item.city }}, {{ props.item.state }}</td>
            <td class="text-xs-left">{{ props.item.publishers.length }}</td>
            <td class="text-xs-left">{{ formatDate(props.item.created_at) }}</td>
            <td class="justify-center layout px-0">
                <v-tooltip top>
                    <template v-slot:activator="{on}">
                        <v-icon color="primary" v-on="on" dark left @click="editVendor(props.item)">edit</v-icon>
                    </template>
                    <span>Edit the vendor information</span>
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
    props: {
      routes: Object
    },
    data() {
      return {
        //@todo fix this computed values issue, the value in headers should match what is in the template
        headers: [
          { text: 'Name', align: 'left', value: 'name', width: '20%' },
          { text: 'Address', align: 'left', value: 'street_address', width: '40%' },
          { text: 'Num Publishers', align: 'left', value: 'publishers', width: '19%', sortable: false },
          { text: 'Creation Date', align: 'left', value: 'created_at', width: '20%' },
          { text: 'Actions', value: 'name', width: '1%', sortable: false }
        ],
        vendors: [],
        search: '',
        loading: false,
        noDataText: 'No Vendors Available',
        noResultsText: 'No Vendors Available',
        pagination: {sortBy: 'created_at', descending: true, rowsPerPage: 10},
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
          var idx = self.vendors.findIndex(x => x.id === vendor.id);
          if (idx >= 0) {
            self.vendors[idx] = vendor;
          }
        });
    },
    mounted() {
        console.log('Display vendors component mounted.');
        this.getVendors();
    },
    methods: {
        getVendors() {
          this.loading = true;
          axios({
              method: 'get',
              url: this.routes.list
          }).then((res) => {
              this.vendors = res.data.data;
              console.log(res.data.data);
          }).catch((error) => {
              this.vendors = [];
              this.sweet_alert('An unknown error has occurred, vendors cannot be retrieved. Please try again', 'error');
          }).finally(() => this.loading = false);
        },
        showDetails(item) {
           window.location =item.links.details;
        },
        editVendor(item) {
          Event.$emit('edit-vendor', item);
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