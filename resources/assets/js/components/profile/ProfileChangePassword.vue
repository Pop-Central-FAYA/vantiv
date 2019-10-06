<template>
  <v-dialog v-model="dialog" scrollable persistent max-width="600px" data-app>
    <template v-slot:activator="{ on }">
      <v-btn color class="default-vue-btn" dark v-on="on">Update Password</v-btn>
    </template>
    <v-card>
      <v-card-text>
        <v-container grid-list-md>
          <v-form>
            <v-subheader>Update Password</v-subheader>
            <v-divider></v-divider>
            <v-layout wrap>
              <v-flex xs12 sm12 md12>
                <v-flex xs12 sm12 md12>
                  <v-text-field
                    required
                    v-validate="'required|max:255'"
                    name="password"
                    hint="At least 6 characters"
                    v-model="user.password"
                    :append-icon="show ? 'visibility' : 'visibility_off'"
                    :type="show ? 'text' : 'password'"
                    @click:append="show = !show"
                    label="Password"
                    solo
                    :error-messages="errors.collect('password')"
                  ></v-text-field>
                </v-flex>
              </v-flex>
            </v-layout>
            <v-layout wrap>
              <v-flex xs12 sm12 md12>
                <v-flex xs12 sm12 md12>
                  <v-text-field
                    required
                    v-validate="'required|max:255'"
                    name="confirm_password"
                    solo
                    hint="At least 6 characters"
                    v-model="user.confirm_password"
                    :append-icon="show1 ? 'visibility' : 'visibility_off'"
                    :type="show1 ? 'text' : 'password'"
                    @click:append="show1 = !show1"
                    label="Confirm password"
                    :error-messages="errors.collect('confirm_password')"
                  ></v-text-field>
                </v-flex>
              </v-flex>
            </v-layout>
          </v-form>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="red" dark @click="closeDialog()">Close</v-btn>
            <v-btn color class="default-vue-btn" dark @click="updatePassword()">Update</v-btn>
          </v-card-actions>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<style>
</style>

<script>
export default {
  props: {
    userData: Object,
    permissionList: Array,
    routes: Object
  },
  data() {
    return {
      dialog: false,
      user: {
        password: "",
        confirm_password: ""
      },
      show1: false,
      show: false,
      password: "Password",
      dictionary: {
        custom: {
          password: {
            required: () => "Password cannot be empty",
            max:
              "The password name field may not be greater than 255 characters"
          },
          confirm_password: {
            required: () => " Confirm password cannot be empty",
            max:
              "The  confirm password field may not be greater than 255 characters"
          }
        }
      }
    };
  },
  created() {
   var self = this;
    (self.user = this.userData);
  },
  mounted() {
    console.log("edit user Component mounted.");
  },
  methods: {
    openDialog: function(item) {
      this.dialog = true;
    },
    closeDialog: function() {
      this.dialog = false;
    },
    updatePassword: async function(event) {
      self = this;
      this.$validator.validateAll().then(result => {
        if (result) {
          return;
        }
      });

      this.sweet_alert("Updating user password", "info");
      // make axios call to update password to db
      self.makeUpdateRequest();
    },
    makeUpdateRequest: function() {
      this.sweet_alert("Updating user password", "info");
      this.user.id = this.userData.id;
      console.log(this.user);
      axios({
        method: "post",
        url: this.routes.change_password,
        data: this.user
      })
        .then(res => {
          this.closeDialog();
          this.sweet_alert("Password updat was successfull", "success");
        })
        .catch(error => {
          if (error.response && error.response.status == 422) {
            this.displayServerValidationErrors(error.response.data.errors);
          } else {
            this.sweet_alert(
              "An unknown error has occurred. Please try again",
              "error"
            );
          }
        });
    }
  }
};
</script>