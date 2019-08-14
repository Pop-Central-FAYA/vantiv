<template>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="timeBelts" hide-actions :pagination.sync="pagination">
      <template v-slot:items="props">
        <tr>
          <td class="text-xs-left">{{ props.item.day }}</td>
          <td class="text-xs-left">{{ format_time(props.item.start_time) }} - {{ format_time(props.item.end_time) }}</td>
          <td class="text-xs-left">{{ props.item.program }}</td>
          <td class="text-xs-left">{{ format_audience(props.item.total_audience) }}</td>
          <td class="text-xs-left">
              <v-icon :disabled="props.item.is_selected" color="green" dark @click="addTimebelt(props.item)">add</v-icon>
          </td>
        </tr>
      </template>
    </v-data-table>
</template>

<style>
  tbody tr:hover {
    background-color: transparent !important;
    cursor: pointer;
  }
  tbody:hover {
    background-color: rgba(0, 0, 0, 0.12);
  }
  .custom-vue-table .v-icon {
    color: #44c1c9 !important;
    font-weight: 600;
  }
  .custom-vue-table .theme--dark.v-icon.v-icon--disabled {
    color: hsla(184, 55%, 53%, 0.30)!important;
  }
</style>

<script>
  export default {
    props: {
      stationTimeBelts: Array
    },
    data () {
      return {
        timeBelts: this.stationTimeBelts,
        daysOfWeek: { Monday: 1, Tuesday: 2, Wednesday: 3, Thursday: 4, Friday: 5, Saturday: 6, Sunday: 7 },
        headers: [
          { text: 'Day', align: 'left', value: 'number_day', width: '25%' },
          { text: 'Time Belt', value: 'end_time', width: '25%' },
          { text: 'Program', value: 'program', width: '25%' },
          { text: 'Audience', value: 'total_audience', width: '24%' },
          { text: 'Actions', value: 'name', width: '1%', sortable: false }
        ],
        pagination: {
            rowsPerPage: -1,
            sortBy: ['number_day'],
            descending: false
        },
      }
    },
    created() {
        this.sortTimeBeltsByDay();
        this.setTimeBeltSelectionState();
        var self = this;
        Event.$on('timebelt-unselected', function (timeBelt) {
            self.unSelectTimebelt(timeBelt);
        });
    },
    methods: {
        sortTimeBeltsByDay() {
            var self = this;
            this.timeBelts.sort(function (a, b) {
                return self.daysOfWeek[a.day] - self.daysOfWeek[b.day];
            });
        },
        setTimeBeltSelectionState() {
            var self = this;
            this.timeBelts.forEach(element => {
                element.number_day = self.daysOfWeek[element.day];
                element.is_selected = false;
            });
        },
        addTimebelt(time_belt) {
            this.timeBelts.forEach(element => {
                if (element.id == time_belt.id) {
                    element.is_selected = true;
                }
            });
            this.sortTimeBeltsByDay();
            Event.$emit('timebelt-to-add', time_belt);
            var time = `${this.format_time(time_belt.start_time)} - ${this.format_time(time_belt.end_time)}`;
            var successMsg = `${time_belt.station} - ${time_belt.program}  showing on  ${time_belt.day}  ${time} added successfully`;
            this.sweet_alert(successMsg, 'success');
        },
        unSelectTimebelt(time_belt) {
            this.timeBelts.forEach(element => {
                if (element.id == time_belt.id) {
                    element.is_selected = false;
                }
            });
            this.sortTimeBeltsByDay();
        }
    }
  }
</script>