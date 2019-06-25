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
                        :hours="hours"
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
        data () {
            return {
                hours : ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00',
                '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00',
                '19:00', '20:00', '21:00', '22:00', '23:00'],
                weekView : [],
                startOfWeek : {},
                endOfWeek : {},
                events : [
                    {
                        hour : '00:00',
                        playout_date : '2019-06-24',
                        ad_pattern : '180 seconds',
                        playout_hour : [
                            {
                                program_name : 'Guiness',
                                program_id : '12345',
                                background_color : 'red',
                                program_ad_break : [
                                    {
                                        ad_break : '00:00',
                                        ads : [
                                            {
                                                id : 'hfxjdx',
                                                campaign_name : 'Pepsi Campaign',
                                                client_name : 'Coca cola group',
                                                duration : 30,
                                                order : 1,
                                            },
                                            {
                                                id : 'gjdhxakjsd',
                                                campaign_name : 'Pepsi Campaign',
                                                client_name : 'Coca cola group',
                                                duration : 30,
                                                order : 1,
                                            }
                                        ]
                                    }
                                ]
                            },
                            {
                                program_name : 'Papa Ajasco',
                                program_id : '123',
                                background_color : 'green',
                                program_ad_break : [
                                    {
                                        ad_break : '00:15',
                                        ads : [
                                            {
                                                id : 'gmvdsjfmhvx',
                                                campaign_name : 'Pepsi Campaign',
                                                client_name : 'Coca cola group',
                                                duration : 30,
                                                order : 1,
                                            },
                                            {
                                                id : 'bhvchabsd',
                                                campaign_name : 'Pepsi Campaign',
                                                client_name : 'Coca cola group',
                                                duration : 45,
                                                order : 1,
                                            }
                                        ]
                                        
                                    }
                                ]
                            },
                            {
                                program_name : 'Make we dance',
                                program_id : '1234',
                                background_color : 'blue',
                                program_ad_break : [
                                    {
                                        ad_break : '00:30',
                                        ads : [
                                            {
                                                id : 'zsregdws',
                                                campaign_name : 'Pepsi Campaign',
                                                client_name : 'Coca cola group',
                                                duration : 30,
                                                order : 1,
                                            },
                                            {
                                                id : '7trgfdw5r',
                                                campaign_name : 'Pepsi Campaign',
                                                client_name : 'Coca cola group',
                                                duration : 30,
                                                order : 1,
                                            }
                                        ]
                                        
                                    },
                                    {
                                        ad_break : '00:45',
                                        ads : [
                                            {
                                                id : 'fjhcbexre',
                                                campaign_name : 'Pepsi Campaign',
                                                client_name : 'Coca cola group',
                                                duration : 30,
                                                order : 1,
                                            },
                                            {
                                                id : '894rb345r',
                                                campaign_name : 'Pepsi Campaign',
                                                client_name : 'Coca cola group',
                                                duration : 30,
                                                order : 1,
                                            }
                                        ]
                                        
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        hour : '00:00',
                        playout_date : '2019-06-25',
                        ad_pattern : '180 seconds',
                        playout_hour : [
                            {
                                program_name : 'Big Brother Naija',
                                program_id : '1234545',
                                background_color : 'brown',
                                program_ad_break : [
                                    {
                                        ad_break : '00:00',
                                        ads : [
                                            {
                                                id : '7634tegwr',
                                                campaign_name : 'Always Cool',
                                                client_name : 'Always women care',
                                                duration : 30,
                                                order : 1,
                                            },
                                            {
                                                id : 'dhgrf4es4',
                                                campaign_name : 'Mama do good',
                                                client_name : 'Indomie',
                                                duration : 30,
                                                order : 1,
                                            }
                                        ]
                                    },
                                    {
                                        ad_break : '00:15',
                                        ads : [
                                            {
                                                id : '8747hedfd',
                                                campaign_name : 'Make it Happen',
                                                client_name : 'Samsung Ltd',
                                                duration : 30,
                                                order : 1,
                                            },
                                            {
                                                id : 'jhtr84234',
                                                campaign_name : 'Mama do good',
                                                client_name : 'Indomie',
                                                duration : 30,
                                                order : 1,
                                            }
                                        ]
                                    }
                                ]
                            },
                            {
                                program_name : 'Promo Content',
                                program_id : '984598',
                                background_color : 'orange',
                                program_ad_break : [
                                    {
                                        ad_break : '00:30',
                                        ads : [
                                            {
                                                id : 'gmvdsjfmhvx',
                                                campaign_name : 'Filler Content',
                                                client_name : 'Soundcity',
                                                duration : 30,
                                                order : 1,
                                            },
                                            {
                                                id : 'jhrzh4e',
                                                campaign_name : 'Filler Content',
                                                client_name : 'Soundcity',
                                                duration : 45,
                                                order : 1,
                                            }
                                        ]
                                        
                                    }
                                ]
                            }
                        ]
                    }
                ]
            } 
        },
        methods : {
            prev : function () {
                this.startOfWeek = this.getLastWeekStart()
                this.endOfWeek = this.getLastWeekEnd()
                this.weekView = this.current(this.startOfWeek, this.endOfWeek)
            },
            next : function () {
                this.startOfWeek = this.getNextWeekStart()
                this.endOfWeek = this.getNextWeekEnd()
                this.weekView = this.current(this.startOfWeek, this.endOfWeek)
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
            }
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