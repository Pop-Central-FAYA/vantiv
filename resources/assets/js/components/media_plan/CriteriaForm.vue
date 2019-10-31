<template>
  <v-container grid-list-md>
    <v-form>
        <v-layout wrap>
            <v-flex xs12 sm6 md6>
                <span>Media Type:</span>
                <v-text-field name="media_type" disabled readonly v-model="selectedValues.media_type" v-validate="'required'"></v-text-field>
                <span class="text-danger" v-show="errors.has('media_type')">{{ errors.first('media_type') }}</span>
            </v-flex>
            <v-flex xs12 sm6 md6>
                <span>Gender:</span>
                <multiselect :disabled="disableEditing(true)" placeholder="Please select gender" v-model="selectedValues.gender" :options="genders" :multiple="true" group-values="criterias" group-label="all" :group-select="true" :searchable="false" name="gender" v-validate="'required'" class="mb-3"></multiselect>
                <span class="text-danger" v-show="errors.has('gender')">
                    Gender is required
                </span>
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
                        :disabled="disableEditing(false)"
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
                        :disabled="disableEditing(false)" 
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
        <v-layout wrap v-for="(ageGroup,key) in selectedValues.age_groups" v-bind:key="key">
            <v-flex xs12 sm5 md5>
                <span v-if="key == 0">Minimum Age:</span>
                <v-text-field :disabled="disableEditing(true)"  v-validate="'required|min_value:18'" type="number" placeholder="Min Age" :ref="`min_age[${key}]`" :name="`min_age[${key}]`" v-model="selectedValues.age_groups_min[key]"></v-text-field>
                <span class="text-danger" v-show="errors.has(`min_age[${key}]`)">
                    Minimum age must be 18 or more
                </span>
            </v-flex>
            <v-flex xs12 sm5 md5>
                <span v-if="key == 0">Maximum Age:</span>
                <v-text-field :disabled="disableEditing(true)"  v-validate="'required'" type="number" placeholder="Max Age" :name="`max_age[${key}]`" v-model="selectedValues.age_groups_max[key]"></v-text-field>
                <span class="text-danger" v-show="errors.has(`max_age[${key}]`)">
                    Maximum age is required
                </span>
            </v-flex>
            <v-flex v-if="!disableEditing(true)" xs12 sm1 md1 class="py-3 px-0 should-not-render">
                <v-icon class="mt-4 pt-3" v-if="key == 0" color="success" dark right @click="addAgeGroup">add_box</v-icon>
                <v-icon class="pt-3" v-else color="red" dark right @click="deleteAgeGroup(key)">delete</v-icon>
            </v-flex>
        </v-layout>
        <v-layout wrap>
            <v-flex xs12 sm12 md12>
                <span>Social Class:</span>
                <multiselect :disabled="disableEditing(true)" placeholder="Please select social class" v-model="selectedValues.social_class" :options="socialClasses" :multiple="true" group-values="criterias" group-label="all" :group-select="true" :searchable="false" name="social_class" class="mb-3"></multiselect>
            </v-flex>
        </v-layout>
        <v-layout wrap>
            <v-flex xs12 sm12 md12>
                <span>Region:</span>
                <multiselect :disabled="disableEditing(true)" placeholder="Please select region" v-model="selectedValues.region" :options="regions" :multiple="true" group-values="criterias" group-label="all" :group-select="true" :searchable="false" name="region" class="mb-3"></multiselect>
            </v-flex>
        </v-layout>
        <v-layout wrap>
            <v-flex xs12 sm12 md12>
                <span>State:</span>
                <multiselect :disabled="disableEditing(true)" placeholder="Please select state" v-model="selectedValues.state" :options="states" :multiple="true" group-values="criterias" group-label="all" :group-select="true" :searchable="false" name="state" class="mb-3"></multiselect>
            </v-flex>
        </v-layout>
        <v-layout wrap>
            <v-flex xs12 sm12 md12>
                <span>Service Charge (%):</span>
                <v-flex xs12 sm12 md12>
                  <v-text-field :disabled="disableEditing(false)" v-validate="'numeric'" name="agency_commission" placeholder="Enter Service Charge" v-model="selectedValues.agency_commission" type="number"></v-text-field>
                </v-flex>
                <span class="text-danger" v-show="errors.has('agency_commission')">
                    Service charge must be numeric
                </span>
            </v-flex>
        </v-layout>
        <v-layout wrap>
            <v-flex xs12 sm12 md12>
                <span>Campaign Name:</span>
                <v-flex xs12 sm12 md12>
                  <v-text-field :disabled="disableEditing(false)" v-validate="'required'" name="campaign_name" placeholder="Enter Campaign Name" v-model="selectedValues.campaign_name"></v-text-field>
                </v-flex>
                <span class="text-danger" v-show="errors.has('campaign_name')">
                    Campaign name is required
                </span>
            </v-flex>
        </v-layout>
        <v-layout row wrap class="pt-2 pb-3 white-bg">
            <v-flex xs12 sm4 md4>
                <span>Client:</span>
                <v-select
                    :disabled="disableEditing(false)" 
                    v-model="selectedValues.client"
                    :items="clients"
                    item-text="name"
                    item-value="id"
                    v-validate="'required'"
                    name="client"
                    placeholder="Please select client"
                ></v-select>
                <span class="text-danger" v-show="errors.has('client')">{{ errors.first('client') }}</span>
            </v-flex>
            <v-flex xs12 sm4 md4>
                <span>Brand:</span>
                <v-select
                    :disabled="disableEditing(false)" 
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
            <v-flex xs12 sm4 md4>
                <span>Product Name:</span>
                <v-text-field :disabled="disableEditing(false)" name="product" placeholder="Product name" v-model="selectedValues.product" v-validate="'required'"></v-text-field>
                <span class="text-danger" v-show="errors.has('product')">{{ errors.first('product') }}</span>
            </v-flex>
        </v-layout>
        <!-- BUTTON -->

        <v-layout v-if="isMediaPlanEditable(this.mediaPlan.status || '')" wrap>
            <v-flex align-end class="text-md-right">
                <v-btn large color="default-vue-btn" dark @click="save()"><v-icon left>save</v-icon>Save</v-btn>
                <v-btn large color="default-vue-btn" dark @click="next()">Next<v-icon right>navigate_next</v-icon></v-btn>
            </v-flex>
        </v-layout>

        <v-layout v-else-if="isMediaPlanPastReviewStage(this.mediaPlan.status || '')" wrap>
            <v-flex align-end class="text-md-right">
                <v-btn large color="default-vue-btn" dark @click="next()">Next<v-icon right>navigate_next</v-icon></v-btn>
            </v-flex>
        </v-layout>

        <v-layout v-else wrap>
            <v-flex align-end class="text-md-right">
                <v-btn large class="default-vue-btn" :disabled="isRunRatings" dark @click="runRatings()">Run Ratings</v-btn>
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
</style>

<script>
  export default {
    props : {
      criterias: Object,
      redirectUrls: Object,
      clients: Array,
      mediaPlan: Object
    },
    data() {
      return {
          isRunRatings: false,
          modal: false,
          ageGroups: [
              { min: 0, max:0}
          ],
          selectedValues: this.setupSelectedValues(),
          dateFormatted: this.formatDate(new Date().toISOString().substr(0, 10)),
          today: this.formatDate(new Date().toISOString().substr(0, 10)),
          startDateMenu: false,
          endDateMenu: false,
          genders: [],
          socialClasses: [],
          states: [],
          regions: []
      }
    },
    mounted() {
      console.log('Criteria form Component mounted.');
    },
    created() {
        this.setCriterias();
    },
    computed: {
        computedStartDateFormatted () {
            return this.formatDate(this.selectedValues.start_date)
        },
        computedEndDateFormatted () {
            return this.formatDate(this.selectedValues.end_date)
        },
        mergeMinMaxAges () {
            var newArr = [];
            var self = this;
            this.selectedValues.age_groups_min.forEach((element, key) => {
                newArr[key] = {
                    min: element,
                    max: self.selectedValues.age_groups_max[key]
                }
            });
            return newArr;
        },
        filteredBrands() {
            const self = this;
            const filtered = this.clients.filter(function(clients) {
                return clients.id === self.selectedValues.client;
            });
            if (filtered.length > 0) {
                return filtered[0].brands;
            }
            return [];
        },
    },
    methods: {
        disableEditing(is_criteria) {
            let status = this.mediaPlan.status || '';
            if (this.isMediaPlanPastReviewStage(status)) {
                return true;
            }
            if (this.isMediaPlanEditable(status)) {
                // targeting criteria cannot be edited once the media plan is created
                return is_criteria;
            }
            return false;
        },
      setupSelectedValues() {
          let selectedValues = {
              'media_type': 'Tv',
              'gender': this.formatTargetingCriteria(this.mediaPlan.gender),
              'start_date': this.mediaPlan.start_date,
              'end_date': this.mediaPlan.end_date,
              'social_class': this.formatTargetingCriteria(this.mediaPlan.social_classes),
              'region': this.formatTargetingCriteria(this.mediaPlan.regions),
              'state': this.formatTargetingCriteria(this.mediaPlan.states),
              'agency_commission': this.mediaPlan.agency_commission,
              'campaign_name': this.mediaPlan.campaign_name,
              'client': this.mediaPlan.client_id,
              'brand': this.mediaPlan.brand_id,
              'product': this.mediaPlan.product_name,
          };
         let age_groups = this.formatTargetingCriteria(this.mediaPlan.age_groups);
         let age_groups_min = [];
         let age_groups_max = [];
         age_groups.forEach(function(item) {
             age_groups_min.push(item.min);
             age_groups_max.push(item.max);
         })
        selectedValues['age_groups_min'] = age_groups_min;
        selectedValues['age_groups_max'] = age_groups_max;
        selectedValues['age_groups'] = age_groups;

          return selectedValues;
      },
      /**
       * This is really grimy, will need to refactor
       */
      formatTargetingCriteria(criteria) {
        criteria = criteria || [];
        if (typeof criteria === 'string') {
            try {
                criteria = JSON.parse(criteria);
            } catch (error) {
                return [];
            }
        }
        if (Array.isArray(criteria)) {
            return criteria;
        }
        if (typeof criteria === 'object') {
            return Object.values(criteria);
        }
        return [];

      },
      formatDate(date) {
        if (!date) return null
        const [year, month, day] = date.split('-')
        return `${day}/${month}/${year}`
      },
      parseDate(date) {
        if (!date) return null
        const [month, day, year] = date.split('/')
        return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
      },
      setCriterias() {
        this.genders[0] = this.criterias['genders'];
        this.socialClasses[0] = this.criterias['social_classes'];
        this.regions[0] = this.criterias['regions'];
        this.states[0] = this.criterias['states'];
      },
      addAgeGroup() {
          this.ageGroups.push({ min:0, max:0 });
      },
      deleteAgeGroup(index) {
          this.ageGroups.splice(index,1);
      },
      next() {
          const url = this.mediaPlan.routes.suggestions.index
          if (typeof url === 'string' && url.length > 0) {
              window.location = url;
          }
      },
      save() {
          this.$validator.validate().then(valid => {
          if (!valid) {
            console.log('invalid form');
          }
          this.selectedValues.age_groups = this.mergeMinMaxAges;
          if (valid) {
            this.isRunRatings = true;
            this.sweet_alert("Saving, please wait", 'info', 60000);
            axios({
                method: 'patch',
                url: this.mediaPlan.routes.update,
                data: this.selectedValues
            }).then((res) => {
                this.sweet_alert('Media plan updated', 'success');
            }).catch((error) => {
                if (error.response && (error.response.status == 422)) {
                    this.displayServerValidationErrors(error.response.data.errors);
                } else {
                    this.sweet_alert('An unknown error has occurred, please try again', 'error');
                }
            }).finally(() => {
                this.isRunRatings = false;
            });
          }
        });
      },
      runRatings() {
        // Validate inputs using vee-validate plugin 
        this.$validator.validate().then(valid => {
          if (!valid) {
            console.log('invalid form');
          }
          this.selectedValues.age_groups = this.mergeMinMaxAges;
          if (valid) {
            this.isRunRatings = true;
            var msg = "Generating ratings, please wait";
            this.sweet_alert(msg, 'info', 60000);
            axios({
                method: 'post',
                url: this.redirectUrls.submit_form,
                data: this.selectedValues
            }).then((res) => {
                this.isRunRatings = false;
                console.log(res.data);
                if (res.data.status === "success") {
                    this.sweet_alert(res.data.message, 'success');
                    window.location = res.data.redirect_url;
                } else {
                    this.sweet_alert(res.data.message, 'error');
                    this.dialog = false;
                }
            }).catch((error) => {
                this.isRunRatings = false;
                if (error.response && (error.response.status == 422)) {
                        this.displayServerValidationErrors(error.response.data.errors);
                    } else {
                        this.sweet_alert('An unknown error has occurred, please try again', 'error');
                    }
            });
          }
        });
      }
    }
  }
</script>