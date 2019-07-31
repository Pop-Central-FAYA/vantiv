<template>
    <v-container grid-list-md>
        <v-layout wrap>
            <v-flex xs12 sm12 md12>
                <multiselect placeholder="Please select mpos" 
                v-model="selected" 
                :options="mpos" :multiple="true" 
                group-values="selected_mpos" group-label="all" 
                :group-select="true" :searchable="true"
                track-by="id" label="name"
                @input="onChange"
                class="mb-3"></multiselect>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>
    .multiselect__input {
        position: unset !important;
        width: -webkit-fill-available !important;
    }
</style>

<script>
export default {
    props : {
        mpos : {
            required : true,
            type : Array
        }
    },
    data () {
        return {
            selected : []
        }
    },
    methods : {
        onChange : function() {
            Event.$emit('mpos', this.selected)
        }
    }
}
</script>

