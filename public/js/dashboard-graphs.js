
const numberFormatter = new Intl.NumberFormat('en-US', {});

class DashboardGraph {

    constructor($chart_container) {
        this.$chart_container = $chart_container;
        this.chart_element = this.$chart_container.find("#periodicChart").attr('id');
        this.$form_element = $chart_container.find("#filter-form");
        this.addOnChangeEvents();
    }

    initChart(data) {
        var options = {
            chart: {type: 'column'},
            title: {text: ''},
            credits: {enabled: false},
            exporting: {enabled: false},
            tooltip: {crosshairs: true,shared: true},
            xAxis: {title: {text: 'Months'}, categories: data.labels, crosshair: true},
            lang: {noData: "No data to display for the filter options"},
            noData: {style: {fontWeight: 'bold', fontSize: '15px', color: '#303030'}}
        };
        var custom_options = this.getChartOptions(data);
        options['yAxis'] = custom_options['yAxis'];
        options['series'] = custom_options['series'];
        this.chart_object = Highcharts.chart(this.chart_element, options);
        return this.chart_object;
    }

    getChartOptions(data) {
        var options = {};
        switch (data.report_type) {
            case 'station_revenue':
                options['yAxis'] = {
                    'title': {'text': 'Revenue'},
                    'labels': {
                        formatter: function() {
                            return (String.fromCharCode(0x20A6) + numberFormatter.format(this.value)).replace(".00", "");
                        }
                    }
                };
                if (this.seriesHasData(data.estimated_value)) {
                    options['series'] = [
                        {name: 'Estimated Revenue', data: data.estimated_value, marker: {symbol: 'square'}},
                        {name: 'Actual Revenue', data: data.actual_value, marker: {symbol: 'square'}}
                    ];
                } else {
                    options['series'] = [];
                }
                break;
            case 'active_campaigns':
                options['yAxis'] = {
                    title: {text: 'Number of Approved Campaigns'},
                    labels: {
                        formatter: function() {return numberFormatter.format(this.value).replace(".00", "");}
                    }
                };
                if (this.seriesHasData(data.values)) {
                    options['series'] = [{name: 'Campaigns By Month', data: data.values, marker: {symbol: 'square'}}];
                } else {
                    options['series'] = [];
                }

                break;
            case 'spots_sold':
                options['yAxis'] = {
                    title: {text: 'Percentage of Spots Sold'}, 
                    floor: 0, 
                    ceiling: 100,
                    labels: {format: '{value}%'},
                    startOnTick: true,
                    endOnTick: true,
                };
                if (this.seriesHasData(data.values)) {
                    options['series'] = [{name: 'Adslots', data: data.values, marker: {symbol: 'square'}}];
                } else {
                    options['series'] = [];
                }
                break;
            default:
                options['yAxis'] = {};
                options['series'] = [];
        }

        options['yAxis']['id'] = 'label-axis'
        return options;
    }

    seriesHasData(series_data) {
        var total = series_data.reduce(function(a,b){return a + b}, 0);
        return total > 0;
    }

    refreshChart(data) {
        var update_options = this.getChartOptions(data);
        //completely empty the series
        while (this.chart_object.series.length > 0) {
            this.chart_object.series[0].remove(false);
        }

        this.chart_object.get('label-axis').remove(false);
        this.chart_object.addAxis(update_options['yAxis'], false);

        //now set new series
        var series_length = update_options['series'].length;
        for (var ii = 0; ii < series_length; ii++) {
            var series_option = update_options['series'][ii];
            this.chart_object.addSeries(series_option, false);
        }
        
        this.chart_object.redraw();
    }

    addOnChangeEvents() {
        var self = this;
        this.$form_element.find("select").on('change', function() {
            $.ajax({
                cache: false,
                type: "GET",
                url: '/campaign-management/reports',
                dataType: 'json',
                data: self.$form_element.serialize(),
                beforeSend: function(data) {
                    var toastr_options = {
                        "preventDuplicates": true,
                        "tapToDismiss": false,
                        "hideDuration": "1",
                        "timeOut": "300000000", //give a really long timeout, we should be done before that
                    }
                    var msg = "Getting new charts, please hold."
                    toastr.info(msg, null, toastr_options)
                },
                success: function(data) {
                    toastr.clear();
                    toastr.success("Filtered result retrieved");
                    self.refreshChart(data.data);
                },
                error: function (xhr) {
                    toastr.clear();
                    toastr.error('An unknown error has occurred, please try again');
                }
            })
        });
    }
}


class DashboardTiles {

    initTiles(data) {
        for (var media_type in data) {
            var tile_data = data[media_type];
            var container_id = "pie-chart-" + media_type;
            this.renderSingleTile(container_id, media_type, tile_data);
        }
    }

    renderSingleTile(container, media_type, tile_data) {
        
        var total = Object.values(tile_data).reduce(function(a,b){return a + b}, 0);
        var num_active = tile_data.active || 0;
        var num_pending = tile_data.pending || 0;
        var num_completed = tile_data.finished || 0;

        var percent_active = this.getWholePercent(num_active, total);
        var percent_pending = this.getWholePercent(num_pending, total);
        var percent_finished = this.getWholePercent(num_completed, total);

        Highcharts.chart(container, {
            chart: {type: 'pie', height: 150, width: 150},
            title: {text: ''},
            credits: {enabled: false},
            plotOptions: {pie: {allowPointSelect: false, dataLabels: {enabled: false, format: '{point.name}'}}},
            exporting: {enabled: false},
            series: [
                {
                    innerSize: '30%',
                    data: [
                        {name: 'Active', y: percent_active, color: '#00C4CA'},
                        {name: 'Pending', y: percent_pending, color: '#E89B0B' },
                        {name: 'Finished', y: percent_finished, color: '#E8235F'}
                    ]
                }
            ]
        });

        var $legend_span = $("#legend-" + media_type);
        $legend_span.find(".active span").text(percent_active + "%");
        $legend_span.find(".pending span").text(percent_pending + "%");
        $legend_span.find(".finished span").text(percent_finished + "%");
    }

    getWholePercent(numerator, denominator) {
        if (denominator == 0) {
            return 0;
        }
        return Math.floor((numerator/denominator) * 100);
    }
}
