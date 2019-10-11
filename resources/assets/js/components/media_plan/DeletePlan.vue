<template>
  <v-layout>
    <v-dialog v-model="dialog" persistent max-width="600px">
        <template v-slot:activator="{ on }">
            <v-icon :disabled="isMediaPlanPastReviewStage(plan.status)" color="red" dark v-on="on" right>delete</v-icon>
        </template>
      <v-card>
        <v-card-text style="padding: 40px 20px;">
            Are you sure you want to delete media plan with campaign name "{{plan.campaign_name}}"
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="default-vue-btn" dark @click="dialog = false">No</v-btn>
          <v-btn color="red" dark @click="deletePlan()">Yes</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<style>
  .v-icon.v-icon--disabled.v-icon--link.v-icon--right.material-icons.theme--dark.red--text {
    color: hsl(4, 90%, 58%)!important;
  }
</style>

<script>
  export default {
    props : {
      plan: Object
    },
    data() {
      return {
          dialog: false
      }
    },
    mounted() {
      console.log('Delete Plan Component mounted.');
    },
    methods: {
      deletePlan() {
        $("#load_this").css({ opacity: 0.2 });
        var msg = "Deleting media plan, please wait";
        this.sweet_alert(msg, 'info');
        axios({
            method: 'delete',
            url: this.plan.routes.delete
        }).then((res) => {
            console.log(res.data);
            $('#load_this_div').css({opacity: 1});
            this.dialog = false;
            Event.$emit('media-plan-deleted', this.plan.id);
            this.sweet_alert("Media plan was successfully deleted", 'info');
        }).catch((error) => {
            this.sweet_alert('An unknown error has occurred, media plan cannot be delete. Please try again', 'error');
        });
      }
    }
  }
</script>