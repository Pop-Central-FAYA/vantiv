<template>
  <v-card>
   
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
    </v-card-title>
      <v-layout wrap>
        <v-flex xs12 sm6 md6>
        </v-flex>
        <v-flex xs12 sm4 md4>
              <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
        </v-flex>
        <v-flex xs12 sm2 md2>
            <brand-create  :client_id="client_id" fixed left></brand-create>
        </v-flex>
                                 
       </v-layout>
     <brands-edit></brands-edit>
    <v-data-table :headers="headers" :items="brands" :loading="loading" :search="search" :no-data-text="noDataText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr>
            <td class="text-xs-left clickable"><b-img thumbnail fluid :src="props.item.image_url" class="v-icon notranslate v-icon--left mdi mdi-laptop theme--light" style="width: 30px; height: 30px"  id="brand_logo" alt="Image 1"></b-img> <span class="text1"> {{ props.item.name }}</span>
            </td>
            <td class="text-xs-left clickable"> {{props.item.campaigns_count}} </td>
             <td class="text-xs-left clickable"> {{formatAmount(props.item.campaigns_spendings)}} </td>
           <td class="text-xs-left clickable">{{ dateToHumanReadable(props.item.created_at.date) }}</td>
           <td class="justify-center layout px-0">
            <v-icon color="#44C1C9" @click="showEditBrand(props.item)"  v-b-tooltip.hover title="Edit client" dark right>edit</v-icon> </td>
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
table.v-table tbody tr td.clickable {
    pointer-events: none;
}
 .img-valign {
  vertical-align: middle;
  }
hr {
    margin-top: 0;
    margin-bottom: 0;
    border: 0;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

</style>

<script>
  export default {
       props: {
         brand_list: Array,
          client_id: String,
        },
    data () {
      return {
        search: '',
        loading: false,
        brands: this.brand_list,
        headers: [
           { text: 'Name', align: 'left', value: 'name' },
           { text: 'All Campaigns', value: 'all'},
             { text: 'Total Expense (₦)', value: 'expense' },
            { text: 'Created On', value: 'created_at' },
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
        Event.$on('brand-created', function (brand) {
           self.brands.push(brand);
        });
        Event.$on('brand-updated', function (vendor) {
          idx = self.brands.findIndex(x => x.id === brands.id);
          self.brands[idx] = brands;
        });
    },
    mounted() {
      console.log(this.client_id);
        console.log('Display All Brands Component mounted.');
      
    }, methods: {
         showEditBrand(idx, item) {
          Event.$emit('edit-brand', idx, item);
        }
    }
  }
</script>