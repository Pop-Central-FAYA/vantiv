<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <brand-create  :client_id="brands[0].client_id"></brand-create>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
     <clients-edit></clients-edit>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="brands" :loading="loading" :search="search" :no-data-text="noDataText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr>
            <td class="text-xs-left">{{ props.item.name }}</td>
            <td class="text-xs-left">
                  <b-img thumbnail fluid :src="props.item.image_url"  style="width: 40px; height: 40px"  id="brand_logo" alt="Image 1"></b-img>

            </td>
           <td class="text-xs-left">{{ dateToHumanReadable(props.item.created_at.date) }}</td>
           <td class="justify-center layout px-0">
            <v-icon color="#44C1C9" v-b-tooltip.hover title="Edit client" dark right>fa fa-edit</v-icon> </td>
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
         brand_list: Array,
        },
    data () {
      return {
        search: '',
        loading: false,
        brands: this.brand_list,
        headers: [
           { text: 'Name', align: 'left', value: 'name' },
           { text: 'Icon', value: 'created_at', sortable: false},
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
        Event.$on('vendor-updated', function (vendor) {
          idx = self.vendors.findIndex(x => x.id === vendor.id);
          self.vendors[idx] = vendor;
        });
    },
    mounted() {
        console.log('Display All client Component mounted.');
      
    }
  }
</script>