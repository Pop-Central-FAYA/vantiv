<template>
    <v-container grid-list-md class="p-0">
        <v-layout row wrap class="white-bg">
            <v-flex xs12 sm12 md12 lg12 mb-2>
                <v-expansion-panel class="targeting-attributes-panel">
                    <v-expansion-panel-content>
                        <template v-slot:header>
                            <div><h4 class="weight_medium">Target Attributes</h4></div>
                        </template>
                        <v-card flat tile>
                            <v-card-text class="pt-4 mb-3">
                                <v-layout>
                                    <v-flex md4 my-1>
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Client:</span>
                                            {{ plan.client.name}}
                                        </p>
                                    </v-flex>
                                    <v-flex md4 my-1>
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Brand:</span>
                                            {{ plan.brand.name }}
                                        </p>
                                    </v-flex>
                                    <v-flex md4 my-1>
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Net Total:</span>
                                            &#8358; {{ formatNumber(plan.net_media_cost) }}
                                        </p>
                                    </v-flex>
                                </v-layout>
                                <v-layout>
                                    <v-flex md4 my-1>
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Flight Date:</span>
                                            {{ `${plan.start_date} to ${plan.end_date}` }}
                                        </p>
                                    </v-flex>
                                    <v-flex md4 my-1>
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Media Type:</span>
                                            {{ plan.media_type }}
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
                                            {{ plan.creator.firstname }} {{ plan.creator.lastname }}
                                        </p>
                                    </v-flex>
                                    <v-flex md4 my-1>
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Created On:</span>
                                            {{ plan.date_created }}
                                        </p>
                                    </v-flex>
                                    <v-flex md4 my-1>
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Status:</span>
                                            {{ capitalizeFirstletter(plan.status) }}
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
                                            <template v-for="(gender, key) in plan.gender">
                                                {{ gender }} {{ (plan.gender.length == key+1) ? '':',' }}
                                            </template>
                                        </p>
                                    </v-flex>
                                    <v-flex md4 my-1 v-if="plan.age_groups.length > 0">
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Age Range:</span>
                                            <template v-for="(age, key) in plan.age_groups">
                                                {{ age.min }} - {{ age.max }} {{ (plan.age_groups.length == key+1) ? '':',' }}
                                            </template>
                                        </p>
                                    </v-flex>
                                    <v-flex md4 my-1 v-if="plan.social_classes.length > 0">
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Social Class:</span>
                                            <template v-for="(social_class, key) in plan.social_classes">
                                                {{ social_class }} {{ (plan.social_classes.length == key+1) ? '':',' }}
                                            </template>
                                        </p>
                                    </v-flex>
                                </v-layout>
                                <v-layout v-if="plan.states.length > 0">
                                    <v-flex md12 my-1>
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">States:</span>
                                            <template v-for="(state, key) in plan.states">
                                                {{ state }} {{ (plan.states.length == key+1) ? '':',' }}
                                            </template>
                                        </p>
                                    </v-flex>
                                </v-layout>
                                <v-layout v-if="plan.regions.length > 0">
                                    <v-flex md12 my-1>
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Regions:</span>
                                            <template v-for="(region, key) in plan.regions">
                                                {{ region }} {{ (plan.regions.length == key+1) ? '':',' }}
                                            </template>
                                        </p>
                                    </v-flex>
                                </v-layout>
                            </v-card-text>
                        </v-card>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<style>
    .targeting-attributes-panel .v-expansion-panel__header {
        border-bottom: 1px solid #ccc;
    }
</style>

<script>
  export default {
    props : {
      plan: Object,
    },
    data() {
      return {
      }
    },
    mounted() {
        console.log('Target Attributes Component mounted.');
    }
  }
</script>