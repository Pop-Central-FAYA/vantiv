<template>
  <div>
    <v-card flat tile>
        <v-card-text class="pt-4 mb-3">
            <v-layout>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Client:</span>
                        {{ campaign.client.company_name}}
                    </p>
                </v-flex>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Brand:</span>
                        {{ campaign.brand.name }}
                    </p>
                </v-flex>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Net Total:</span>
                        &#8358; {{ calculateTotal(campaign.budget) }}
                    </p>
                </v-flex>
            </v-layout>
            <v-layout>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Flight Date:</span>
                        {{ campaign.flight_date }}
                    </p>
                </v-flex>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Media Type:</span>
                        {{ campaign.media_type }}
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
                        {{ campaign.creator.firstname }} {{ campaign.creator.lastname }}
                    </p>
                </v-flex>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Created On:</span>
                        {{ campaign.created_at }}
                    </p>
                </v-flex>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Status:</span>
                        {{ campaign.status }}
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
                        {{ campaign.gender}}
                    </p>
                </v-flex>
                <v-flex md4 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Age Range:</span>
                        {{ campaign.age_groups }}
                    </p>
                </v-flex>
                <v-flex md4 my-1 v-if="campaign.social_class != 'null' && campaign.social_class !== ''">
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Social Class:</span>
                        {{ campaign.social_class }}
                    </p>
                </v-flex>
            </v-layout>
            <v-layout v-if="campaign.states != 'null' && campaign.states !== ''">
                <v-flex md12 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">States:</span>
                        {{ campaign.states }}
                    </p>
                </v-flex>
            </v-layout>
            <v-layout v-if="campaign.regions != 'null' && campaign.regions !== ''">
                <v-flex md12 my-1>
                    <p class="weight_medium">
                        <span class="weight_medium small_faint pr-1">Regions:</span>
                        {{ campaign.regions }}
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
        
      }
    },
    methods : {
        calculateTotal : function(budget) {
            var budget = parseInt(budget);
            if(budget > 0){
                return this.formatAmount(budget - ((5/100)*budget))
            }else{
                return this.formatAmount(budget)
            }
        }
    }
  }
</script>