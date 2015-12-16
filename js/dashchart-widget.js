jQuery( document ).ready(function() {
csvimp_piechart();
csvimp_linechart();
});
function csvimp_piechart()
{
jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
                    'action'   : 'firstcsvImpchart',
                    'postdata' : 'firstchartdata',
                },
          dataType: 'json',
          cache: false,
          success: function(data) {
                var val = JSON.parse(data);
                if (val['label'] == 'No Imports Yet') {
                document.getElementById('csvimp_pieStats').innerHTML = "<h2 style='color: red;text-align: center;padding-top: 100px;' >No Imports Yet</h2>";
                return false;
                }
                Morris.Donut({
                        element: 'csvimp_pieStats',
                        data: val//[
                                //{label: val[0][0], value: value[0][1]}
                                //{label: "page", value: 30},
                                //{label: "custompost", value: 20}
                        //]
                });
        }
});
}
function csvimp_linechart() {
jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
                    'action'   : 'secondcsvImpchart',
                    'postdata' : 'secondchartdata',
                },
          dataType: 'json',
          cache: false,
          success: function(result) {
                console.log(result);
                var val = JSON.parse(result);
                var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                 Morris.Line({
                        element: 'csvimp_lineStats',
                        data   : val,
                        xkey: 'year',
                        ykeys: ['post', 'page','custompost'],
                        labels: ['post', 'page','custompost'],
                        lineColors:['red','blue','black'],
                        xLabelFormat: function(x) { // <--- x.getMonth() returns valid index
                                var month = months[x.getMonth()];
                                return month;
                        },
                        dateFormat: function(x) {
                                var month = months[new Date(x).getMonth()];
                                return month;
                        },

                });
        }
});
}  
