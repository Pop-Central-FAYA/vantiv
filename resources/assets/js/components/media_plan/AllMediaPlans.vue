<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="allPlans" :search="search" :no-data-text="noDataText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr @click="redirectPlan(props.item)">
          <td class="text-xs-left"><a :href="props.item.redirect_url" class="default-vue-link">{{ props.item.campaign_name }}</a></td>
          <td class="text-xs-left">{{ (props.item.product_name) ? props.item.product_name:'NA' }}</td>
          <td class="text-xs-left">{{ (props.item.brand) ? props.item.brand.name:'NA' }}</td>
          <td class="text-xs-left">{{ props.item.media_type }}</td>
          <td class="text-xs-left">{{ dateToHumanReadable(props.item.start_date)+" - "+dateToHumanReadable(props.item.end_date) }}</td>
          <td class="text-xs-left">{{ formatNumber(props.item.net_media_cost) }}</td>
          <td class="text-xs-left" :style="{ 'color' : getStatusColor(props.item.status)}">{{ capitalizeFirstletter(props.item.status) }}</td>
          <td class="text-xs-left">{{ dateToHumanReadable(props.item.date_created) }}</td>
          <td class="justify-center layout px-0">
            <media-plan-delete 
              :plan="props.item"
            ></media-plan-delete>
            <!-- <clone-media-plan 
              :plan="props.item"
              :clients="clients"
            ></clone-media-plan> -->
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
  import ClonePlan from "./ClonePlan.vue";
  import DeletePlan from "./DeletePlan.vue";
  export default {
    props: {
      plans: Array,
      clients: Array,
    },
    components: {
        'clone-media-plan': ClonePlan,
        'media-plan-delete': DeletePlan,
    },
    data () {
      return {
        search: '',
        allPlans: this.plans,
        headers: [
          { text: 'Name', align: 'left', value: 'campaign_name' },
          { text: 'Product', align: 'left', value: 'product_name' },
          { text: 'Brand', align: 'left', value: 'brand' },
          { text: 'Media Type', value: 'media_type' },
          { text: 'Flight Date', value: 'start_date' },
          { text: 'Net Media Cost (₦)', value: 'start_date' },
          { text: 'Status', value: 'status' },
          { text: 'Created On', value: 'date_created' },
          { text: 'Actions', value: 'campaign_name', sortable: false }
        ],
        pagination: {
            rowsPerPage: 10,
            sortBy: 'date_created',
            descending: true,
        },
        noDataText: 'No media plan was found'
      }
    },
    created() {
        var self = this;
        Event.$on('media-plan-added', function (new_plan) {
            self.allPlans.push(new_plan);
        });

        Event.$on('media-plan-deleted', function (plan_id) {
          self.allPlans = self.allPlans.filter(function( plan ) {
            return plan.id !== plan_id;
          });
        });
    },
    mounted() {
        console.log('Display All media plans Component mounted.');
        console.log(this.allPlans);
    },
    methods: {
      goToPlanActivities(url) {
        window.location = url;
      },
      redirectPlan(plan) {
        if (this.isMediaPlanPastReviewStage(plan.status)) {
          window.location = plan.routes.summary.index;
        } else {
          window.location = plan.routes.suggestions.index;
        }
      },
      getStatusColor(status) {
        var status = status.toLowerCase();
        if (status == 'pending' || status == 'in review' || status == 'finalized') {
          return 'orange';
        } else if (status == 'converted' || status == 'approved') {
          return 'green';
        } else if (status == 'rejected') {
          return 'red';
        } else {
          return 'grey';
        }
      }
    }
  }
</script>