$(document).ready(function () {
$('#sidebarCollapse').on('click', function () {
$('#sidebar').toggleClass('active');
});
});
/*--chart--*/

$(function(){
$("#bars li .bar").each(function(key, bar){
var percentage = $(this).data('percentage');

$(this).animate({
'height':percentage+'%'
}, 1000);
})
})
$(document).ready(function(){
var data= [   { name:"first",
data:[
  {value:12, date:"12/14/2014"},
  {value:32, date:"12/15/2014"},
  {value:56, date:"12/17/2014"},
  {value:45, date:"12/19/2014"}

]
}
]





data= [   { name:"first",
data:[
{value:32, date:"12/13/2014"},
{value:20, date:"12/14/2014"},
{value:36, date:"12/15/2014"},
{value:18, date:"12/16/2014"},
{value:36, date:"12/17/2014"},
{value:18, date:"12/18/2014"},
{value:32, date:"12/19/2014"}

]
},
{ name:"second",
data:[
{value:16, date:"12/13/2014"},
{value:32, date:"12/14/2014"},
{value:18, date:"12/15/2014"},
{value:30, date:"12/16/2014"},
{value:16, date:"12/17/2014"},
{value:32, date:"12/18/2014"},
{value:16, date:"12/19/2014"}

]
}
] 
// Curve chart
options={
height: 220,
width:500,
lines:{curve:true}
}
$("#curve").pista(data, options);
});

<!--circularprogress--> 

<!--Bar chart-->
var data = [

{ y: '2015', a: 120,  b: 90},
{ y: '2016', a: 80,  b: 100},
{ y: '2017', a: 75,  b: 50},
{ y: '2018', a: 80,  b: 65},
{ y: '2019', a: 90,  b: 70},
{ y: '2020', a: 100, b: 75},
{ y: '2021', a: 115, b: 75},
{ y: '2022', a: 120, b: 85},
{ y: '2023', a: 145, b: 85},
{ y: '2024', a: 160, b: 95}
],
formatY = function (y) {
return '$'+y;
},
formatX = function (x) {
return x.src.y;
},

config = {
data: data,
xkey: 'y',
ykeys: ['a', 'b' ],
labels: ['Total Income', 'Total Outcome'],
fillOpacity: 0.6,
hideHover: 'auto',
stacked: true,
resize: true,
pointFillColors:['#ffffff'],
pointStrokeColors: ['black'],
barColors:['#540b3f','#e8e7e7'],
yLabelFormat:formatY,
xLabelFormat: formatX,
hoverCallback: function (index, options, content, row) {
return '';
}
};

config.element = 'bar-chart';


<!--donut_chart chart-->


function getMorris(type, element) {
if (type === 'donut') {
Morris.Donut({
element: element,
data: [
{
	label: 'Online',
	value:45,
labelcolor:"#000",
}, {
		label: 'Offline',
		value: 25,
	}, {
		label: 'Invisible',
		value: 35,
	}],
labelColor:"#000",   
colors: ['#e1235f', '#5d133e', '#f0f1f4'],
formatter: function (y) {
	return y
}
});
}
}

// REF
// http://stackoverflow.com/questions/40094194/chart-js-line-graph-multitooltipkey-background-color-issue




// Progress bar


/*--spline--*/

$(function(){
$("#bars li .bar").each(function(key, bar){
var percentage = $(this).data('percentage');

$(this).animate({
'height':percentage+'%'
}, 1000);
})
})
$(document).ready(function(){
var data= [   { name:"first",
data:[
  
]
}
]



data= [   { name:"first",
data:[
{value:0, date:"12/13/2014"},
{value:30, date:"12/14/2014"},
{value:5, date:"12/15/2014"},
{value:28, date:"12/16/2014"},
{value:10, date:"12/17/2014"},


]
}
] 
// Curve chart
options={
height: 220,
width:500,
lines:{curve:true}
}
$("#spline").pista(data, options);
});

