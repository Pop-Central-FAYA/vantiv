<template>
    <v-card>
        <p><br></p>
        <v-layout>
            <v-flex xs12 style="text-align : center">
                <h2 class="text-center" style="display : block"><b>{{ company.name }}</b></h2>
                <img :src="company.logo" style="width:100px; height:100px; display : inline-block;" alt="">
            </v-flex>
        </v-layout>
        <v-layout>
            <v-flex xs12>
                <h6 class="text-center">{{ company.address }}</h6>
            </v-flex>
        </v-layout>
        <v-layout>
            <v-flex xs6>
                <v-card-text class="text-left">
                    <div class="outer">
                        <div class="left-item-vendor"><h4>The Advertising manager, </h4></div>
                    </div>
                    <div class="outer">
                        <div class="left-item-vendor"><h4>{{ vendor.name }}, </h4></div>
                    </div>
                    <div class="outer">
                        <div class="left-item-vendor"><h4>{{ vendor.street_address }}, {{ vendor.city }}, {{ vendor.state }}. </h4></div>
                    </div>
                </v-card-text>
            </v-flex>
            <v-flex xs6>
                <v-card-text class="text-right">
                    <div class="outer">
                        <div class="left-item"><h4>Mpo Number : </h4></div>
                        <div class="right-item" ><h4> {{ mpoDetails.reference_number }}</h4></div>
                    </div>
                    <div class="outer">
                        <div class="left-item"><h4>Client : </h4></div>
                        <div class="right-item" ><h4> {{ mpoDetails.campaign.client.name }}</h4></div>
                    </div>
                    <div class="outer">
                        <div class="left-item"><h4>Product : </h4></div>
                        <div class="right-item" ><h4> {{ mpoDetails.campaign.product }}</h4></div>
                    </div>
                    <div class="outer">
                        <div class="left-item"><h4>Campaign : </h4></div>
                        <div class="right-item" ><h4> {{ mpoDetails.campaign.name }}</h4></div>
                    </div>
                    <div class="outer">
                        <div class="left-item"><h4>Brand : </h4></div>
                        <div class="right-item" ><h4> {{ mpoDetails.campaign.brand.name }}</h4></div>
                    </div>
                    <div class="outer">
                        <div class="left-item"><h4>Order Date : </h4></div>
                        <div class="right-item" ><h4> {{ currentDate() }}</h4></div>
                    </div>
                </v-card-text>
            </v-flex>
        </v-layout>
        <v-layout>
            <v-flex v-if="previousReference">
                <h3 class="text-center"><b>Revised Media Contract</b></h3><br>
                <p class="text-center">This cancels and replaces Media Contract No: 
                    <span v-if="isPublic">{{ previousReference }}</span>
                    <a :href="previousMpo" target="_blank" v-else>{{ previousReference }}</a>
                    </p>
            </v-flex>
        </v-layout>
    </v-card>
</template>
<script>
export default {
    props : {
        mpoDetails : Object,
        previousReference : Number,
        company : Object,
        vendor : Object,
        previousMpo : String,
        isPublic : Boolean
    }
}
</script>
<style >
    .layout  {
        margin-top : 20px !important;
    }
    .left-item {
        width: 140px; 
        display: inline-block;
        text-align: right
    }
    .right-item {
        display: inline-block;
        width: 150px;
        text-align: left;
    }
    .outer {
        display: block
    }
    .left-item-vendor{
        width: 300px; 
        display: inline-block;
        text-align: left
    }
</style>