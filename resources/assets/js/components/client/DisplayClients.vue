<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
     <clients-edit></clients-edit>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="clients" :loading="loading" :search="search" :no-data-text="noDataText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr>
            <td @click="showClientDetails(props.item.id)" class="text-xs-left" >{{ props.item.name }}</td>
            <td @click="showClientDetails(props.item.id)" class="text-xs-left">{{ props.item.number_brands }}</td>
            <td @click="showClientDetails(props.item.id)" class="text-xs-left">{{ formatAmount(props.item.client_spendings)}}</td>
            <td @click="showClientDetails(props.item.id)" class="text-xs-left">{{ props.item.sum_active_campaign }}</td>
            <td @click="showClientDetails(props.item.id)" class="text-xs-left">{{ dateToHumanReadable(props.item.created_at.date) }}</td>
            <td class="justify-center layout px-0">
               <v-tooltip top>
                    <template v-slot:activator="{on}">
                       <v-icon  v-on="on" @click="showEditClient(props.item)" color="#44C1C9" v-b-tooltip.hover title="Edit client" dark right>edit</v-icon>
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
          { text: 'Brands', value: 'number_brands' },
          { text: 'Total Spend (₦)', value: 'client_spendings' },
          { text: 'Active Campaigns', value: 'sum_active_campaign' },
          { text: 'Created On', value: 'created_at.date' },
           { text: 'Actions', value: 'name',  sortable: false}
        ],
        pagination: {
            rowsPerPage: 10,
            sortBy: 'created_at',
            descending: true,
        },
        noDataText: 'No client to display'
      }
    },
     created() {
        var self = this;
        Event.$on('client-created', function (client) {
            self.getClients();
        });
    },
    mounted() {
        console.log('Display All client Component mounted.');
         this.getClients();
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
              this.sweet_alert('An unknown error has occurred, vendors cannot be retrieved. Please try again', 'error');
          }).finally(() => this.loading = false);
        },
         showEditClient(idx, item) {
          Event.$emit('view-client', idx, item);
        },
        showClientDetails(id){
            var url= '/clients/'+id+'/details/';
             window.location = url;
        }
        
    }
  }
</script>