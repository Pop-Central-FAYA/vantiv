<template>
    <!-- client charts -->
    <div class="clearfix dashboard_pies mb3">
        <div class="" v-for="(channel, key ) in mediaChannels" v-bind:key="key">
            <div class="pie_icon margin_center">
                <img :src="channel.icon_url">
            </div>
            <p class="align_center">{{key}}</p>
            <div class="_pie_chart margin_center" style="height: 100px">
              <highcharts :options="chartOptionsArr[key]" :highcharts="hcInstance"></highcharts>
            </div>
            <ul style="margin-left: 9px;" v-if="Object.values(channel.ratings).length > 0">
                <li class="pie_legend active"><span class="weight_medium">{{ channel.ratings['percentage_active'] }}%</span> Active</li>
                <li class="pie_legend pending"><span class="weight_medium">{{ channel.ratings['percentage_pending'] }}%</span> Pending</li>
                <li class="pie_legend finished"><span class="weight_medium">{{ channel.ratings['percentage_finished'] }}%</span> Finished</li>
            </ul>
            <ul style="margin-left: 9px;" v-else>
                <li class="pie_legend active"><span class="weight_medium">0%</span> Active</li>
                <li class="pie_legend pending"><span class="weight_medium">0%</span> Pending</li>
                <li class="pie_legend finished"><span class="weight_medium">0%</span> Finished</li>
            </ul>
        </div>
    </div>
</template>

<style>
  rect.highcharts-background {
    fill: transparent;
  }
</style>

<script>
  import Highcharts from 'highcharts';
  Highcharts.setOptions({
      lang: {
      decimalPoint: '.',
      thousandsSep: ','
      }
  });

  export default {
    props: {
      mediaChannels: Object,
    },
    data () {
      return {
        hcInstance: Highcharts,
      }
    },
    computed: {
      chartOptionsArr() {
        var optionsByChannel = [];
        var self = this;
        Object.keys(this.mediaChannels).forEach((key) => {
          optionsByChannel[key] = {
            chart: {
              renderTo: 'container',
              type: 'pie',
              height: 150,
              width: 150
            },
            title: { text: '' },
            credits: { enabled: false },
            plotOptions: {
              pie: {
                allowPointSelect: false,
                dataLabels: {
                    enabled: false,
                    format: '{point.name}'
                }
              }
            },
            exporting: { enabled: false },
            series: [{
              innerSize: '30%',
              data: [
                {name: 'Active', y: self.mediaChannels[key]['ratings']['percentage_active'], color: '#00C4CA'},
                {name: 'Pending', y: self.mediaChannels[key]['ratings']['percentage_pending'], color: '#E89B0B' },
                {name: 'Finished', y: self.mediaChannels[key]['ratings']['percentage_finished'], color: '#E8235F'}
              ]
            }]
          };
        });
        return optionsByChannel;
      }
    },
    mounted() {
        console.log('Display campaign summary chart Component mounted.');
    }
  }
</script>