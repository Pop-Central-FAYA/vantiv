<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="650px">
            <template v-slot:activator="{ on }">
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-icon color="#01c4ca" dark left v-on="on" @click="populateModal()">fa-share</v-icon>
                    </template>
                    <span>Generate and copy share link</span>
                </v-tooltip>
            </template>
            <v-card>
                <v-card-title>
                    <span class="headline"> {{ mpo.vendor }}</span>
                </v-card-title>
                <v-card-text>
                    <div class="card">
                        <p>{{ shareLink.url }}</p>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" class="default-vue-btn" dark @click="dialog = false">Close</v-btn>
                    <v-btn color="green" class="default-vue-btn" dark @click="copyToClipboard(shareLink.url)"> Copy to clipboard</v-btn>
                </v-card-actions>
            </v-card>
            
        </v-dialog>
    </v-layout>
</template>
<script>
export default {
    props : {
        mpo: {
            required : true,
            type : Object
        }
    },
    data() {
        return {
            dialog: false,
            email : '',
            shareLink : {}
        }
    },
    created() {
        var self = this
        Event.$on('share-link', function (share_link) {
            self.shareLink = share_link;
        });
        this.getShareLink()
    },
    methods : {
        getShareLink : function() {
            axios({
                method: 'get',
                url: `/api/mpos/${this.mpo.id}/share-links`
            }).then((res) => {
                let result = res.data.data;
                if (result != null) {
                    this.shareLink = result;                
                }
            }).catch((error) => {
                this.sweet_alert('An unknown error has occurred, link cannot be retrieved. Please try again', 'error');
            });
        },
        populateModal : function() {
            if(this.shareLink.is_expired || Object.keys(this.shareLink).length === 0){
                this.addShareLink()
            }
            this.dialog = true
        },
        copyToClipboard : function(url){
            let copyListener = event => {
                document.removeEventListener("copy", copyListener, true);
                event.preventDefault();
                let clipboardData = event.clipboardData;
                clipboardData.clearData();
                clipboardData.setData("text/plain", url);
            };
            document.addEventListener("copy", copyListener, true);
            try {
                document.execCommand("copy")
                var message = 'copied '+url+' to clipboard'
                this.sweet_alert(message, 'success');
            } catch (error) {
                this.sweet_alert('Oops, unable to copy '+error, 'error');
                return false;
            }
        },
        addShareLink : function () {
            var msg = "Processing request, please wait...";
            this.sweet_alert(msg, 'info');
            axios({
                method: 'POST',
                url: `/api/mpos/${this.mpo.id}/share-links`,
                data: {}
            }).then((res) => {
                if (res.data.status === 'success') {
                    this.sweet_alert(res.data.message, 'success');
                    this.email = ''
                    this.shareLink = res.data.data
                } else {
                    this.sweet_alert(res.data.message, 'error');
                }
            }).catch((error) => {
                this.sweet_alert(error.response.data.message, 'error');
            });
        }
    }
}
</script>
<style>
    .modal {
    overflow-y: auto;
    }

    .modal-open {
    overflow: auto;
    }
</style>


