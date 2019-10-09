<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="plans" :search="search" :no-data-text="noDataText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr @click="goToPlanActivities(props.item.redirect_url)">
          <td class="text-xs-left"><a :href="props.item.redirect_url" class="default-vue-link">{{ props.item.campaign_name }}</a></td>
          <td class="text-xs-left">{{ (props.item.product_name) ? props.item.product_name:'NA' }}</td>
          <td class="text-xs-left">{{ (props.item.brand) ? props.item.brand.name:'NA' }}</td>
          <td class="text-xs-left">{{ props.item.media_type }}</td>
          <td class="text-xs-left">{{ props.item.start_date+" - "+props.item.end_date }}</td>
          <td class="text-xs-left">{{ formatAmount(props.item.net_media_cost) }}</td>
          <td class="text-xs-left" v-html="props.item.status"></td>
          <td class="text-xs-left">{{ props.item.date_created }}</td>
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
      plans: Array
    },
    data () {
      return {
        search: '',
        headers: [
          { text: 'Name', align: 'left', value: 'campaign_name' },
          { text: 'Product', align: 'left', value: 'product_name' },
          { text: 'Brand', align: 'left', value: 'brand' },
          { text: 'Media Type', value: 'media_type' },
          { text: 'Flight Date', value: 'start_date' },
          { text: 'Net Media Cost', value: 'start_date' },
          { text: 'Status', value: 'status' },
          { text: 'Created On', value: 'date_created' }
        ],
        pagination: {
            rowsPerPage: 10,
            sortBy: 'date_created',
            descending: true,
        },
        noDataText: 'No media plan was found'
      }
    },
    mounted() {
        console.log('Display All media plans Component mounted.');
        console.log(this.plans);
    },
    methods: {
      goToPlanActivities(url) {
        window.location = url;
      }
    }
  }
</script>