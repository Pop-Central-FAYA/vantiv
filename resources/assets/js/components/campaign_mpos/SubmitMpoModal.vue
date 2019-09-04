<template>
    <v-layout>
        <v-dialog v-model="dialog" persistent max-width="600px">
            <template v-slot:activator="{ on }">
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-icon color="#01c4ca" dark left v-on="on" @click="dialog = true">fa-clipboard-list</v-icon>
                    </template>
                    <span>Submit to vendor</span>
                </v-tooltip>
            </template>
            <v-card>
                <v-card-title>
                    <span class="headline"> {{ mpo.station }}</span>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-form>
                            <v-layout wrap>
                                <v-flex xs12 sm12 md12>
                                    <span>
                                        Email
                                    </span>
                                    <v-text-field v-validate="'required|email'" 
                                    type="text" placeholder="Vendor's Email" 
                                    name="email" v-model="email"></v-text-field>
                                    <span class="text-danger" v-show="errors.has('email')">{{ errors.first('email') }}</span>
                                </v-flex>
                            </v-layout>
                        </v-form>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" class="default-vue-btn" dark @click="dialog = false">Close</v-btn>
                    <v-btn color="green" class="default-vue-btn" dark @click="submitToVendor()"> Submit</v-btn>
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
            shareLink : {},
        }
    },
    mounted() {
        this.getShareLink()
    },
    methods : {
        getShareLink : function() {
            axios({
                method: 'get',
                url: `/mpos/${this.mpo.id}/share-links`
            }).then((res) => {
                let result = res.data.data;
                if (result != null) {
                    this.shareLink = result;                
                }
            }).catch((error) => {
                this.sweet_alert('An unknown error has occurred, link cannot be retrieved. Please try again', 'error');
            });
        },
        submitToVendor : async function () {
            let isValid = await this.$validator.validate().then(valid => {
                if (!valid) {
                    return false;
                } else {
                    return true;
                }
            });
            if (!isValid) {
                return false;
            }
            var msg = "Processing request, please wait...";
            this.sweet_alert(msg, 'info');
            if(this.shareLink.is_expired || Object.keys(this.shareLink).length === 0){
                this.addShareLink()
            }else{
                this.submit(this.shareLink)
            }
        },
        addShareLink : function () {
            axios({
                method: 'POST',
                url: `/mpos/${this.mpo.id}/share-links`,
                data: {}
            }).then((res) => {
                if (res.data.status === 'success') {
                    Event.$emit('share-link', res.data.data)
                    this.submit(res.data.data.url)
                } else {
                    this.sweet_alert(res.data.message, 'error');
                }
            }).catch((error) => {
                this.sweet_alert(error.response.data.message, 'error');
            });
        },
        submit : function(url) {
            axios({
                method: 'POST',
                url: `/mpos/${this.mpo.id}/submit`,
                data: {url : url, email : this.email}
            }).then((res) => {
                if (res.data.status === 'success') {
                    this.sweet_alert(res.data.message, 'success');
                    this.email = ''
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


