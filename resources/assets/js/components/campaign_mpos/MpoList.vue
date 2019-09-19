<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="mpos" :search="search" :no-data-text="noDataText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr>
          <td class="text-xs-left">{{ props.item.campaign.name }}</td>
          <td class="text-xs-left">{{ props.item.insertions }}</td>
          <td class="text-xs-left">{{ numberFormat(props.item.net_total) }}</td>
          <td class="text-xs-left">{{ props.item.adslots }}</td>
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
    props: {
      mpos: Array,
    },
    data () {
      return {
        search: '',
        headers: this.getHeader(),
        pagination: {
            rowsPerPage: 10,
            sortBy: 'created_at',
            descending: true,
        },
        noDataText: 'No Mpo to display'
      }
    },
    mounted() {
        console.log(this.mpos)
        console.log('Display All mpos Component mounted.');
    },
    methods: {
      campaignDetails(url) {
        window.location = url;
      },
      getHeader(){
        var header =[
          { text: 'Campaign Name', align: 'left', value: 'name' },
          { text: 'Insertions', value: 'insertions' },
          { text: 'Net Total (₦)', value: 'net_total'},
          { text: 'Ad Slots', value: 'adslots' },
          { text: 'Created On', value: 'created_at' }
        ];
        return header
      },
    }
  }
</script>