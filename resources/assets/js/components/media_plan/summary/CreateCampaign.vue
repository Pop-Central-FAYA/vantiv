<template>
    <button @click="createCampaign()" type="button" class="btn btn-success media-plan btn block_disp uppercased">Convert to Campaign</button>
</template>

<script>
    export default {
        props: {
            id: String,
            permissionList : Array
        },
        data() {
            return {
                client: '',
            };
        },
        mounted() {
            console.log('Media Plan create campaign Component mounted.');
        },
        methods: {
            get_brands(event) {
                if (event.target.value === '') {
                    return;
                }
                axios({
                    method: 'get',
                    url: '/client/get-brands/'+event.target.value,
                    data: {
                        clients: event.target.value
                    }
                }).then((res) => {
                    this.brands = res.data.brands;
                }).catch((error) => {
                    this.brands = [];
                });
            },
            createCampaign() {
                if(!this.hasPermissionAction(this.permissionList, 'convert.media_plan')){
                    return
                }
                $("#load_this").css({ opacity: 0.2 });
                var msg = "Converting media plan to MPO, please wait";
                this.sweet_alert(msg, 'info');
                axios({
                    method: 'get',
                    url: '/media-plan/convert-to-campaign/'+this.id
                }).then((res) => {
                    console.log(res.data);
                    $('#load_this_div').css({opacity: 1});
                    if (res.data.status === "error") {
                        this.sweet_alert(res.data.data, 'error');
                    } else {
                        this.sweet_alert(res.data.data, 'success');
                        window.location = '/campaigns/details/'+res.data.campaign_id;
                    }
                }).catch((error) => {
                    console.log(error.response.data);
                    this.sweet_alert('An unknown error has occurred, please try again', 'error');
                    $('#load_this_div').css({opacity: 1});
                });
            }
        }
    }
</script>