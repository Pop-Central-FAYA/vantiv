<template>
    <v-layout wrap>
        <v-flex xs12 sm3 md3>
            <span>Station Type:</span>
                <v-select class="mt-0 pt-1" v-model="selectedFilters.station_type" :items="filterValues['station_type']"></v-select>
        </v-flex>
        <v-flex xs12 sm2 md2>
            <span>Days:</span>
                <v-select class="mt-0 pt-1" v-model="selectedFilters.day" :items="filterValues['day']"></v-select>
        </v-flex>
        <v-flex xs12 sm2 md2>
            <span>States:</span>
                <v-select class="mt-0 pt-1" v-model="selectedFilters.state" :items="filterValues['state']"></v-select>
        </v-flex>
        <v-flex xs12 sm2 md2>
            <span>Day Parts:</span>
                <v-select class="mt-0 pt-1" v-model="selectedFilters.day_part" :items="filterValues['day_part']"></v-select>
        </v-flex>
        <v-flex xs12 sm3 md3 pt-4>
            <v-btn @click="createNewRatings" color="default-vue-btn">
                <v-icon>search</v-icon> FILTER
            </v-btn>
        </v-flex>
    </v-layout>
</template>

<style>
    .v-text-field .v-input__slot {
        padding: 0px 12px;
        min-height: 45px;
        margin-bottom: 0px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .v-text-field>.v-input__control>.v-input__slot:after, .v-text-field>.v-input__control>.v-input__slot:before {
        content: none;
    }
    .theme--dark.v-btn.v-btn--disabled:not(.v-btn--icon):not(.v-btn--flat):not(.v-btn--outline) {
        background-color: hsl(184, 55%, 53%)!important;
    }
    .v-btn {
        height: 45px !important;
    }
</style>

<script>
    export default {
        props: {
            filterValues: Object,
            selectedFilters: Object,
            routes: Object
        },
        mounted() {
            console.log('Filter Suggestions Component mounted.');
            this.createNewRatings();
        },
        methods: {
            createNewRatings() {
                this.sweet_alert('Getting station list based on filters', 'info', 60000);
                axios({
                    method: 'get',
                    url: this.routes.new_ratings_action,
                    params: this.selectedFilters
                }).then((res) => {
                    if (this.isNotEmpty(res.data.data)) {
                        this.sweet_alert('Ratings retrieved', 'success');
                        Event.$emit('ratings-created', res.data.data);
                    } else {
                        this.sweet_alert('No results found, please try another filter', 'error');
                    }
                 }).catch((error) => {
                     if (error.response && (error.response.status == 422)) {
                        this.displayServerValidationErrors(error.response.data.errors);
                    } else {
                        this.sweet_alert('An unknown error has occurred, please try again', 'error');
                    }
                 })
            }
        }
    }
</script>
