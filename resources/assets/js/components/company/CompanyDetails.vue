<template>
<v-flex>
  <v-card>
    <p></p>
     <v-layout wrap>
        <v-flex xs12 sm12 md12>
             <v-layout  style="height: 70px; margin-Top: 10px;"> 
                <v-flex md9 my-1>
                    <p class="weight_medium">
                     {{ company.name }})
                    </p>
                    <p class="weight_medium small_faint">
                      {{ company.address }}
                    </p>
            
                </v-flex>
                 <v-flex md2 my-1>
                    <b-img :src="company.logo"  style="width: 60px; height: 60px" id="client_logo" alt="Image 1"></b-img>
                                
                </v-flex>
            </v-layout>
        </v-flex>
    </v-layout>
    <v-divider></v-divider>
     <v-layout wrap>
        <v-flex xs12 sm12 md12>
             <v-layout style="height: 70px" >
                <v-flex md4 my-1 style="border-width:2px;" >
                    <p class="bold small_faint">
                     RC
                    </p>
                      <p class="weight_medium">
                      {{ company.company_rc}}
                    </p>
                </v-flex>
                <v-divider vertical></v-divider>
                  <v-flex md4 my-1>
                     <p class="bold small_faint">
                     Email
                    </p>
                      <p class="weight_medium">
                     {{ company.email }}
                    </p>
                </v-flex>
                <v-divider vertical></v-divider>
                  <v-flex md4 my-1>
                     <p class="bold small_faint">
                    Phone
                    </p>
                      <p class="weight_medium">
                      {{ company.phone_number }}
                    </p>
                </v-flex>
                 <v-divider vertical></v-divider>
               

               
            </v-layout>
        </v-flex>
    </v-layout>
     <v-divider></v-divider>
     <v-layout wrap >
        <v-flex xs12 sm12 md12>
             <v-layout style="height: 70px" >
                  <v-flex md4 my-1>
                       
                   <p class="bold small_faint">
                     Website
                    </p>
                      <p class="weight_medium">
                     {{ company.website }}  
                    </p>
                </v-flex>

                <v-divider vertical></v-divider>
                  <v-flex md4 my-1>
                     <p class="bold small_faint">
                     City
                    </p>
                      <p class="weight_medium">
                     {{ company.city }}
                    </p>
                </v-flex>
                <v-divider vertical></v-divider>
                  <v-flex md4 my-1>
                     <p class="bold small_faint">
                    State
                    </p>
                      <p class="weight_medium">
                      {{ company.state }}
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
    hr {
    margin-top: 0;
    margin-bottom: 0;
}
</style>

<script>
  export default {
        props: {
         companyData: Object,
        },
    data () {
      return {
        search: '',
           company: this.companyData,
      }
    },
    created() {
        var self = this;
        Event.$on('company-updated', function (company) {
           self.company = company;
        });
    },
    mounted() {
      console.log(this.brands);
        console.log('Display company details Component mounted.');
    },
    methods: {
        goBack(id){
            var url= '/clients/index';
             window.location = url;
        }
        
    }
  }
</script>