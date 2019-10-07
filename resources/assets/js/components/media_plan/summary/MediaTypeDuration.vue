<template>
  <div style="overflow: auto;">
    <table class="display dashboard_campaigns">
        <thead>
            <tr>
                <th rowspan="2"></th>
                <th class="station" rowspan="2" style="text-align: center; vertical-align: middle; font-weight: bold">STATION</th>
                <th colspan="7" style="vertical-align: middle; text-align: center; font-weight: bold">DAYS OF THE WEEK</th>
                <th colspan="1" style="text-align: center; vertical-align: middle; font-weight: bold">{{`${duration}"`}}</th>
                <th colspan="1" style="text-align: center; vertical-align: middle; font-weight: bold">VOL.DISC</th>
                <th colspan="1" style="text-align: center; vertical-align: middle; font-weight: bold">VALUE LESS </th>
                <th colspan="1" style="text-align: center; vertical-align: middle; font-weight: bold">AGENCY</th>
                <th colspan="1" style="text-align: center; vertical-align: middle; font-weight: bold">{{`${duration}"`}}</th>
                <th colspan="1" style="text-align: center; vertical-align: middle; font-weight: bold">TOTAL</th>
                <th colspan="1" style="text-align: center; vertical-align: middle; font-weight: bold">BONUS</th>
                <th colspan="1" style="text-align: center; vertical-align: middle; font-weight: bold">COST OF BONUS</th>
                <th colspan="1" style="text-align: center; vertical-align: middle; font-weight: bold">GROSS</th>
                <th colspan="1" style="text-align: center; vertical-align: middle; font-weight: bold">NET</th>
                <th colspan="1" style="text-align: center; vertical-align: middle; font-weight: bold">NET VALUE</th>
                <th v-for="(weeks, month) in monthlyWeeks" :key="month" style="text-align: center; vertical-align: middle; font-weight: bold" :colspan="weeks.length + 1">{{ (month) }}</th>
                <th colspan="1">TOTAL</th>
            </tr>
            <tr>
                <th style="font-weight: bold;">M</th>
                <th style="font-weight: bold;">TU</th>
                <th style="font-weight: bold;">W</th>
                <th style="font-weight: bold;">TH</th>
                <th style="font-weight: bold;">F</th>
                <th style="font-weight: bold;">SA</th>
                <th style="font-weight: bold;">SU</th>
                <th class="td-width-140" style="font-weight: bold; text-align: center;">GROSS UNIT RATE</th>
                <th class="td-width-140" style="font-weight: bold; text-align: center;">%</th>
                <th class="td-width-140" style="font-weight: bold; text-align: center;">VOL.DISC</th>
                <th class="td-width-140" style="font-weight: bold; text-align: center;">COMM</th>
                <th class="td-width-140" style="font-weight: bold; text-align: center;">NET UNIT RATE</th>
                <th class="td-width-140" style="font-weight: bold; text-align: center;">SPOTS</th>
                <th class="td-width-140" style="font-weight: bold; text-align: center;">SPOTS</th>
                <th class="td-width-140" style="font-weight: bold; text-align: center;">SPOTS</th>
                <th class="td-width-140" style="font-weight: bold; text-align: center;">VALUE</th>
                <th class="td-width-140" style="font-weight: bold; text-align: center;">VALUE</th>
                <th class="td-width-140" style="font-weight: bold; text-align: center;">AFTER BONUS SPOTS</th>
                <th class="td-width-65" v-for="week in monthlyWeeksHeader" :key="week" style="font-weight: bold; border-top: 2px solid #000000;">{{ week }}</th>
                <th class="td-width-65" style="font-weight: bold; text-align: center;">SPOTS</th>
            </tr>
        </thead>
        <tbody>
            <tr></tr>
            <template v-for="(stations, stationType) in data">
                <tr :key="stationType+'station-type'">
                    <td><b>{{ stationType.toUpperCase() }}</b></td>
                </tr>
                 <template v-for="(programs, station) in stations">
                    <tr :key="station"><td></td><td><b>{{ station }}</b></td></tr>
                    <tr v-for="(timebelt, key) in programs" :key="key+station" v-html="generateTableRow(timebelt)"></tr>
                 </template>
                <tr v-html="generateTotalStationTypeRow(data[stationType], stationType.toUpperCase())" :key="stationType+'-total'"></tr>
            </template>
            <tr v-html="generateSubTotalRow()"></tr>
        </tbody>
    </table>
  </div>
</template>

<style>
    td, th {
        border: 1px solid #cccccc;
        min-width: 50px;
    }
    td.station, th.station {
        min-width: 300px;
    }
    .td-width-140 {
        min-width: 140px;
        text-align: center;
    }
    .td-width-65 {
        min-width: 65px;
        text-align: center;
    }
</style>


<script>
  export default {
    props: {
        data: Object,
        monthlyWeeks: Object,
        duration: String,
    },
    data () {
      return {
        
      }
    },
    computed: {
        monthlyWeeksHeader() {
            let result = [];
            Object.keys(this.monthlyWeeks).forEach((month) => {
                let weeks = this.monthlyWeeks[month];
                let weeksCount = 1; let totalWeeks = weeks.length;
                weeks.forEach(week => {
                    if (weeksCount < totalWeeks) {
                        console.log(weeksCount);
                        result.push(`WK ${weeksCount}`);
                    } else {
                        result.push(`WK ${weeksCount}`);
                        result.push('Mthly Total');
                    }
                    weeksCount++;
                });
            });
            return result;
        }
    },
    mounted() {
        console.log(this.monthlyWeeksHeader);
    },
    methods: {
        generateTableRow(timebelt) {
            let rowHtml = "<td></td>";
            rowHtml += `<td class="station">${ timebelt.program }</td>`;
            Object.keys(timebelt.week_days).forEach(key => {
                let value = timebelt.week_days[key];
                if (value == 1) {
                    rowHtml += `<td>${ key}</td>`;
                } else {
                    rowHtml += `<td></td>`;
                }
            });
            rowHtml += `<td class="td-width-140">${ this.numberFormat(timebelt['gross_unit_rate'], 2)}</td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(timebelt['volume_discount'], 1)}%</td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(timebelt['value_less'], 2)}</td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(timebelt['agency_commission'], 1)}%</td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(timebelt['net_unit_rate'], 2)}</td>`;
            rowHtml += `<td class="td-width-140">${ timebelt['total_spots'] }</td>`;
            rowHtml += `<td class="td-width-140">${ timebelt['bonus_spots'] }</td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(timebelt['cost_bonus_spots'], 2)}</td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(timebelt['gross_value'], 2)}</td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(timebelt['net_value'], 2)}</td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(timebelt['net_value_after_bonus_spots'], 2)}</td>`;

            Object.keys(timebelt['month_weeks']).forEach((month) => {
                let weeks = timebelt['month_weeks'][month];
                let weeksCount = 1; let totalWeeks = weeks.length; let totalSlots = 0;
                weeks.forEach(week => {
                    if (weeksCount < totalWeeks) {
                         rowHtml += `<td class="td-width-65">${ week.slot}</td>`;
                    } else {
                        rowHtml += `<td class="td-width-65">${ week.slot}</td>`;
                        rowHtml += `<td class="td-width-65">${ totalSlots}</td>`;
                    }
                    weeksCount++; totalSlots += week.slot;
                });
            });
            rowHtml += `<td class="td-width-65">${ timebelt['total_spots']}</td>`;
            return rowHtml;
        },
        sumData(data, column) {
           let sumPropertyValue = (items, prop) => items.reduce((a, b) => a + b[prop], 0); 
           return sumPropertyValue(data, column);
        },
        generateTotalStationTypeRow(stations, station_type) {
            let total_spots = 0; let bonus_spots = 0; let cost_bonus_spots = 0.0; let net_value = 0.0;
            let net_value_after_bonus_spots = 0.0;
            Object.keys(stations).forEach(station => {
                total_spots += this.sumData(stations[station], 'total_spots');
                bonus_spots += this.sumData(stations[station], 'bonus_spots');
                cost_bonus_spots += this.sumData(stations[station], 'cost_bonus_spots');
                net_value += this.sumData(stations[station], 'net_value');
                net_value_after_bonus_spots += this.sumData(stations[station], 'net_value_after_bonus_spots');
            });
            let rowHtml = `<td></td><td class="station"><b>TOTAL ${station_type}</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>`;
            rowHtml += `<td class="td-width-140"></td>`;
            rowHtml += `<td class="td-width-140"></td>`;
            rowHtml += `<td class="td-width-140"></td>`;
            rowHtml += `<td class="td-width-140"></td>`;
            rowHtml += `<td class="td-width-140"></td>`;
            rowHtml += `<td class="td-width-140">${ total_spots }</td>`;
            rowHtml += `<td class="td-width-140">${ bonus_spots }</td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(cost_bonus_spots, 2)}</td>`;
            rowHtml += `<td class="td-width-140"></td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(net_value, 2)}</td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(net_value_after_bonus_spots, 2)}</td>`;

            this.monthlyWeeksHeader.forEach(element => {
                rowHtml += `<td class="td-width-65"></td>`;
            });

            rowHtml += `<td class="td-width-65">${ total_spots }</td>`;
            return rowHtml;
        },
        generateSubTotalRow() {
            let total_spots = 0; let bonus_spots = 0; let cost_bonus_spots = 0.0; let net_value = 0.0;
            let net_value_after_bonus_spots = 0.0;
            let station_types = ['national_stations', 'cable_stations', 'regional_stations'];

            Object.keys(this.data).forEach(station_type=> {
                Object.keys(this.data[station_type]).forEach(station => {
                    total_spots += this.sumData(this.data[station_type][station], 'total_spots');
                    bonus_spots += this.sumData(this.data[station_type][station], 'bonus_spots');
                    cost_bonus_spots += this.sumData(this.data[station_type][station], 'cost_bonus_spots');
                    net_value += this.sumData(this.data[station_type][station], 'net_value');
                    net_value_after_bonus_spots += this.sumData(this.data[station_type][station], 'net_value_after_bonus_spots');
                });
            })
            
            let rowHtml = `<td></td><td class="station"><b>SUB TOTAL (NAIRA)</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>`;
            rowHtml += `<td class="td-width-140"></td>`;
            rowHtml += `<td class="td-width-140"></td>`;
            rowHtml += `<td class="td-width-140"></td>`;
            rowHtml += `<td class="td-width-140"></td>`;
            rowHtml += `<td class="td-width-140"></td>`;
            rowHtml += `<td class="td-width-140">${ total_spots }</td>`;
            rowHtml += `<td class="td-width-140">${ bonus_spots }</td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(cost_bonus_spots, 2)}</td>`;
            rowHtml += `<td class="td-width-140"></td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(net_value, 2)}</td>`;
            rowHtml += `<td class="td-width-140">${ this.numberFormat(net_value_after_bonus_spots, 2)}</td>`;
            this.monthlyWeeksHeader.forEach(element => {
                rowHtml += `<td class="td-width-65"></td>`;
            });
            rowHtml += `<td class="td-width-65">${ total_spots }</td>`;
            return rowHtml;
        }
    }
  }
</script>