<template>
  <v-flex>
    <v-card>
      <p></p>
      <v-layout wrap>
        <v-flex xs12 sm12 md12>
          <v-layout style="height: 80px; margin-Top: 10px; margin-right: -70px;">
            <v-flex md11 my-1>
              <p class="weight_medium">{{ user.name }}</p>
              <p class="weight_medium small_faint">
                <span v-for="(list, index) in user.role_name" v-bind:key="index">
                  <span>{{list.label}}</span>
                  <span v-if="index+1 < user.role_name.length">,</span>
                </span>
              </p>
            </v-flex>
            <v-flex md2 my-1>
              <b-img
                thumbnail
                fluid
                :src="user.avatar"
                style="width: 70px; height: 70px"
                alt="Image 1"
              ></b-img>
            </v-flex>
          </v-layout>
        </v-flex>
      </v-layout>
      <v-divider></v-divider>
      <v-layout wrap>
        <v-flex xs12 sm12 md12>
          <v-layout style="height: 70px">
            <v-flex md4 my-1 style="border-width:2px;">
              <p class="bold small_faint">Company</p>
              <p class="weight_medium">{{ user.company_name}}</p>
            </v-flex>
            <v-divider vertical></v-divider>
            <v-flex md4 my-1>
              <p class="bold small_faint">Email</p>
              <p class="weight_medium">{{ user.email }}</p>
            </v-flex>
            <v-divider vertical></v-divider>
            <v-flex md4 my-1>
              <p class="bold small_faint">Phone</p>
              <p class="weight_medium">{{ user.phone_number }}</p>
            </v-flex>
            <v-divider vertical></v-divider>
          </v-layout>
        </v-flex>
      </v-layout>
    </v-card>
  </v-flex>
</template>

<style>
    tbody tr:hover {
      background-color: transparent !important;
      cursor: pointer;
    }
    tbody:hover {
      background-color: rgba(0, 0, 0, 0.12);
    }
    p {
      margin-bottom: -1px;
      margin-left: 30px;
    }
    .v-tabs {
      background: #fafafa;
    }
    .accent {
      background-color: #01c4ca !important;
      border-color: #01c4ca !important;
    }
    hr {
      margin-top: 0;
      margin-bottom: 0;
    }
</style>

  <script>
    export default {
      props: {
        userData: Object,
        permissionList: Array
      },
      data() {
        return {
          search: "",
          user: this.userData
        };
      },
      created() {
        var self = this;
        Event.$on("user-updated", function(user) {
          self.user = user;
        });
      },
      mounted() {
        console.log("Display user details Component mounted.");
      }
    };
</script>