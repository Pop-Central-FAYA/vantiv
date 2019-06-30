<template>
    <table>
        <tr>
            <th>Station</th>
            <th>Budget</th>
            <th>Adslots</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <tbody>
            <tr v-for="mpo in mpos" :key="mpo.id">
                <td>{{ mpo.station }}</td>
                <td>{{ format_audience(mpo.budget) }}</td>
                <td>{{ mpo.ad_slots }}</td>
                <td>{{ mpo.status }}</td>
                <td><button @click="exportMpo(mpo.id)">Export</button> | <a href="#">Submit</a> | <button @click="adslotList(mpo.id)">View Adslots</button></td>
            </tr>
        </tbody>
    </table>
</template>

<script>
  export default {
        props : {
            mpos : {
                required : true,
                type : Array
            }
        },
        methods : {
            adslotList : function(mpo_id) {
                return window.location.href = '/campaigns/mpo/details/'+mpo_id
            },
            exportMpo : function(mpo_id) {
                var msg = "Generating Excel Document, Please wait";
                this.sweet_alert(msg, 'info');
                return window.location.href = '/campaigns/mpo/export/'+mpo_id
            }
        }
    }
</script>