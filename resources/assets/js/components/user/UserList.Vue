<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
     <edit-user :roles="roles"></edit-user>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="users" :loading="loading" :search="search" :no-data-text="noDataText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr>
            <td @click="showClientDetails(props.item.id)" class="text-xs-left" >{{ props.item.name }}</td>
            <td @click="showClientDetails(props.item.id)" class="text-xs-left">{{ props.item.email }}</td>
            <td @click="showClientDetails(props.item.id)" class="text-xs-left"><span v-for="(list, index) in props.item.roles" v-bind:key="index"> <span>{{list}}</span> <span v-if="index+1 < props.item.roles.length">, </span>  </span>     </td>
            <td @click="showClientDetails(props.item.id)" class="text-xs-left">{{ props.item.status}}</td>
            <td @click="showClientDetails(props.item.id)" class="text-xs-left">{{ dateToHumanReadable(props.item.created_at.date) }}</td>
            <td class="justify-center layout px-0">
               <v-tooltip top>
                    <template v-slot:activator="{on}">
                       <v-icon  v-on="on" @click="showEditUser(props.item)" color="#44C1C9" v-b-tooltip.hover title="Edit client" dark right>edit</v-icon>
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
     props: {
        roles:Array,
        permissionList:Array
        },
    data () {
      return {
        users: [],
        search: '',
        loading: false,
        headers: [
          { text: 'Name', align: 'left', value: 'name' },
          { text: 'Email', value: 'email' },
          { text: 'Role(s)', value: 'roles' },
          { text: 'Status', value: 'sum_active_campaign' },
          { text: 'Created On', value: 'created_at.date' },
          { text: 'Actions', value: 'name',  sortable: false}
        ],
        pagination: {
            rowsPerPage: 10,
            sortBy: 'created_at',
            descending: true,
        },
        noDataText: 'No user to display'
      }
    },
     created() {
        var self = this;
        Event.$on('user-created', function (client) {
            self.getUsers();
        });
    },
    mounted() {
        console.log('Display All client Component mounted.');
         this.getUsers();
    },
    methods: {
       getUsers() {
          this.loading = true;
          axios({
              method: 'get',
              url: '/users'
          }).then((res) => {
              this.users = res.data.data;
              console.log(this.users)
          }).catch((error) => {
              this.sweet_alert('An unknown error has occurred, vendors cannot be retrieved. Please try again', 'error');
          }).finally(() => this.loading = false);
        },

        showEditUser(idx, item) {
          Event.$emit('edit-user', idx, item);
        },
        showClientDetails(id){
            var url= '/clients/'+id+'/details/';
             window.location = url;
        }
        
    }
  }
</script>