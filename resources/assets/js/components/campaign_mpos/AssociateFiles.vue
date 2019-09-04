<template>
  <v-layout>
    <v-dialog v-model="dialog" persistent max-width="600px">
      <template v-slot:activator="{ on }">
          <v-tooltip top>
              <template v-slot:activator="{ on }">
                  <v-icon color="#01c4ca" dark left v-on="on" @click="dialog = true">attach_files</v-icon>
              </template>
              <span>Attach Files to MPO</span>
          </v-tooltip>
      </template>
      <v-card>
        <v-card-title>
          <span class="headline">Attach Files to {{ mpo.station }}</span>
        </v-card-title>
        <v-card-text>Media assets below are for brand {{ brand}} which to belongs to client {{ client}}.</v-card-text>
        <v-card-text>
          <v-container grid-list-md>
            <v-form>
              <v-layout wrap v-for="(duration,key) in form.duration" v-bind:key="key">
                <v-flex xs12 sm6 md2>
                  <span>Duration</span>
                  <v-text-field required readonly :value="duration" v-model="form.duration[key]"></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md10>
                  <span>Select File</span>
                  <v-select
                    v-model="form.asset[key]"
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
      mpo: {
          required : true,
          type : Object
      },
      assets: Array,
      client: String,
      brand: String
    },
    data() {
      return {
          dialog: false,
          groupedAssets: [],
          form: {
            duration: [],
            asset: []
          }
      }
    },
    mounted() {
      console.log('Associate Files Component mounted.');
      // console.log(this.mpo.campaign_mpo_time_belts);
      this.filterAssetByDuration(this.assets, this.mpo.campaign_mpo_time_belts);
      // console.log(this.assets);
    },
    methods: {
      getDistinctDuration(time_belts) {
        return [...new Set(time_belts.map(x => x.duration))];
      },
      filterAssetByDuration(assets, time_belts) {
        const duration = this.getDistinctDuration(time_belts);
        this.form.duration = duration;
        const filtered_assets = assets.filter(item => duration.some(resultItem => resultItem === item.duration));
        const groupedAssets = _.groupBy(filtered_assets, asset => asset.duration);
        this.groupedAssets = groupedAssets;
        const defaultSelectValues = [];
        Object.keys(groupedAssets).forEach(function (item, index) {
          defaultSelectValues[index] = groupedAssets[item][0]['id'];
        });
        this.form.asset = defaultSelectValues;
      },
      associateFiles() {
        // Validate inputs using vee-validate plugin 
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
                url: '/campaigns/mpo/associate-assets',
                data: {
                  mpo_id: this.mpo.id,
                  durations: this.form.duration,
                  assets: this.form.asset
                }
            }).then((res) => {
                console.log(res.data);
                $('#load_this_div').css({opacity: 1});
                if (res.data.status === "error") {
                    this.sweet_alert(res.data.data, 'error');
                } else {
                    this.sweet_alert(res.data.data, 'success');
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