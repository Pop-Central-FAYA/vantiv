<template>
 <v-container grid-list-md>
        <v-form>
            <v-layout wrap>
            <v-flex xs12 sm4 md4>
             
             
            </v-flex>
             <v-flex xs12 sm8 md8>
                <span>Campaign Name:</span>
                <v-flex xs12 sm6 md6>
                  <v-text-field v-validate="'required'" name="campaign_name" placeholder="Enter Campaign Name" ></v-text-field>
                   <span>Campaign Name:</span>
                   <v-text-field v-validate="'required'" name="campaign_name" placeholder="Enter Campaign Name" ></v-text-field>
                </v-flex>
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
        props: {
            companyDetail: Object,
            permissionList:Array,
            imgURL: 'http://thumb7.shutterstock.com/display_pic_with_logo/2892448/342092249/stock-vector-cool-comic-book-bubble-text-pop-art-retro-style-342092249.jpg',
        },
        data() {

        },
        mounted() {
             console.log("Summary component mounted");
           
        },  
        created() {    
          }, 
         methods: {   
        }
    }
</script>
