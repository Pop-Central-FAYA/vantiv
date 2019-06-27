<template>
    <table>
        <tr>
            <th>Day</th>
            <th>Program</th>
            <th>Time Belt</th>
        </tr>
        <tbody>
            <tr v-for="adslot in adslots" :key="adslot.id">
                <td>{{ adslot.day }}</td>
                <td>{{ adslot.program }}</td>
                <td>{{ adslot.time_belt_start_time }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
  export default {
        props : {
            adslots : {
                required : true,
                type : Array
            }
        }
    }
</script>