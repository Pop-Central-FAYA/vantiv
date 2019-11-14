<template>
    <v-card>
        <v-card-text>
            <!-- <v-layout>
                <v-flex xs8>
                    <p class="net_word">{{ netTotalWord }}</p><hr>
                </v-flex>
            </v-layout> -->
            <v-layout>
                <v-flex xs1 class="justify-left">
                    <b>Terms: </b>
                </v-flex>
                <v-flex xs11 class="justify-right">
                    <p class="terms">1. The contract exclusively governs this transaction</p>
                    <p class="terms"> 2. The rate on this contract cannot be reviewed upwards but 
                        may be reviewed downwards when expedient to do so. 
                        If the rate is reviewed downwards at any time during the 
                        life of this contract the new lower rate will henceforth apply.</p>
                    <p class="terms">3. This order can be discontinued at any time. It is agreed that both 
                        parties shall fully discharge all their financial obligations at the 
                        rate in force on the date of discontinuance</p>
                    <p class="terms"> 4. Bills for service must be itemized and rendered monthly</p>
                    <p class="terms">5. Bills/Certificate of Broadcast must be received within 10 days 
                        of such broadcast, failing which the agency reserves the right to 
                        decline such payment unless special circumstances necessitating the 
                        acceptance of such bills are proved to exist.</p>
                    <p class="terms">6. Do not use advert materials if they are faulty or damaged. 
                        Contact the agency immediately as there will be no payments for poor placements,</p>
                    <p class="terms">7. All invoices not received within 10 days after the end of each month of the 
                        campaign will not be honoured for payment.</p>
                    <p class="terms">8. Failure to execute the campaign as stipulated in this contract will result in 
                        cancellation of the particular transaction and full refund of all monies advanced in respect thereof.</p>
                    <p class="terms">9. This contract shall be subject to the provisions of all relevant statutory tax laws.</p>
                    <p class="terms">10. Relevant copies of invoices related to this contract may be sent 
                        to the client without recourse to the Agency.</p>
                    <p class="terms">11. This contract is subject to Client independently commissioned Monitoring Agency's 
                        Compliance Report; except in cases where the concerned media house(s) is/are not 
                        covered by the commissioned Monitoring Agency.</p>
                    <p class="terms">12. All invoices must be accompanied with Original COT from the station.</p>
                    <p class="terms">13. Supplier should attach 2 photocopies of their invoice and COT.</p>
                    <p class="terms">14. Please DO NOT plough back any non compliant spot(s) without the approval of {{ company.name }}.</p>
                </v-flex>
            </v-layout>
        </v-card-text>
        <v-spacer></v-spacer>
        <v-card-text>
            <v-layout>
                <v-flex xs1></v-flex>
                <v-flex xs4>
                    <h3 class=" text-center signature"><span class="name">{{ fullName(mediaBuyer.firstname,mediaBuyer.lastname) }}</span> ,
                        <span class="date" v-if="mpo.mpo_details.submitted_at != null">{{ dateToHumanReadable(mpo.mpo_details.submitted_at) }},</span> <br>
                        <span class="date">{{ mediaBuyer.email }}</span> <span v-if="mediaBuyer.phone_number">,</span><br>
                        <span class="date" v-if="mediaBuyer.phone_number">{{ mediaBuyer.phone_number }}</span>
                    </h3>
                    <span>___________________________________________________</span>
                </v-flex>
                <v-flex xs2></v-flex>
                <v-flex xs4 class="text-right">
                    <h3 class="text-center signature" v-if="mpo.mpo_details.mpo_accepter">
                        <span class="name">{{ fullName(mpo.mpo_details.mpo_accepter.first_name, mpo.mpo_details.mpo_accepter.last_name) }}</span> ,
                        <span class="date">{{ dateToHumanReadable(mpo.mpo_details.accepted_at) }}</span>, <br>
                        <span class="date">{{ mpo.mpo_details.mpo_accepter.email }}</span>,<br>
                        <span class="date">{{ mpo.mpo_details.mpo_accepter.phone_number }}</span> <br>
                    </h3>
                    <h4 v-else>
                        Media House Rep (Name, Sign, TelNo, Email)
                    </h4>
                    <span>__________________________________________________</span>
                </v-flex>
                <v-flex xs1></v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
</template>
<script>
export default {
    props : {
        mediaBuyer : Object,
        company : Object,
        netTotalWord : String,
        mpo : Object
    },
    methods : {
        fullName(firstName, lastName) {
            return `${firstName} ${lastName}`
        }
    }
}
</script>
<style>
    .terms {
        font-size: 10px;
    }
    .net_word {
        margin-bottom: -5px;
        margin-top: -24px;
        text-transform: capitalize
    }
    .date {
        font-size: 12px;
        font-style: italic
    }
    .name {
        font-family: cursive
    }
    .signature {
        margin-bottom: -16px;
    }
</style>