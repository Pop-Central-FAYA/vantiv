<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header py-3">
                        <div class="row">
                            <div class="col-4">
                                <button @click="prev">prev</button>
                            </div>
                            <div class="col-4">
                                <h4 class="week-text bold">WEEKLY SCHEDULES</h4>
                            </div>
                            <div class="col-4">
                                <button @click="next" class="right">next</button>
                            </div>
                        </div>
                    </div>
                    <ad-schedule-table
                        :hours_breakdown="time_belts"
                        :current_week="weekView"
                        :events="events"
                    ></ad-schedule-table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment' 
    export default {
        props :{
            time_belts : {
                required : true,
                type : Array
            },
            weekly_schedule : {
                required : true,
                type : Array
            }
        }, 
        data () {
            return {
                weekView : [],
                startOfWeek : {},
                endOfWeek : {},
                events : this.weekly_schedule
            } 
        },
        methods : {
            prev : function () {
                this.startOfWeek = this.getLastWeekStart()
                this.endOfWeek = this.getLastWeekEnd()
                this.weekView = this.current(this.startOfWeek, this.endOfWeek)
                this.fetchEvent(this.startOfWeek, this.endOfWeek)
            },
            next : function () {
                this.startOfWeek = this.getNextWeekStart()
                this.endOfWeek = this.getNextWeekEnd()
                this.weekView = this.current(this.startOfWeek, this.endOfWeek)
                this.fetchEvent(this.startOfWeek, this.endOfWeek)
            },
            getNextWeekStart : function () {
                return this.startOfWeek.add(7, 'days')
            },
            getNextWeekEnd : function () {
                return this.endOfWeek.add(7, 'days')
            },
            getLastWeekStart : function() {
                return this.startOfWeek.subtract(7, 'days')
            },
            getLastWeekEnd : function() {
                return this.endOfWeek.subtract(7, 'days')
            },
            current: function (start_of_week, end_of_week) {
                var weeks = [];
                while (start_of_week <= end_of_week) {
                    weeks.push({
                        'day_number' : start_of_week.format('Do'),
                        'full_date' : start_of_week,
                        'current_date' : moment().format('YYYY-MM-DD'),
                        'date' : start_of_week.format('YYYY-MM-DD'),
                        'name' : start_of_week.format('dddd')
                    })
                    start_of_week = start_of_week.clone().add(1, 'd');
                }
                return weeks
            },
            fetchEvent : function(start_date, end_date) {
                var msg = "Processing request, please wait...";
                this.sweet_alert(msg, 'info');
                axios({
                    method: 'post',
                    url: '/schedule/weekly/navigate',
                    data : {
                        start_date : start_date,
                        end_date : end_date
                    }
                }).then((res) => {
                    let result = res.data.data;
                    if (result.length === 0) {
                        this.sweet_alert('No schedule available for this week', 'info');
                        this.events = [];
                    } else {
                        this.sweet_alert('Scheduled displayed', 'success');
                        this.events = result;
                    }
                }).catch((error) => {
                    this.events = [];
                    this.sweet_alert('An unknown error has occurred, schedule cannot be retrieved. Please try again', 'error');
                });
            },
        },
        mounted() {
            this.startOfWeek = moment().startOf('isoWeek');
            this.endOfWeek = moment().endOf('isoWeek');
            this.weekView = this.current(this.startOfWeek, this.endOfWeek)
        },
    }
</script>
<style>
    .table-wrapper {
    max-height: 500px;
    overflow: auto;
    display:inline-block;
    }
    .table-earnings {
        background: #F3F5F6;
    }
    .card_table {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2); /* this adds the "card" effect */
        text-align: center;
        background-color: #f1f1f1;
    }
    .program {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2); /* this adds the "card" effect */
        text-align: center;
    }
    .bold {
        font-weight: bold;
    }
    .week-text {
        text-align : center;
    }
    .right {
        text-align: right;
    }
    .split_column {
        border: solid 1px lightblue;
    }
    .active {
        color : blue
    }
    .program_name {
        color : white
    }
</style>