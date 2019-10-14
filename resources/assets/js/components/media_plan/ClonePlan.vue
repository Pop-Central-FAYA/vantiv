<template>
  <v-layout>
    <v-dialog v-model="dialog" persistent max-width="600px">
        <template v-slot:activator="{ on }">
            <v-icon :disabled="!isMediaPlanPastReviewStage(plan.status)" color="green" dark v-on="on" right>new_releases</v-icon>
        </template>
      <v-card>
        <v-card-text>
            <v-container grid-list-md>
                <v-layout wrap>
                    <v-flex xs12 sm6 md6>
                        <span>Campaign Name:</span>
                        <v-flex xs12 sm12 md12>
                        <v-text-field v-validate="'required'" name="campaign_name" placeholder="Enter Campaign Name" v-model="selectedValues.campaign_name"></v-text-field>
                        </v-flex>
                        <span class="text-danger" v-show="errors.has('campaign_name')">
                            Campaign name is required
                        </span>
                    </v-flex>
                    <v-flex xs12 sm6 md6>
                        <span>Product Name:</span>
                        <v-text-field name="product" placeholder="Product name" v-model="selectedValues.product" v-validate="'required'"></v-text-field>
                        <span class="text-danger" v-show="errors.has('product')">{{ errors.first('product') }}</span>
                    </v-flex>
                </v-layout>
                <v-layout wrap>
                    <v-flex xs12 sm6 md6>
                        <span>Start Date:</span>
                        <v-menu
                        v-model="startDateMenu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            lazy
                            transition="scale-transition"
                            offset-y
                            full-width
                            max-width="290px"
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on }">
                                <v-text-field
                                v-model="computedStartDateFormatted"
                                name="start_date"
                                ref="startDate"
                                v-validate="'required|date_format:dd/MM/yyyy|after:'+today"
                                placeholder="DD/MM/YYYY"
                                v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker v-model="selectedValues.start_date" no-title @input="startDateMenu = false"></v-date-picker>
                        </v-menu>
                        <span class="text-danger" v-show="errors.has('start_date')">
                            Start date is required and must be greater than today
                        </span>
                    </v-flex>
                    <v-flex xs12 sm6 md6>
                        <span>End Date:</span>
                        <v-menu
                        v-model="endDateMenu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            lazy
                            transition="scale-transition"
                            offset-y
                            full-width
                            max-width="290px"
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on }">
                                <v-text-field
                                v-model="computedEndDateFormatted"
                                name="end_date"
                                v-validate="'required|date_format:dd/MM/yyyy|after:startDate'"
                                placeholder="DD/MM/YYYY"
                                v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker v-model="selectedValues.end_date" no-title @input="endDateMenu = false"></v-date-picker>
                        </v-menu>
                        <span class="text-danger" v-show="errors.has('end_date')">
                            End date is required and must be greater than start date
                        </span>
                    </v-flex>
                </v-layout>
                <v-layout wrap>
                    <v-flex xs12 sm6 md6>
                        <span>Client:</span>
                        <v-select
                            v-model="selectedValues.client"
                            :items="clients"
                            item-text="name"
                            item-value="id"
                            v-validate="'required'"
                            name="client"
                            @change="getBrands"
                            placeholder="Please select client"
                        ></v-select>
                        <span class="text-danger" v-show="errors.has('client')">{{ errors.first('client') }}</span>
                    </v-flex>
                    <v-flex xs12 sm6 md6>
                        <span>Brand:</span>
                        <v-select
                            v-model="selectedValues.brand"
                            :items="filteredBrands"
                            item-text="name"
                            item-value="id"
                            v-validate="'required'"
                            name="brand"
                            placeholder="Please select brand"
                        ></v-select>
                        <span class="text-danger" v-show="errors.has('brand')">{{ errors.first('brand') }}</span>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red" dark @click="dialog = false">Cancel</v-btn>
          <v-btn class="default-vue-btn" dark @click="clonePlan()">Clone</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<style>
    .v-text-field .v-input__slot {
        padding: 0px 12px;
        min-height: 45px;
        margin-bottom: 0px;
        border: 1px solid #ccc;
        border-radius: 5px;
        /* box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12); */
    }
    .v-text-field>.v-input__control>.v-input__slot:after, .v-text-field>.v-input__control>.v-input__slot:before {
        content: none;
    }
    .v-date-picker-table {
        height: 100% !important;
    }
    .multiselect__tags {
        min-height: 45px;
        border: 1px solid #ccc;
        margin-top: 16px;
    }
    .theme--dark.v-btn.v-btn--disabled:not(.v-btn--icon):not(.v-btn--flat):not(.v-btn--outline) {
        background-color: hsl(184, 55%, 53%)!important;
    }
    .v-icon.v-icon--disabled.v-icon--link.v-icon--right.material-icons.theme--dark.green--text {
        color: hsl(122, 39%, 49%)!important;
    }
</style>

<script>
  export default {
    props : {
      plan: Object,
      clients: Array
    },
    data() {
      return {
          dialog: false,
          dateFormatted: this.formatDate(new Date().toISOString().substr(0, 10)),
          today: this.formatDate(new Date().toISOString().substr(0, 10)),
          startDateMenu: false,
          endDateMenu: false,
          filteredBrands: [],
          selectedValues: {
              start_date: '',
              end_date: '',
              campaign_name: '',
              client: '',
              brand: '',
              product: '',
          },
      }
    },
    mounted() {
      console.log('Clone Plan Component mounted.');
    },
    computed: {
        computedStartDateFormatted () {
            return this.formatDate(this.selectedValues.start_date)
        },
        computedEndDateFormatted () {
            return this.formatDate(this.selectedValues.end_date)
        }
    },
    methods: {
        formatDate(date) {
            if (!date) return null
            const [year, month, day] = date.split('-')
            return `${day}/${month}/${year}`
        },
        getBrands() {
            var client = this.selectedValues.client;
            var filtered = this.clients.filter(function(clients) {
                    return clients.id === client;
                });
            this.filteredBrands = filtered[0].brands;
        },
        clonePlan() {
            this.$validator.validate().then(valid => {
                if (valid) {
                    $("#load_this").css({ opacity: 0.2 });
                    var msg = "Cloning Media Plan, please wait";
                    this.sweet_alert(msg, 'info');
                    axios({
                        method: 'post',
                        url: this.plan.routes.clone,
                        data: this.selectedValues
                    }).then((res) => {
                        console.log(res.data);
                        $('#load_this_div').css({opacity: 1});
                        this.dialog = false;
                        Event.$emit('media-plan-added', res.data.data);
                        this.sweet_alert("Media plan was successfully cloned", 'success');
                    }).catch((error) => {
                        if (error.response && (error.response.status == 422)) {
                            this.displayServerValidationErrors(error.response.data.errors);
                        } else {
                            this.sweet_alert('An unknown error has occurred, media plan cannot be cloned. Please try again', 'error');
                        }
                    });
                }
            });
        }
    }
  }
</script>