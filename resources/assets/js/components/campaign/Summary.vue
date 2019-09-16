<template>
  <div>
    <v-card flat tile>
        <v-card-text class="pt-4 mb-3">
            <v-layout>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Client:</span>
                        {{ campaignData.client.name}}
                    </p>
                </v-flex>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Brand:</span>
                        {{ campaignData.brand.name }}
                    </p>
                </v-flex>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Net Total:</span>
                        &#8358; {{ calculateNetTotal }}
                    </p>
                </v-flex>
            </v-layout>
            <v-layout>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Flight Date:</span>
                        {{ campaignData.flight_date }}
                    </p>
                </v-flex>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Media Type:</span>
                        {{ campaignData.media_type }}
                    </p>
                </v-flex>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">VAT:</span>
                        5%
                    </p>
                </v-flex>
            </v-layout>
            <v-layout>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Created By:</span>
                        {{ campaignData.creator.firstname }} {{ campaignData.creator.lastname }}
                    </p>
                </v-flex>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Created On:</span>
                        {{ campaignData.created_at }}
                    </p>
                </v-flex>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Status:</span>
                        {{ campaignData.status }}
                    </p>
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
    <v-card flat tile>
        <v-card-text>
            <v-layout>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Gender:</span>
                        {{ campaignData.gender}}
                    </p>
                </v-flex>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Age Range:</span>
                        {{ campaignData.age_groups }}
                    </p>
                </v-flex>
                <v-flex md4 my-1 v-if="campaignData.social_class != 'null' && campaignData.social_class !== ''">
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Social Class:</span>
                        {{ campaignData.social_class }}
                    </p>
                </v-flex>
            </v-layout>
            <v-layout v-if="campaignData.states != 'null' && campaignData.states !== ''">
                <v-flex md12 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">States:</span>
                        {{ campaignData.states }}
                    </p>
                </v-flex>
            </v-layout>
            <v-layout v-if="campaignData.regions != 'null' && campaignData.regions !== ''">
                <v-flex md12 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Regions:</span>
                        {{ campaignData.regions }}
                    </p>
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
  </div>
</template>

<script>
  export default {
    props: {
        campaign: Object,
    },
    data () {
      return {
          campaignData : this.campaign
      }
    },
    created() {
        var self = this;
        Event.$on('updated-campaign', function (campaign) {
            self.campaignData = campaign;
        });
    },
    computed : {
        calculateNetTotal : function() {
            var budget = parseInt(this.campaignData.budget);
            if(budget > 0){
                return this.formatAmount(budget - ((5/100)*budget))
            }else{
                return this.formatAmount(budget)
            }
        }
    }
  }
</script>