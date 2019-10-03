<template>
  <v-layout>
    <v-dialog v-model="dialog" persistent max-width="800px">
      <template v-slot:activator="{ on }">
        <v-btn color class="default-vue-btn" dark v-on="on">Edit</v-btn>
      </template>
      <v-card>
        <v-card-text>
          <v-container grid-list-md>
            <v-form>
              <v-subheader>Update Profile Information</v-subheader>
              <v-divider></v-divider>
              <v-layout wrap>
                <v-flex xs12 sm6 md6>
                  <v-text-field
                    required
                    :clearable="true"
                    :label="'First Name'"
                    :placeholder="'First Name'"
                    :hint="'Enter your first name'"
                    :solo="true"
                    :single-line="true"
                    v-validate="'required|max:255'"
                    :error-messages="errors.collect('first_name')"
                    v-model="user.firstname"
                    data-vv-name="firstname"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-text-field
                    required
                    :clearable="true"
                    :label="'Last Name'"
                    :placeholder="'Last Name'"
                    :hint="'Enter your last name'"
                    :solo="true"
                    :single-line="true"
                    v-validate="'required|max:255'"
                    :error-messages="errors.collect('last_name')"
                    v-model="user.lastname"
                    data-vv-name="lastname"
                  ></v-text-field>
                </v-flex>
              </v-layout>

              <v-layout wrap>
                <v-flex xs12 sm6 md6>
                  <v-text-field
                    required
                    :label="'Email'"
                    :placeholder="'Email'"
                    :hint="'Enter email'"
                    :solo="true"
                    :single-line="true"
                    v-validate="'required|email'"
                    :error-messages="errors.collect('email')"
                    v-model="user.email"
                    data-vv-name="email"
                    disabled
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-text-field
                    required
                    :clearable="true"
                    :label="'Phone Number'"
                    :placeholder="'Phone Number'"
                    :hint="'Enter your phone number'"
                    :solo="true"
                    :single-line="true"
                    v-validate="'required|numeric'"
                    :error-messages="errors.collect('phone_number')"
                    v-model="user.phone_number"
                    data-vv-name="phone_number"
                  ></v-text-field>
                </v-flex>
              </v-layout>

              <v-layout wrap>
                <v-flex xs12 sm11 md11>
                  <v-text-field
                    solo
                    v-model="avatar_input_label"
                    prepend-icon="attach_file"
                    name="avatar"
                    :error-messages="errors.collect('avatar')"
                    @click="chooseFile()"
                  ></v-text-field>
                  <input
                    type="file"
                    style="display: none"
                    ref="avatar"
                    accept=".png, .jpeg, .jpg"
                    v-validate="'ext:jpg,png,jpeg'"
                    data-vv-name="avatar"
                    @change="onFileChange($event)"
                  >
                </v-flex>
                <v-flex xs12 sm1 md1>
                  <b-img
                    thumbnail
                    fluid
                    :src="user.avatar"
                    style="width: 50px; height: 50px"
                    id="avatar"
                    alt="Image 1"
                  ></b-img>
                </v-flex>
              </v-layout>
              <v-layout wrap>
                <v-flex xs12 sm12 md12 v-show="show_progress_bar">
                  <p class="text-muted">{{ current_upload_title }}</p>
                  <v-progress-linear v-model="upload_percentage" color="green"></v-progress-linear>
                </v-flex>
              </v-layout>
            </v-form>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="red" dark @click="closeDialog()">Close</v-btn>
              <v-btn color class="default-vue-btn" dark @click="editUser()">Save</v-btn>
            </v-card-actions>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<style>
.v-text-field {
  padding-top: 2px;
  margin-top: 0px;
}
.default-vue-btn {
  color: #fff;
  cursor: pointer;
  background: #44c1c9 !important;
  -webkit-appearance: none;
  font-family: "Roboto", sans-serif;
  font-weight: 500;
  border: 0;
  font-size: 15px;
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
  -webkit-box-shadow: 9px 10px 20px 1px rgba(0, 159, 160, 0.21);
  -moz-box-shadow: 9px 10px 20px 1px rgba(0, 159, 160, 0.21);
  box-shadow: 9px 10px 20px 1px rgba(0, 159, 160, 0.21);
  position: relative;
  display: inline-block;
  text-transform: uppercase;
}
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
            user: this.setupModel(),
            editMode: false,
            avatar_file: "",
            avatar_url: "",
            avatar_input_label: "",
            s3_presigned_url: "",
            show_progress_bar: false,
            upload_percentage: 0,
            current_upload_title: "",
            upload_image: false,
            dictionary: {
                custom: {
                lastname: {
                    required: () => "Last name cannot be empty",
                    max: "The company name field may not be greater than 255 characters"
                },
                firstname: {
                    required: () => " First name cannot be empty",
                    max: "The company name field may not be greater than 255 characters"
                },
                email: {
                    required: () => "Email address cannot be empty",
                    email: () => "This must be a valid email address"
                },
                phone_number: {
                    required: () => "Phone number cannot be empty",
                    numeric: () => "Phone number must be all digits"
                },
                avatar: {
                    numeric: () => "The user avatar must be a picture"
                }
                }
            }
            };
        },
        created() {
            console.log("bessss");
            var self = this;
            (self.user = this.userData);
             self.avatar_input_label = self.user.avatar;
          
        },
        mounted() {
            console.log("Edit profile Component mounted.");
            this.$validator.localize("en", this.dictionary);
        },
        methods: {
            setupModel: function() {
            return {
                firstname: "",
                lastname: "",
                avatar: "",
                email: "",
                phone_number: ""
            };
            },
            openDialog: function(item) {
              this.dialog = true;
            },
            closeDialog: function() {
              this.dialog = false;
            },
            editUser: async function(event) {
                self = this;
                this.$validator.validateAll().then(result => {
                    if (result) {
                    return;
                    }
                });

                try {
                    // generate presigned url for ciient logo upload
                    if (this.upload_image) {
                    await this.generatePresignedUrl(this.avatar_file, "user-images/");
                    await this.uploadFile(this.avatar_file, this.s3_presigned_url);
                    }
                    // make axios call to edit created company to db
                    self.makeUpdateRequest();
                } catch (error) {
                    console.log(error);
                    this.sweet_alert(
                    "An unknown error has occurred, logo upload failed. Please try again",
                    "error"
                    );
                }
            },
            makeUpdateRequest: function() {
            this.sweet_alert("Updating user information", "info");
            axios({
                method: "patch",
                url: this.user.links.profile_update,
                data: this.user
            })
                .then(res => {
                this.sweet_alert("User was successfully created", "success");
                Event.$emit("user-updated", res.data.data);
                this.closeDialog();
                })
                .catch(error => {
                this.sweet_alert(
                    "An unknown error has occurred, user cannot be updated. Please try again",
                    "error"
                );
                });
            },
            chooseFile() {
            this.$refs.avatar.click();
            },
            onFileChange(event) {
                this.upload_image = true;
                this.avatar_file = event.target.files[0];
                this.avatar_input_label = this.avatar_file.name;
                console.log(this.avatar_input_label);
                const output = $("#avatar");
                output.attr("src", URL.createObjectURL(event.target.files[0]));
            },
            generatePresignedUrl: async function(file, folder_name) {
            await axios({
                method: "post",
                url: this.routes.presigned_url,
                data: {
                filename: file.name,
                folder: folder_name
                }
            })
                .then(res => {
                    this.s3_presigned_url = res.data;
                    })
                    .catch(error => {
                    console.log(error.response.data);
                    this.s3_presigned_url = "";
                    this.sweet_alert(
                        "An unknown error has occurred, upload failed. Please try again",
                        "error"
                    );
                });
            return this.s3_presigned_url;
            },
            uploadFile: async function(file, presigned_url) {
                this.show_progress_bar = true;
                this.upload_percentage = 0;
                this.current_upload_title = "Uploading Avatar";

                await axios
                    .put(presigned_url, file, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    },
                    onUploadProgress: function(progressEvent) {
                        this.upload_percentage = parseInt(
                        Math.round((progressEvent.loaded * 100) / progressEvent.total)
                        );
                    }.bind(this)
                    })
                    .then(res => {
                        this.show_progress_bar = false;
                        this.user.avatar = `https:${presigned_url.split("?")[0].substr(6)}`;
                    })
                    .catch(error => {
                        console.log(error);
                        this.sweet_alert(
                            "An unknown error has occurred, upload failed. Please try again",
                            "error"
                        );
                    });
            }
        }
    };
</script>