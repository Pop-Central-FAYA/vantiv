<template>
  <v-container grid-list-md>
    <v-form>
      <span>Change Password</span>
      <v-divider></v-divider>
      <v-layout wrap>
        <v-flex xs12 sm12 md12>
          <v-flex xs12 sm12 md12>
            <v-text-field
              required=""
              v-validate="'required|max:255'"
              name="password"
              hint="At least 8 characters"
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
              required=""
              v-validate="'required|max:255'"
              name="confirm_password"
              solo
              hint="At least 8 characters"
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
      <v-layout wrap>
        <v-flex align-end class="text-md-center">
          <v-btn large class="default-vue-btn" @click="updatePassword()" dark>Update Password</v-btn>
        </v-flex>
      </v-layout>
       <v-layout wrap>
        <v-flex align-end class="text-md-center">
            <span> <a href="/login"> Sign in</a></span>
        </v-flex>
      </v-layout>
    </v-form>
  </v-container>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>
.v-text-field .v-input__slot {
  padding: 0px 12px;
  min-height: 45px;
  margin-bottom: 0px;
  border: 1px solid #ccc;
  border-radius: 5px;
  /* box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12); */
}
.v-text-field > .v-input__control > .v-input__slot:after,
.v-text-field > .v-input__control > .v-input__slot:before {
  content: none;
}
.theme--dark.v-btn:not(.v-btn--icon):not(.v-btn--flat) {
    background-color: #01c4ca;
}
</style>

<script>
export default {
  props: {
        token: String,
        permissionList: Array,
        routes: Object
  },
  data() {
    return {
      user: {
        token: this.token,
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
                    max: "The password name field may not be greater than 255 characters"
                },
                confirm_password: {
                    required: () => " Confirm password cannot be empty",
                    max: "The  confirm password field may not be greater than 255 characters"
                },
                }
            }
    };
  },
  mounted() {
    console.log("Change password form Component mounted.");
  },
  created() {},

  methods: {
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
              console.log(this.user);
            axios({
                method: "post",
                url: this.routes.change_password,
                data: this.user
            })
            .then(res => {
                    this.sweet_alert("Password updat was successfull", "success");
                })
            .catch(error => {
                 if (error.response && (error.response.status == 422)) {
                            this.displayServerValidationErrors(error.response.data.errors);
                        } else {
                            this.sweet_alert('An unknown error has occurred. Please try again', 'error');
                        }
                });
         },
  }
};
</script>