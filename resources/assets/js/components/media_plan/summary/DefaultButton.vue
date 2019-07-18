<template>


    <button @click="ButtonAction()" type="button" class="btn btn-success media-plan btn block_disp uppercased"><i class="media-plan material-icons">{{btnIcon}}</i>{{ btnName}}</button>
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