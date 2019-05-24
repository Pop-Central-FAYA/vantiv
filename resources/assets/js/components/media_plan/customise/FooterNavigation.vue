<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <a :href="prevRoute" class="btn btn-secondary w-100 bg-secondary"><i class="media-plan material-icons">navigate_before</i> BACK</a>
            </div>
            <div class="col-md-10 text-right">
                <div v-if="planStatus == 'Approved' || planStatus == 'Declined'" class="btn-group" role="group" aria-label="Basic example">
                    <button @click="save_selections(0)" type="button" class="media-plan btn btn-info mx-2 disabled-action-btn">
                        <i class="media-plan material-icons">save</i> SAVE
                    </button>
                    <a :href="nextRoute" class="btn btn-info mx-2">
                        Next <i class="media-plan material-icons">navigate_next</i>
                    </a>
                </div>
                <div v-else class="btn-group" role="group" aria-label="Basic example">
                    <button @click="save_selections(0)" type="button" class="media-plan btn btn-info mx-2">
                        <i class="media-plan material-icons">save</i> SAVE
                    </button>
                    <button @click="save_selections(1)" type="button" class="btn btn-info mx-2">
                        <i class="media-plan material-icons">library_add</i> CREATE PLAN
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            prevRoute: String,
            nextRoute: String,
            planStatus: String,
            planId: String
        },
        data() {
            return {
                timeBeltsArr: []
            };
        },
        created() {
            var self = this;
            Event.$on('selected-timebelts', function (timeBelts) {
                self.timeBeltsArr = timeBelts;
            });
        },
        mounted: function mounted() {
            console.log('Footer Navigation Component mounted.');
        },
        methods: {
            save_selections(saveType) {
                var suggestionIds = [];
                var self = this;
                this.timeBeltsArr.forEach(function (timeBelt) {
                    suggestionIds.push(timeBelt.id);
                });
                $('.save').prop('disabled', true);
                if (suggestionIds.length === 0) {
                    this.sweet_alert("Select the station you want to add", 'error');
                    $("#load_this").css({ opacity: 1 });
                    $('.save').prop('disabled', false);
                } else {
                    $("#load_this").css({ opacity: 0.2 });
                    // run toast showing progress
                    var msg = `${self.timeBeltsArr.length} suggestion(s) selected. Saving in progress, please wait...`;
                    this.sweet_alert(msg, 'info');

                    axios({
                        method: 'post',
                        url: '/agency/media-plan/select_plan',
                        data: {
                            data: JSON.stringify(suggestionIds),
                            mediaplan: this.planId
                        }
                    }).then((res) => {
                        $("#load_this").css({ opacity: 1 });
                        if (res.data.status === 'success') {
                            $('.save').prop('disabled', false);
                            if (saveType === 1) {
                                this.sweet_alert("Media plan successfully created!", 'success');
                                setTimeout(function () {
                                    location.href = '/agency/media-plan/createplan/'+self.planId;
                                }, 2000);
                            } else {
                                this.sweet_alert("Selected suggestions successfully saved!", 'success');
                            }
                        } else {
                            this.sweet_alert("Selected suggestions couldn't be saved, please try again", 'error');
                            $('.save').prop('disabled', false);
                        }
                    }).catch((error) => {
                        $("#load_this").css({ opacity: 1 });
                        console.log(error.response.data);
                        this.sweet_alert("An unknown error has occurred, please try again", 'error');
                        $('.save').prop('disabled', false);
                    });
                }
            }
        }
    }
</script>
