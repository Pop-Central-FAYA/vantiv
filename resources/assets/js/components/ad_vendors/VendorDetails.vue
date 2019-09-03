<template>
<v-flex>
  <v-card>
    <p></p>
     <v-layout wrap>
        <v-flex xs12 sm12 md12>
             <v-layout  style="height: 60px; margin-Top: 10px;"> 
                <v-flex>
                 <v-icon color="#44C1C9" @click="goBack()"  v-b-tooltip.hover title="back" dark right>fa-arrow-left</v-icon>
                </v-flex>
                <v-flex md9 my-1>
                    <p class="weight_medium">
                     {{ ad_vendorData.name }}
                    </p>
                    <p class="weight_medium small_faint">
                      {{ ad_vendorData.street_address }}
                    </p>
            
                </v-flex>
                 <v-flex md2 my-1>          
                </v-flex>
            </v-layout>
        </v-flex>
    </v-layout>
    <v-divider></v-divider>
     <v-layout wrap>
        <v-flex xs12 sm12 md12>
             <v-layout style="height: 70px" >
                <v-flex md3 my-1 style="border-width:2px;" >
                  
                   <p class="bold small_faint">
                     Account Executive
                    </p>
                      <p class="weight_medium">
                     {{ ad_vendorData.contacts[0].first_name }}  {{ ad_vendorData.contacts[0].last_name }}
                    </p>
            
                </v-flex>
                <v-divider vertical></v-divider>
                  <v-flex md4 my-1>
                     <p class="bold small_faint">
                     Email
                    </p>
                      <p class="weight_medium">
                     {{ ad_vendorData.contacts[0].email }}
                    </p>
                </v-flex>
                <v-divider vertical></v-divider>
                  <v-flex md3 my-1>
                     <p class="bold small_faint">
                    Phone
                    </p>
                      <p class="weight_medium">
                      {{ ad_vendorData.contacts[0].phone_number }}
                    </p>
                </v-flex>
                 <v-divider vertical></v-divider>
                  <v-flex md3 my-1>
                     <p class="bold small_faint">
                     Joined
                    </p>
                      <p class="weight_medium">
                      {{ dateToHumanReadable(ad_vendorData.created_at)}}
                    </p>
                </v-flex>


               
            </v-layout>
        </v-flex>
    </v-layout>
  </v-card>

  </v-flex>
</template>

<style>
  tbody tr:hover {
    background-color: transparent !important;
    cursor: pointer;
  }
  tbody:hover {
    background-color: rgba(0, 0, 0, 0.12);
  }
  p {
    margin-bottom: -1px;
    margin-left: 30px;
  }
   .v-tabs {
        background: #fafafa;
    }
    a.v-tabs__item {
        padding: 0px 3rem;
        text-decoration: none !important;
        border: 1px solid #e8e8e8;
        border-left: none !important;
    }
    a.v-tabs__item.v-tabs__item--active {
        background: #01c4ca;
        color: #fff;
    }
    .accent {
        background-color: #01c4ca !important;
        border-color: #01c4ca !important;
    }
</style>

<script>
  export default {
        props: {
         ad_vendorData: Object,
        },
    data () {
      return {
        search: '',
      }
    },
    mounted() {
        console.log('Display vendor details Component mounted.');
    },
    methods: {
        goBack(id){
            var url= '/ad-vendors';
             window.location = url;
        }
        
    }
  }
</script>