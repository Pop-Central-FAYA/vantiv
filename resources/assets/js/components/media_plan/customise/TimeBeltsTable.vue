<template>
    <v-data-table class="custom-vue-table elevation-1" :headers="headers" :items="timeBeltList" hide-actions :pagination.sync="pagination" @input="showMyData($event)">
      <template v-slot:items="props">
        <tr>
          <td class="text-xs-left">{{ props.item.day }}</td>
          <td class="text-xs-left">{{ format_time(props.item.start_time) }} - {{ format_time(props.item.end_time) }}</td>
          <td class="text-xs-left">{{ props.item.program }}</td>
          <td class="text-xs-left">{{ format_audience(props.item.total_audience) }}</td>
          <td class="text-xs-left">{{ props.item.rating }}</td>
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
      station: Object,
      selectedTimeBelts: Array,
    },
    data () {
      return {
        timeBeltList: [],
        daysOfWeek: { Monday: 1, Tuesday: 2, Wednesday: 3, Thursday: 4, Friday: 5, Saturday: 6, Sunday: 7 },
        headers: [
          { text: 'Day', align: 'left', value: 'number_day', width: '20%' },
          { text: 'Time Belt', value: 'end_time', width: '25%' },
          { text: 'Program', value: 'program', width: '25%' },
          { text: 'Audience', value: 'total_audience', width: '24%' },
          { text: 'Rating', value: 'rating', width: '5%' },
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
        var self = this;

        Event.$on('timebelt-unselected', function (timeBelt) {
            self.unSelectTimebelt(timeBelt);
        });

        var privateEvent = `${this.station.key}-timebelt-table-opened`;
        Event.$on(privateEvent, function (station) {
            self.loadTimeBelts(station);
        });
    },
    mounted() {
        console.log(`Timebelt table for ${this.station.name} mounted.`);
    },
    methods: {
        loadTimeBelts(station) {
          if (this.isEmpty(this.timeBeltList)) {
            this.createNewTimeBeltRatings();
          }
        },
        renderTimeBelts(timeBeltList) {
          this.timeBeltList = timeBeltList;
          this.sortTimeBeltsByDay();
          this.setTimeBeltSelectionState();
        },
        sortTimeBeltsByDay() {
            var self = this;
            this.timeBeltList.sort(function (a, b) {
                return self.daysOfWeek[a.day] - self.daysOfWeek[b.day];
            });
        },
        setTimeBeltSelectionState() {
            var self = this;
            this.timeBeltList.forEach(element => {
                element.number_day = self.daysOfWeek[element.day];
                let is_selected = this.selectedTimeBelts.some(timebelt => (timebelt['key'] === element.key));
                if (is_selected) {
                  element.is_selected = true;
                } else {
                  element.is_selected = false;
                }
            });
        },
        addTimebelt(timeBelt) {
            this.timeBeltList.forEach(element => {
                if (element.key == timeBelt.key) {
                    element.is_selected = true;
                }
            });
            this.sortTimeBeltsByDay();
            Event.$emit('timebelt-to-add', timeBelt);
            var time = `${this.format_time(timeBelt.start_time)} - ${this.format_time(timeBelt.end_time)}`;
            var successMsg = `${timeBelt.station} - ${timeBelt.program}  showing on  ${timeBelt.day}  ${time} added successfully`;
            this.sweet_alert(successMsg, 'success');
        },
        unSelectTimebelt(timeBelt) {
            this.timeBeltList.forEach(element => {
                if (element.key == timeBelt.key) {
                    element.is_selected = false;
                }
            });
            this.sortTimeBeltsByDay();
        },
        createNewTimeBeltRatings() {
            var msg = `Getting timebelts for ${this.station.name}`;
            this.sweet_alert(msg, 'info', 60000);
            axios({
                method: 'post',
                url: this.station.links.timebelt_ratings,
            }).then((res) => {
                this.sweet_alert('Ratings retrieved', 'success');
                this.renderTimeBelts(res.data.data);
            }).catch((error) => {
                if (error.response && (error.response.status == 422)) {
                    this.displayServerValidationErrors(error.response.data.errors);
                } else {
                    this.sweet_alert('An unknown error has occurred, please try again', 'error');
                }
            })
        }
    }
  }
</script>