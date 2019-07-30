<template>
    <v-app>
        <v-dialog v-model="editDialog" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline"> {{ program_time_belt.program_name }} |
                        {{ program_time_belt.date }} |
                        ({{ program_time_belt.time_belt }})
                        </span>
                </v-card-title>
                <v-card-title>
                    {{ sumDurationInAdBreak(program_time_belt.ads_schedules) }} seconds used from {{ getAdPattern(program_time_belt.ads_schedules) }}
                </v-card-title>
                <v-card-text>
                    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="program_time_belt.ads_schedules" :search="search">
                        <template v-slot:items="props">
                            <td>{{ props.item.client_name }}
                            </td>
                            <td class="text-xs-left">{{ props.item.campaign_name }}
                            </td>
                            <td class="text-xs-left">{{ props.item.duration }} Seconds</td>
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="error" dark @click="editDialog = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-app> 
</template>
<script>
export default {
    data () {
        return {
            search: '',
            editDialog : false,
            headers: [
                { text: 'Client', align: 'left', value: 'client' },
                { text: 'Campaign', value: 'campaign' },
                {text: 'Duration', value: 'duration'}
            ],
        }
    },
    created () {
        var self = this
        Event.$on('display-ads-modal', function(modal) {
            self.editDialog = modal
        })
    },
    props : {
        program_time_belt : {
            required : true,
            type : Object
        }
    },
    methods : {
        sumDurationInAdBreak : function(ad_break) {
            if(ad_break){
                return ad_break.reduce((prev,next) => prev + next.duration, 0);
            }
        },
        getAdPattern : function(ad_break) {
            if(ad_break){
                return ad_break[0].ad_pattern
            }
        }
    }
}
</script>
