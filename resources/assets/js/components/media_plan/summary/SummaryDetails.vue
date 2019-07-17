<template>
   
                <div class="the_stats the_frame clearfix mb4 mt4">
                    <table class="display dashboard_campaigns">
                        <tbody>
                           <tr>
                            <td><span class="mr-2"><b>Client Name:</b></span>  {{ summarydataobj.client.company_name }}</td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Product Name:</b></span>  {{ summarydataobj.product_name }}</td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Flight Date:</b></span> {{ dateToYMD(summarydataobj.start_date) }} to {{ dateToYMD(summarydataobj.end_date) }} </td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Status:</b></span>  {{ summarydataobj.status }}</td>
                           </tr>
                        </tbody>
                    </table>
                </div>
</template>

<script>
    export default {
        props: {
            summaryData: Object,
        },
        data() {
            return {
                summarydataobj: this.summaryData, 
            };
        },
        mounted() {
            console.log('Suggestions Table Component mounted.')
        },   methods: {
            dateToYMD(date) {
                const months = ["Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                let current_datetime = new Date(date)
                return current_datetime.getDate() + "-" + months[current_datetime.getMonth()] + "-" + current_datetime.getFullYear()

            }
         }
    }
</script>
