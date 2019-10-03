<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
    <edit-user :roles="roles" :permissionList="permissionList"></edit-user>
    <reinvite-user :roles="roles" :permissionList="permissionList"></reinvite-user>
    <delete-user :roles="roles" :permissionList="permissionList"></delete-user>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-text-field v-model="search" append-icon="search" label="Enter Keyword" single-line hide-details></v-text-field>
    </v-card-title>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="users" :loading="loading" :search="search" :no-data-text="noDataText" :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr>
            <td class="text-xs-left clickable" >{{ props.item.name }}</td>
            <td class="text-xs-left clickable">{{ props.item.email }}</td>
            <td class="text-xs-left clickable"><span v-for="(list, index) in props.item.role_name" v-bind:key="index"> <span>{{list.label}}</span> <span v-if="index+1 < props.item.role_name.length">, </span>  </span>     </td>
            <td class="text-xs-left">{{ props.item.status}} </td>
            <td class="text-xs-left clickable">{{ dateToHumanReadable(props.item.created_at) }}</td>
            <td class="justify-lefr layout px-0 ">
               <v-tooltip top>
                    <template v-slot:activator="{on}">
                       <v-icon  v-on="on" @click="showEditUser(props.item)" color="#44C1C9" v-b-tooltip.hover title="Edit user" dark right>edit</v-icon>
                     </template>
                    <span>Edit the user information</span>
                </v-tooltip>
                   <v-tooltip top v-if="props.item.status== 'Unconfirmed'">
                    <template v-slot:activator="{on}">
                       <v-icon  v-on="on" @click="showResend(props.item)" color="#44C1C9" v-b-tooltip.hover title="Resend invitation" dark right>send</v-icon>
                     </template>
                    <span>Edit the user information</span>
                   </v-tooltip>
                     <v-tooltip top v-if="props.item.status== 'Unconfirmed'">
                    <template v-slot:activator="{on}">
                       <v-icon  v-on="on" @click="showDeleteUser(props.item)" color="red" v-b-tooltip.hover title="Delete user" dark right>delete</v-icon>
                     </template>
                    <span>Edit the user information</span>
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
  table.v-table tbody tr td.clickable {
    pointer-events: none;
}
  .v-text-field {
        padding-top: 2px;
        margin-top: 0px;
    }
</style>

<script>
  export default {
     props: {
        roles:Array,
        permissionList:Array,
        routes:Object,
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
          { text: 'Status', value: 'status' },
          { text: 'Created On', value: 'created_at' },
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
        Event.$on('user-created', function (user) {
           self.users.push(user);
        });
         Event.$on('user-deleted', function (user) {
           const filteredArray =  self.users.filter(element => element.id !== user)
           self.users =filteredArray;
        });
    },
    mounted() {
        console.log('Display All user Component mounted.');
         this.getUsers();
    },
    methods: {
       getUsers() {
          this.loading = true;
          axios({
              method: 'get',
              url: this.routes.list
          }).then((res) => {
              this.users = res.data.data;
          }).catch((error) => {
              this.sweet_alert('An unknown error has occurred. Please try again', 'error');
          }).finally(() => this.loading = false);
        },
        showEditUser(idx, item) {
          Event.$emit('edit-user', idx, item);
        },
        showResend(idx, item){
           Event.$emit('reinvite-user', idx, item); 
        },
         showDeleteUser(idx, item) {
          Event.$emit('delete-user', idx, item);
        },
        
    }
  }
</script>