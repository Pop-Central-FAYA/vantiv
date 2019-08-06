<template>
    <main>
        <div class="toolbar">
            <div class="toggle">
                <div class="toggle__option toggle__option--selected" @click="prev">Previous</div>
                <div class="toggle__option toggle__option--selected" @click="current_week">Today</div>
            </div>
            
            <div class="toggle_center"> <h3>{{ weekMonthName() }}</h3> </div>
            <div class="toggle_right">
                <div class="toggle__option toggle__option--selected" @click="next">Next</div>
            </div>
            </div>
            <div class="calendar">
            <div class="calendar__header">
                <div class="hour"></div>
                <div v-for="(week, key) in weekView" :key="key" class="week_name"> 
                    <h2  :class="getDayStyle(week)">{{ week.day_number }}</h2> 
                    <h5 class="week_name" :class="getDayStyle(week)">{{ week.name }}</h5>
                </div>
            </div>
            <div class="scrollable">
                <div class="calendar__week" v-for="(time_belt, time_belt_key) in time_belts" :key="time_belt_key">
                    <div class="calendar__day day hour">{{ time_belt.start_time }}</div>
                    <div class="calendar__day day" 
                        v-for="(day_object, key) in weekView" :key="key"
                        :style="{ 'background-color' : getProgramForDay(day_object, time_belt).program_color}"
                        :class="getDynamicBorderTop(time_belt, day_object, time_belt_key)"
                        @click="openAdslotModal(getProgramForDay(day_object, time_belt))" 
                        >
                        <p style="text-align : center; color: white;"
                        v-if="!checkConcurrentProgram(time_belt, day_object, time_belt_key)"
                        > {{ getProgramForDay(day_object, time_belt).program_name }}</p>
                        <p
                        :style="{'text-align' : 'center', 'color': getProgramForDay(day_object, time_belt).program_color}" 
                        v-else>{{ getProgramForDay(day_object, time_belt).program_name }}</p>
                        <div class="total_duration_box" v-if="getProgramForDay(day_object, time_belt).program_name" style="background-color : white">
                            <div class="text-center my-3" >
                                <b-button 
                                v-b-popover.hover="getProgramForDay(day_object, time_belt).total_slot_used+' seconds used from '+ad_pattern+' seconds'" 
                                class="hover_over"
                                title="Slot Duration">
                                </b-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <ad-break-modal
            :program_time_belt="currentItem"
            ></ad-break-modal>
        </div>
    </main>
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
            type : Object
        },
        ad_pattern : {
            required : true,
            type : Number
        }
    }, 
    data () {
        return {
            editDialog: false,
            weekView : [],
            startOfWeek : {},
            endOfWeek : {},
            events : this.weekly_schedule,
            currentItem: {},
            current_moment : {
                date : moment().format('YYYY-MM-DD'),
                day_number : moment().format('D'),
                month_year : moment().format('MMM')+', '+moment().format('YYYY')
            },
            selected_mpos : []
        } 
    },
    methods : {
        getDayStyle : function(week) {
            if(week.name === 'Sun'){
                return 'sunday'
            }else if(week.day_number === this.current_moment.day_number){
                return 'current_day'
            }else{
                return 'regular_day'
            }
        },
        current_week : function() {
            this.startOfWeek = moment().startOf('isoWeek');
            this.endOfWeek = moment().endOf('isoWeek');
            this.weekView = this.current(this.startOfWeek, this.endOfWeek)
            this.fetchEvent(this.startOfWeek, this.endOfWeek)
        },
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
                    'day_number' : start_of_week.format('D'),
                    'full_date' : start_of_week,
                    'current_date' : moment().format('YYYY-MM-DD'),
                    'date' : start_of_week.format('YYYY-MM-DD'),
                    'name' : start_of_week.format('ddd'),
                    'full_day_name' : start_of_week.format('dddd').toLowerCase()
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
                    end_date : end_date,
                    selected_mpos : this.selected_mpos
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
        weekMonthName : function() {
            if(this.weekView.length != 0){
                var first_date = this.weekView[0].date
                var last_date = this.weekView[this.weekView.length - 1].date
                var first_date_month = moment(first_date).format('MMM')
                var last_date_month = moment(last_date).format('MMM')
                var year = moment(first_date).format('YYYY')
                if(first_date_month === last_date_month){
                    return first_date_month+', '+year
                }else{
                    return first_date_month+' - '+last_date_month+', '+year
                }
            }
        },
        getProgramForDay : function(day_object, time_belt) {
            var self = this;
            var current_day_schedule = this.events[day_object.full_day_name]
            var program_name =''
            var program_color = ''
            var program_id = ''
            var ads_schedule_for_day = []
            if(current_day_schedule){
                current_day_schedule.forEach(function(item) {
                    if(item.time_belt === time_belt.start_time){
                        program_name = item.program_name
                        program_color = item.background_color
                        program_id = item.program_id
                        ads_schedule_for_day.push(item)
                    }
                })
            }
            return {'program_name' : program_name, 
                    'program_color' : program_color,
                    'program_id' : program_id, 
                    'time_belt' : time_belt.start_time,
                    'date' : day_object.date,
                    'ads_schedules' : ads_schedule_for_day,
                    'total_slot_used' : this.sumDurationInAdBreak(ads_schedule_for_day),
                    'tool_tip_heigth' : this.getTootTipHeight(ads_schedule_for_day)+'px',
                    'toot_tip_margin_top' : -this.getToottipMarginTop(ads_schedule_for_day)+'px'}
        },
        openAdslotModal : function(item) {
            if(item.program_name !== ""){
                this.currentItem = Object.assign({}, item)
                Event.$emit('display-ads-modal', true)
            }
        },
        sumDurationInAdBreak : function(ad_break) {
            if(ad_break){
                return ad_break.reduce((prev,next) => prev + next.duration, 0);
            }
        },
        getTootTipHeight : function(ad_break) {
            let total_scheduled_slot = this.sumDurationInAdBreak(ad_break)
            return Math.floor((total_scheduled_slot * 30) / this.ad_pattern)
        },
        getToottipMarginTop : function(ad_break) {
            let total_scheduled_slot = this.sumDurationInAdBreak(ad_break)
            return Math.floor((total_scheduled_slot * 5) / this.ad_pattern)
        },
        checkConcurrentProgram: function (time_belt, day_object, time_belt_key) {
            if(time_belt_key != 0) {
                return this.getProgramForDay(day_object, time_belt).program_id 
                    === this.getProgramForDay(day_object, this.time_belts[time_belt_key - 1]).program_id;
            }
        },
        getDynamicBorderTop : function(time_belt, day_object, time_belt_key) {
            if(this.getProgramForDay(day_object, time_belt).program_id && this.checkConcurrentProgram(time_belt, day_object, time_belt_key)){
                return 'no_border_top'
            }else{
                return 'has_border_top'
            }
        }
    },
    created () {
        var self = this;
        Event.$on('mpos', function (mpo) {
            self.selected_mpos = mpo;
            self.fetchEvent(self.startOfWeek, self.endOfWeek)
        });  
    },
    mounted() {
        this.current_week()
    },
}
</script>
<style>
    .week_name {
        text-transform: capitalize;
    }
    .regular_day {
        color: silver;
    }
    .current_day {
        color: black;
    }
    .scrollable {
        overflow-y: auto;
        height: 500px;
    }
    .hover_over {
        width: -webkit-fill-available !important;
        margin-top: 17px;
        height: 12px;
    }
    .no_border_top {
        border-top: 0px !important;
    }
    .has_border_top {
        border-top: 1px solid #e1e1e1;
    }
    .total_duration_box {
        height: 29px;
        margin-top: -14px;
    }
</style>


