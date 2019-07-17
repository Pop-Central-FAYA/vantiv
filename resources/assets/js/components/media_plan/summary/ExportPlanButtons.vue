<template>
    <button @click="ExportPlan()" type="button" class="btn btn-success media-plan btn block_disp uppercased"><i class="media-plan material-icons">{{btnIcon}}</i>{{ btnName}}</button>
</template>

<script>
    export default {
        props: {
            id: String,
            btnName: String,
            btnIcon: String
        },
        data() {
            return {
                client: '',
            };
        },
        mounted() {
            console.log('Media Plan Export Plan Component mounted.');
        },
        methods: {
            ExportPlan() {
                if(this.btnName == "Export Plan"){
                  window.location = '/media-plan/export/'+this.id; 
                }else if(this.btnName == "Approve Plan"){
                    window.location = '/media-plan/approve/'+this.id; 
                }else if(this.btnName == "Decline Plan"){
                    window.location = '/media-plan/decline/'+this.id; 
                }else if(this.btnName == "Back"){
                    window.location = '/media-plan/createplan/'+this.id; 
                }
                 
            }
        }
    }
</script>