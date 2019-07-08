<template>
  <v-layout>
    <v-dialog v-model="dialog" persistent max-width="600px">
        <template v-slot:activator="{ on }">
            <v-icon color="red" dark v-on="on" right>delete</v-icon>
        </template>
      <v-card>
        <v-card-text style="padding: 40px 20px;">
            Are you sure you want to delete media asset with file name "{{asset.file_name}}" and a duration of {{asset.duration}} seconds?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="default-vue-btn" dark @click="dialog = false">No</v-btn>
          <v-btn color="red" dark @click="deleteAsset()">Yes</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
  export default {
    props : {
      asset: Object
    },
    data() {
      return {
          dialog: false
      }
    },
    mounted() {
      console.log('Delete Asset Component mounted.');
    },
    methods: {
      deleteAsset() {
        $("#load_this").css({ opacity: 0.2 });
        var msg = "Deleting media asset, please wait";
        this.sweet_alert(msg, 'info');
        axios({
            method: 'get',
            url: '/media-assets/delete/'+this.asset.id
        }).then((res) => {
            console.log(res.data);
            if (res.data.status == 'success') {
                $('#load_this_div').css({opacity: 1});
                this.dialog = false;
                Event.$emit('latest-assets', res.data.data);
                this.sweet_alert("Media asset was successfully deleted", 'info');
            } else {
                this.sweet_alert('Media asset cannot be deleted, try again', 'error');
            }
        }).catch((error) => {
            this.sweet_alert('An unknown error has occurred, media assets cannot be delete. Please try again', 'error');
        });
      }
    }
  }
</script>