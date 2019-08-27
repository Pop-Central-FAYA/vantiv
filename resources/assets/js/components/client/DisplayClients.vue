<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="clients" :loading="loading" :search="search" :no-data-text="noDataText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr @click="showDetails(props.item)">
            <td class="text-xs-left">{{ props.item.name }}</td>
            <td class="text-xs-left">{{ props.item.number_brands }}</td>
            <td class="text-xs-left">{{ props.item.sum_active_campaign }}</td>
            <td class="text-xs-left">{{ props.item.client_spendings}}</td>
          <td class="text-xs-left">{{ dateToHumanReadable(props.item.created_at) }}</td>
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

<style>
  tbody tr:hover {
    background-color: transparent !important;
    cursor: pointer;
  }
  tbody:hover {
    background-color: rgba(0, 0, 0, 0.12);
  }
</style>

<script>
  export default {
    data () {
      return {
        clients: [],
        search: '',
        loading: false,
        headers: [
          { text: 'Name', align: 'left', value: 'name' },
          { text: 'Brands', value: 'brands' },
          { text: 'Total Spend (₦)', value: 'total spend' },
          { text: 'Active Campaigns', value: 'active campaigns' },
          { text: 'Created On', value: 'date_created' }
        ],
        pagination: {
            rowsPerPage: 10,
            sortBy: 'created_at',
            descending: true,
        },
        noDataText: 'No client to display'
      }
    },
    mounted() {
        console.log('Display All client Component mounted.');
         this.getClients();
         console.log(this.clients);
    },
    methods: {
       getClients() {
          this.loading = true;
          axios({
              method: 'get',
              url: '/clients'
          }).then((res) => {
              this.clients = res.data.data;
          }).catch((error) => {
              this.clients = [];
              this.sweet_alert('An unknown error has occurred, vendors cannot be retrieved. Please try again', 'error');
          }).finally(() => this.loading = false);
        },
         showDetails(idx, item) {
           console.log(idx);
          Event.$emit('view-client', idx, item);
        }
    }
  }
</script>