<template>


    <button @click="ButtonAction()" type="button" class="media-plan btn block_disp uppercased bg_red mr-1">><i class="media-plan material-icons">{{btnIcon}}</i>{{ btnName}}</button>
</template>

<script>
    export default {
        props: {
            btnName: String,
            btnIcon: String,
            btnDestination: String
        },
        data() {
            return {
                client: '',
            };
        },
        mounted() {
        },
        methods: {
            ButtonAction() {
                  window.location = this.btnDestination; 
            }
        }
    }
</script>