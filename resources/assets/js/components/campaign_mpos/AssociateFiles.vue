<template>
  <v-layout>
    <v-dialog v-model="dialog" persistent max-width="600px">
      <template v-slot:activator="{ on }">
          <v-tooltip top>
              <template v-slot:activator="{ on }">
                <div v-on="on" class="d-inline-block position-icon">
                  <v-icon color="#01c4ca" dark left v-on="on" :disabled="!isCampaignOpen(campaign.status)" @click="prepareData()">attach_files</v-icon>
                </div>
              </template>
              <span v-if="group === 'publisher_id' && isCampaignOpen(campaign.status)">Attach assets</span>
              <span v-else-if="isCampaignOpen(campaign.status)">Attach Files to MPO</span>
              <span v-else>Action is disabled while campaign is {{ campaign.status.toLowerCase() }}</span>
          </v-tooltip>
      </template>
      <v-card>
        <v-card-text>Media assets below are for brand <b>{{ brand }}</b>  which to belongs to client <b>{{ client }}</b> .</v-card-text>
        <v-card-text>
          <v-container grid-list-md>
            <v-form>
              <v-layout wrap v-for="(duration,key) in filtered_durations" v-bind:key="key">
                <v-flex xs12 sm6 md2>
                  <span>Duration</span>
                  <v-text-field required readonly :value="duration" v-model="filtered_durations[key]"></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md10>
                  <span>Select File</span>
                  <v-select
                    v-model="selected_assets[duration]"
                    :items="groupedAssets[duration]"
                    item-text="file_name"
                    item-value="id"
                    :name="`file.${key}`"
                  ></v-select>
                  <span class="text-danger" v-show="errors.has(`file.${key}`)">{{ errors.first(`file.${key}`) }}</span>
                </v-flex>
              </v-layout>
            </v-form>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red" dark @click="dialog = false">Close</v-btn>
          <v-btn class="default-vue-btn" dark @click="associateFiles()">Submit</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
  export default {
    props : {
      campaign : Object,
      assets: Array,
      client: String,
      brand: String,
      selectedAdslots : Array,
      group : String
    },
    data() {
      return {
          dialog: false,
          filtered_durations : [],
          groupedAssets : [],
          filtered_assets : [],
          selected_assets : []
      }
    },
    methods: {
      prepareData : function() {
          this.filterAssetByDuration(this.assets, this.selectedAdslots);
          this.dialog = true
      },
      getDistinctDuration(time_belts) {
          return [...new Set(time_belts.map(x => x.duration))];
      },
      filterAssetByDuration(assets, time_belts) {
          const duration = this.getDistinctDuration(time_belts);
          this.filtered_durations = duration;
          const filtered_assets = assets.filter(item => duration.some(resultItem => resultItem === item.duration));
          const groupedAssets = _.groupBy(filtered_assets, asset => asset.duration);
          this.groupedAssets = groupedAssets;
          const defaultSelectValues = [];
          Object.keys(groupedAssets).forEach(function (item, index) {
            defaultSelectValues[index] = groupedAssets[item][0]['id'];
          });
          this.filtered_assets = defaultSelectValues;
      },
      associateFiles() {
        this.$validator.validate().then(valid => {
          if (!valid) {
            console.log('invalid form');
          }
          if (valid) {
            $("#load_this").css({ opacity: 0.2 });
            var msg = "Associating media asset to MPO, please wait";
            this.sweet_alert(msg, 'info');
            axios({
                method: 'post',
                url: this.campaign.links.associate_assets,
                data: {
                  id : _.map(this.selectedAdslots, 'id'),
                  durations: this.filtered_durations,
                  assets: this.selected_assets,
                  group : this.group
                }
            }).then((res) => {
                $('#load_this_div').css({opacity: 1});
                if (res.data.status === "error") {
                    this.sweet_alert(res.data.message, 'error');
                } else {
                    this.sweet_alert(res.data.message, 'success');
                    Event.$emit('updated-group-adslots',res.data.data.grouped_time_belts)
                    Event.$emit('updated-mpos', res.data.data.campaign_mpos)
                    Event.$emit('updated-campaign', res.data.data)
                    this.dialog = false;
                }
            }).catch((error) => {
                this.sweet_alert('An unknown error has occurred, media assets cannot be associated. Please try again', 'error');
            });
          }
        });
      }
    }
  }
</script>
<style>
  .theme--dark.v-icon.v-icon--disabled {
      color: grey !important;
  }
  .position-icon {
      padding-top: 12px;
  }
</style>