!function($){$.fn.vcLineChart=function(){var waypoint="undefined"!=typeof $.fn.waypoint;return this.each(function(){function addchart(){$this.data("animated")||(chart="bar"===$this.data("vcType")?new Chart(ctx).Bar(data,options):new Chart(ctx).Line(data,options),$this.data("vcChartId",chart.id),$this.data("chart",chart),$this.data("animated",!0))}var data,gradient,chart,i,j,$this=$(this),ctx=$this.find("canvas")[0].getContext("2d"),options={showTooltips:$this.data("vcTooltips"),animationEasing:$this.data("vcAnimation"),datasetFill:!0,scaleLabel:function(object){return" "+object.value},responsive:!0},color_keys=["fillColor","strokeColor","highlightFill","highlightFill","pointHighlightFill","pointHighlightStroke"];for($this.data("chart")&&($this.data("chart").destroy(),$this.removeData("animated")),data=$this.data("vcValues"),ctx.canvas.width=$this.width(),ctx.canvas.height=$this.width(),i=data.datasets.length-1;i>=0;i--)for(j=color_keys.length-1;j>=0;j--)"object"==typeof data.datasets[i][color_keys[j]]&&2===data.datasets[i][color_keys[j]].length&&(gradient=ctx.createLinearGradient(0,0,0,ctx.canvas.height),gradient.addColorStop(0,data.datasets[i][color_keys[j]][0]),gradient.addColorStop(1,data.datasets[i][color_keys[j]][1]),data.datasets[i][color_keys[j]]=gradient);waypoint?$this.waypoint($.proxy(addchart,$this),{offset:"85%"}):addchart()}),this},"function"!=typeof window.vc_line_charts&&(window.vc_line_charts=function(model_id){var selector=".vc_line-chart";"undefined"!=typeof model_id&&(selector='[data-model-id="'+model_id+'"] '+selector),$(selector).vcLineChart()}),$(document).ready(function(){!window.vc_iframe&&vc_line_charts()})}(jQuery);