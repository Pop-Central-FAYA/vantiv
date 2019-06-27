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
                <td>{{ mpo.budget }}</td>
                <td>{{ mpo.ad_slots }}</td>
                <td>{{ mpo.status }}</td>
                <td><a href="#">Export</a> | <a href="#">Submit</a></td>
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
        }
    }
</script>