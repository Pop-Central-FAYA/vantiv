<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="campaigns" :search="search" :no-data-text="noDataText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr @click="campaignDetails(props.item.redirect_url)">
          <td class="text-xs-left"><a :href="props.item.redirect_url" class="default-vue-link">{{ props.item.name }}</a></td>
          <td class="text-xs-left">{{ props.item.brand }}</td>
          <td class="text-xs-left">{{ dateToHumanReadable(props.item.start_date) }}</td>
          <td class="text-xs-left">{{ dateToHumanReadable(props.item.end_date) }}</td>
          <td class="text-xs-left">{{ props.item.budget }}</td>
          <td class="text-xs-left">{{ props.item.adslots }}</td>
          <td class="text-xs-left" v-html="props.item.status"></td>
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
      companyType: String,
      campaigns: Array
    },
    data () {
      return {
        search: '',
        headers: [
          { text: 'Name', align: 'left', value: 'name' },
          { text: 'Brands', value: 'brands' },
          { text: 'Total Spend (₦)', value: 'total spend' },
          { text: 'Active Campaigns', value: 'active campaigns' },
          { text: 'Created On', value: 'date_created' }
        ],
        pagination: {
            rowsPerPage: 10,
            sortBy: 'date_created',
            descending: true,
        },
        noDataText: 'No campaign to display'
      }
    },
    mounted() {
        console.log('Display All camapaigns Component mounted.');
    },
    methods: {
      campaignDetails(url) {
        window.location = url;
      }
    }
  }
</script>