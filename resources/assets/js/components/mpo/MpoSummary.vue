<template>
    <v-card>
        <v-layout>
            <table>
                <thead>
                    <tr class="rule">
                        <th class="text-center rule" colspan="34"> COSTING </th>
                    </tr>
                    <tr class="rule">
                        <th class="text-left rule">Inserts</th>
                        <th class="text-left rule" colspan="29">Description</th>
                        <th class="text-left rule" colspan="2">Rate (&#8358;)</th>
                        <th class="text-left rule" colspan="2">Total Amount (&#8358;)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in timeBeltSummary" :key="item.id" class="rule">
                        <td class="rule">{{ item.total_spot }}</td>
                        <td colspan="29" class="rule">Publisher Name : {{ item.publisher_name }} <br>
                                                      Program : {{ item.program }} <br>
                                                      Program Time : {{ item.program_time }} <br>
                                                      Duration : {{ item.duration }} Seconds
                        </td>
                        <td colspan="2" class="text-right rule">{{ formatAmount(item.rate) }}</td>
                        <td colspan="2" class="text-right rule">{{ formatAmount(item.gross_total) }}</td>
                    </tr>
                    <tr class="rule">
                        <td class="rule"></td>
                        <td colspan="29" class="text-right rule">Sub Total</td>
                        <td colspan="2" class="text-right rule"></td>
                        <td colspan="2" class="text-right rule">{{ formatAmount(mpoCosting.subTotal) }}</td>
                    </tr>
                    <tr class="rule">
                        <td class="rule"></td>
                        <td colspan="29" class="text-right rule">Volume Discount</td>
                        <td colspan="2" class="text-right rule">{{ formatAmount(mpoCosting.volumeDiscount) }}%</td>
                        <td colspan="2" class="text-right rule">{{ formatAmount(mpoCosting.volumeDiscountValue) }}</td>
                    </tr>
                    <tr class="rule">
                        <td class="rule"></td>
                        <td colspan="29" class="text-right rule"></td>
                        <td colspan="2" class="text-right rule"></td>
                        <td colspan="2" class="text-right rule">{{ formatAmount(mpoCosting.netTotalLessVolumeDiscount) }}</td>
                    </tr>
                    <tr class="rule">
                        <td class="rule"></td>
                        <td colspan="29" class="text-right rule">Agency Commission</td>
                        <td colspan="2" class="text-right rule">15%</td>
                        <td colspan="2" class="text-right rule">{{ formatAmount(mpoCosting.agencyCommission) }}</td>
                    </tr>
                    <tr class="rule">
                        <td class="rule"></td>
                        <td colspan="29" class="text-right rule"></td>
                        <td colspan="2" class="text-right rule"></td>
                        <td colspan="2" class="text-right rule">{{ formatAmount(mpoCosting.netTotalLessAgencyCommission) }}</td>
                    </tr>
                    <tr class="rule">
                        <td class="rule"></td>
                        <td colspan="29" class="text-right rule">VAT</td>
                        <td colspan="2" class="text-right rule">5%</td>
                        <td colspan="2" class="text-right rule">{{ formatAmount(mpoCosting.vat) }}</td>
                    </tr>
                    <tr class="rule last-record">
                        <td colspan="32" class="text-center rule">Total Amount Payable</td>
                        <td colspan="2" class="text-right rule">{{ formatAmount(mpoCosting.totalPayable) }}</td>
                    </tr>
                </tbody>
            </table>
        </v-layout>
    </v-card>
</template>
<script>
export default {
    props : {
        timeBeltSummary : Array,
        totalBudget : Number,
        netTotal : Number,
        mpoDetails : Object,
        mpoCosting : Object
    }
}
</script>
<style >
    .layout  {
        margin-top : 20px !important;
    }
    .left-item {
        width: 140px; 
        display: inline-block;
        text-align: right
    }
    .right-item {
        display: inline-block;
        width: 150px;
        text-align: left;
    }
    .outer {
        display: block
    }
    td.rule {
        border-right: solid 1px;
        border-bottom: 0 !important;
        padding: 1px 24px !important;
        font-size: 13px !important;
    }
    tr.rule {
        border-top : solid 1px;
        border-bottom : solid 1px;
    }
    tr.last-record {
        border-bottom: solid !important;
    }
    th.rule {
        border-top : solid 1px;
        border-bottom : solid 1px;
        border-right: solid 1px;
    }
</style>