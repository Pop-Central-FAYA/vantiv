<template>
    <table>
        <tr v-for="ad in ads" :key="ad.id">
            <td style="border : 0;">
                <div class="card_table">
                    {{ ad.campaign_name }} <br>
                     {{ ad.client_name }} <br>
                      {{ ad.duration }}
                </div>
            </td>
        </tr>
    </table>
</template>
<script>
    export default {
        props : {
            ads : {
                required : true,
                type : Array
            }
        }
    }
</script>

