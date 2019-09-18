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
                     {{ adVendorData.name }}
                    </p>
                    <p class="weight_medium small_faint">
                      {{ adVendorData.street_address }}
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
                     {{ adVendorData.contacts[0].first_name }}  {{ adVendorData.contacts[0].last_name }}
                    </p>
            
                </v-flex>
                <v-divider vertical></v-divider>
                  <v-flex md4 my-1>
                     <p class="bold small_faint">
                     Email
                    </p>
                      <p class="weight_medium">
                     {{ adVendorData.contacts[0].email }}
                    </p>
                </v-flex>
                <v-divider vertical></v-divider>
                  <v-flex md3 my-1>
                     <p class="bold small_faint">
                    Phone
                    </p>
                      <p class="weight_medium">
                      {{ adVendorData.contacts[0].phone_number }}
                    </p>
                </v-flex>
                 <v-divider vertical></v-divider>
                  <v-flex md3 my-1>
                     <p class="bold small_faint">
                     Joined
                    </p>
                      <p class="weight_medium">
                      {{ dateToHumanReadable(adVendorData.created_at)}}
                    </p>
                </v-flex>


               
            </v-layout>
        </v-flex>
    </v-layout>
  </v-card>
  <v-card style="margin-top: 20px;"  >
    <v-card-title>
    </v-card-title>
     <v-layout wrap>
        <v-flex xs12 sm12 md12>
            <v-tabs>
                    <v-tabs-slider></v-tabs-slider>
                    <v-tab href="#campaigns-tab">Mpos</v-tab>
                    <v-tab-item value="campaigns-tab">
                       <mpo-list :mpos="mpos"></mpo-list>
                    </v-tab-item>
            </v-tabs>
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
    hr {
    margin-top: 0;
    margin-bottom: 0;
}
</style>

<script>
  export default {
        props: {
         adVendorData: Object,
         routes:Object,
         mpos:Array
        },
    data () {
      return {
        search: '',
      }
    },
    mounted() {
      console.log(this.routes);
        console.log('Display vendor details Component mounted.');
    },
    methods: {
        goBack(id){
             window.location = this.routes.home;
        }
        
    }
  }
</script>