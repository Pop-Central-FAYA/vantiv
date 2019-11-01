<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="campaigns" :search="search" 
                :loading="loading" :no-data-text="noDataText" :no-results-text="noResultsText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr @click="campaignDetails(props.item.redirect_url)">
          <td class="text-xs-left"><a :href="props.item.redirect_url" class="default-vue-link">{{ props.item.name }}</a></td>
          <td class="text-xs-left">{{ props.item.brand }}</td>
            <td class="text-xs-left">{{ props.item.product }}</td>
          <td class="text-xs-left">{{ dateToHumanReadable(props.item.start_date) }}</td>
          <td class="text-xs-left">{{ dateToHumanReadable(props.item.end_date) }}</td>
          <td class="text-xs-left">{{ formatAmount(props.item.budget) }}</td>
          <td class="text-xs-left">{{ props.item.adslots }}</td>
          <td class="text-xs-left" :style="{ 'color' : statusColor(props.item.status)}">{{ props.item.status }}</td>
          <td class="text-xs-left">{{ dateToHumanReadable(props.item.date_created) }}</td>
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
    props: {
      routes: Object,
    },
    data () {
      return {
        search: '',
        loading: false,
        noDataText: 'No Campaigns Available',
        noResultsText: 'No Campaigns Available',
        campaigns : [],
        headers: this.getHeader(),
        pagination: {
            rowsPerPage: 10,
            sortBy: 'date_created',
            descending: true,
        },
      }
    },
    mounted() {
      this.getCampaigns()
      console.log('Display All camapaigns Component mounted.');
    },
    methods: {
      campaignDetails(url) {
        window.location = url;
      },
      getHeader(){
        var header =[
          { text: 'Name', align: 'left', value: 'name' },
          { text: 'Brand', value: 'brand' },
          { text: 'Product', value: 'product' },
          { text: 'Start Date', value: 'start_date' },
          { text: 'End Date', value: 'end_date' },
          { text: 'Budget (₦)', value: 'budget' },
          { text: 'Ad Slots', value: 'adslots' },
          { text: 'Status', value: 'status' },
          { text: 'Created On', value: 'date_created' }
        ];
        return header
      },
      statusColor : function(status) {
          var status = status.toLowerCase()
          switch (status) {
              case 'active':
                  return 'green'
                  break;
              case 'pending':
                  return 'orange'
                  break;
              default :
                  return 'grey'
          }
      },
      getCampaigns() {
        this.loading = true;
        axios({
            method: 'get',
            url: this.routes.list
        }).then((res) => {
            this.campaigns = res.data.data;
            console.log(res.data.data);
        }).catch((error) => {
            this.vendors = [];
            this.sweet_alert('An unknown error has occurred, campaigns cannot be retrieved. Please try again', 'error');
        }).finally(() => this.loading = false);
      },
    }
  }
</script>