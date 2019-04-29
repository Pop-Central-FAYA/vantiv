
const numberFormatter = new Intl.NumberFormat('en-US', {});

class DashboardGraph {

    constructor($chart_container) {
        this.$chart_container = $chart_container;
        this.chart_element = this.$chart_container.find("#periodicChart").attr('id');
        this.$form_element = $chart_container.find("#filter-form");
        this.$all_toggles = this.$chart_container.find('.chart-toggle button');

        this.initToggles();
        this.initDropdowns();
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
            self.performFilterRequest();
        });
    }

    performFilterRequest() {
        var self = this;

        $.ajax({
            cache: false,
            type: "GET",
            url: '/campaign-management/reports',
            dataType: 'json',
            data: self.$form_element.find(":not(.do-not-send)").serialize(),
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
    }

    initToggles() {
        var self = this;
        this.$all_toggles.on('click', function() {
            var $el = $(this);
            if ($el.hasClass('active-toggle')) {
                return;
            }

            //toggle the active/inactive classes (shows which button is active)
            self.$all_toggles.removeClass('active-toggle').addClass('inactive-toggle');
            $el.removeClass('inactive-toggle').addClass('active-toggle');

            //set the media type in the form (for requests)
            var media_type = $el.attr('data-media-type');
            self.$form_element.find("#media-type-input").val(media_type);

            //hide the filter bar belonging to one media type and show the selected one

            self.$form_element.find(".filter-containers").hide();
            self.$form_element.find("#" + media_type + "-filter-container").show()

            self.$form_element.find(".filter-val").addClass("do-not-send");
            self.$form_element.find("#" + media_type + "-filter-container .filter-val").removeClass("do-not-send")

            //make a request with the default values
            self.performFilterRequest();
        });
    }

    initDropdowns(media_type) {
        this.$chart_container.find('.single-select').SumoSelect({placeholder: 'Select One', csvDispCount: 3});

        this.$chart_container.find('.publishers').SumoSelect({
            placeholder: 'Select Stations',
            csvDispCount: 3,
            selectAll: true,
            captionFormat: '{0} Stations Selected',
            captionFormatAllSelected: 'All Stations selected!',
            okCancelInMulti: true, 
        });
    }
}

class DashboardTiles {

    initTiles(data) {
        for (var media_type in data.detailed_counts) {
            var tile_data = data.detailed_counts[media_type];
            var total = data.total[media_type]
            var container_id = "pie-chart-" + media_type;
            this.renderSingleTile(container_id, media_type, tile_data, total);
        }
    }

    renderSingleTile(container, media_type, tile_data, total) {
        
        // var total = Object.values(tile_data).reduce(function(a,b){return a + b}, 0);
        var num_active = tile_data.active || 0;
        var num_pending = tile_data.pending || 0;
        var num_completed = tile_data.finished || 0;

        var percent_active = this.getWholePercent(num_active, total);
        var percent_pending = this.getWholePercent(num_pending, total);
        var percent_finished = this.getWholePercent(num_completed, total);

        Highcharts.chart(container, {
            // chart: {type: 'pie', height: 200, width: 200},
            chart: {
                type: 'pie', 
                size:'100%', 
                dataLabels: {enabled: false},
                margin: [0, 0, 0, 0],
                spacingTop: 0,
                spacingBottom: 0,
                spacingLeft: 0,
                spacingRight: 0
            },
            title: {text: ''},
            credits: {enabled: false},
            plotOptions: {pie: {allowPointSelect: false, dataLabels: {enabled: false, format: '{point.name}'}}},
            exporting: {enabled: false},
            series: [
                {
                    innerSize: '25%',
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

class TimebeltRevenueChart {

    constructor($chart_container) {
        this.$chart_container = $chart_container;
        this.chart_element = this.$chart_container.find("#periodicChart").attr('id');
        this.$form_element = $chart_container.find("#filter-form");
        this.$all_toggles = this.$chart_container.find('.chart-toggle button');

        this.initToggles();
        this.initDropdowns();
        this.addOnChangeEvents();
    }

    initChart(data) {
        var options = {
            chart: {type: 'spline'},
            title: {text: ''},
            credits: {enabled: false},
            exporting: {enabled: false},
            tooltip: {crosshairs: true,shared: true},
            xAxis: {title: {text: 'Time Belt'}, categories: data.time_belts, crosshair: true},
            lang: {noData: "No data to display for the filter options"},
            noData: {style: {fontWeight: 'bold', fontSize: '15px', color: '#303030'}},
            plotOptions: {spline: {marker: {radius: 4, lineColor: '#666666', lineWidth: 1}}},
        };
        var custom_options = this.getChartOptions(data);
        options['yAxis'] = custom_options['yAxis'];
        options['series'] = custom_options['series'];
        this.chart_object = Highcharts.chart(this.chart_element, options);
        return this.chart_object;
    }

    getChartOptions(data) {
        var options = {
            yAxis: {
                id: 'label-axis',
                title: {text: 'Revenue'},
                labels: {
                    formatter: function() {
                        return (String.fromCharCode(0x20A6) + numberFormatter.format(this.value)).replace(".00", "");
                    },
                }
            },
            
        };
       
        if (this.seriesHasData(data.revenue)) {
            options['series'] = [{name: 'Revenue', marker: {symbol: 'square'}, data: data.revenue}];
        } else {
            options['series'] = []
        }
        return options;
    }

    seriesHasData(series_data) {
        if (series_data !== null && series_data.length > 0) {
            var total = series_data.reduce(function(a,b){return a + b}, 0);
            return total > 0;
        }
        return false;
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
            self.performFilterRequest();
        });
    }

    performFilterRequest() {
        var self = this;
        $.ajax({
            cache: false,
            type: "GET",
            url: '/inventory-management/reports',
            dataType: 'json',
            data: self.$form_element.find(":not(.do-not-send)").serialize(),
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
    }

    initToggles() {
        var self = this;
        this.$all_toggles.on('click', function() {
            var $el = $(this);
            if ($el.hasClass('active-toggle')) {
                return;
            }

            //toggle the active/inactive classes (shows which button is active)
            self.$all_toggles.removeClass('active-toggle').addClass('inactive-toggle');
            $el.removeClass('inactive-toggle').addClass('active-toggle');

            //set the media type in the form (for requests)
            var media_type = $el.attr('data-media-type');
            self.$form_element.find("#media-type-input").val(media_type);

            //hide the filter bar belonging to one media type and show the selected one

            self.$form_element.find(".filter-containers").hide();
            self.$form_element.find("#" + media_type + "-filter-container").show()

            self.$form_element.find(".filter-val").addClass("do-not-send");
            self.$form_element.find("#" + media_type + "-filter-container .filter-val").removeClass("do-not-send")

            //make a request with the default values
            self.performFilterRequest();
        });
    }

    initDropdowns(media_type) {
        // this.$chart_container.find('.single-select').SumoSelect({placeholder: 'Select One', csvDispCount: 3});
        this.$chart_container.find('.publishers-filter').SumoSelect({
            captionFormat: '{0} Stations Selected',
            captionFormatAllSelected: 'All Stations selected!',
            csvDispCount: 3,
            selectAll: true,
            okCancelInMulti: true, 
        });
        this.$chart_container.find('.day-parts-filter').SumoSelect({
            captionFormat: '{0} Day Parts Selected',
            captionFormatAllSelected: 'All Day Parts selected!',
            csvDispCount: 3,
            selectAll: true,
            okCancelInMulti: true, 
        });
        this.$chart_container.find('.days-filter').SumoSelect({
            captionFormat: '{0} Days Selected',
            captionFormatAllSelected: 'All Days selected!',
            csvDispCount: 3,
            selectAll: true,
            okCancelInMulti: true, 
        });
    }
}