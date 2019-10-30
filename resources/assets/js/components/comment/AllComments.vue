<template>
    <div class="text-xs-center" style="display: inline-block">
        <v-menu :close-on-content-click="false" :nudge-width="150">
            <template v-slot:activator="{ on: menu }">
                <v-btn dark v-on="{ ...menu }" color="default-vue-btn" large><v-icon left>comment</v-icon> Comments</v-btn>
            </template>
            <v-container grid-list-md class="p-0">
                <v-layout row wrap class="white-bg">
                    <v-flex xs12 sm12 md12 lg12 class="comment-panel">
                         <v-card max-width="500" class="mx-auto">
                             <v-card-title>
                                 <h3>Comments</h3>
                             </v-card-title>
                             <v-card-text class="p-0">
                                 <v-list two-line>
                                    <template v-for="(comment, index) in comments">
                                        <v-list-tile :key="comment.id+'li'" avatar>
                                            <v-list-tile-content>
                                                <v-list-tile-sub-title class="text--primary">{{ comment.comment }}</v-list-tile-sub-title>
                                                <v-list-tile-sub-title>By {{ `${comment.user.firstname} ${comment.user.lastname}`  }} {{ timeAgo(comment.created_at) }}</v-list-tile-sub-title>
                                            </v-list-tile-content>
                                        </v-list-tile>
                                        <v-divider v-if="index + 1 < comments.length" :key="index"></v-divider>
                                    </template>
                                    <v-list-tile v-if="comments.length == 0">
                                        <v-list-tile-content>
                                            <v-list-tile-sub-title class="text--primary">No comments</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                </v-list>
                                <v-layout>
                                    <v-flex xs12>
                                        <v-textarea solo label="Enter comment" v-model="comment" class="mb-0"></v-textarea>
                                        <v-btn @click="storeComment()" block color="default-vue-btn my-0" dark>Send</v-btn>
                                    </v-flex>
                                </v-layout>
                             </v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-menu>
    </div>
 
</template>

<style>
    .v-menu__content {
        top: 250px !important;
    }
    textarea {
        height: 90px;
    }
    .v-list__tile__sub-title {
        white-space: normal !important;
    }
    .v-list--two-line {
        min-height: 50px;
        max-height: 200px;
        overflow: auto;
    }
    .v-list--two-line .v-list__tile {
        height: auto;
    }
    .comment-panel {
        box-shadow: 0 2px 9px 1px rgba(0,0,0,.2), 0 1px 8px 0px rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);
    }
    .comment-panel .v-text-field.v-text-field--enclosed .v-text-field__details {
        margin-bottom: 0px;
    }
    .comment-panel .v-messages {
        min-height: 2px;
    }
    .comment-panel .v-expansion-panel__header {
        padding: 8px 24px;
        min-height: 20px;
    }
    .comment-panel .v-expansion-panel__header .v-icon {
        display: block;
    }
</style>

<script>
  export default {
    props: {
      routes: Object // typically this will include a get and store comments routes
    },
    data () {
      return {
        comment: '',
        comments: [],
        msg: '',
      }
    },
    mounted() {
        console.log('Display All Comments Component mounted.');
        this.getComments();
    },
    methods: {
      getComments() {
        axios({
            method: 'get',
            url: this.routes.all
        }).then((res) => {
            this.comments = res.data.data;
        }).catch((error) => {
            this.sweet_alert("Comments could not be retrieved", 'error');
        });
      },
      storeComment() {
        if (this.comment == '') {
            return;
        }
        axios({
            method: 'post',
            url: this.routes.store,
            data: {
                comment: this.comment
            }
        }).then((res) => {
            this.comments.push(res.data.data);
            this.sweet_alert("Comment was successful", 'success');
            this.comment = "";
        }).catch((error) => {
            this.sweet_alert("Comment could not be sent", 'error');
        });
      }
    }
  }
</script>