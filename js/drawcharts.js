/**
 * summary_data variable is an object coming from PHP 
 * via wp_localize_scripts
 */
google.charts.load("current", {packages:["corechart"]});


var drawcharts = {

    // draws summary chart
    drawUsersByRoleChart : function() {

        function drawChart(){
            
            // build data array.
            var p_data = [
                ['User Roles', 'Total'],
            ];
            for ( let key in summary_data.user_data.avail_roles ) {
                let parsed_key =  summary_data.user_data.avail_roles[key] + ' ' + key.charAt(0).toUpperCase() + key.slice(1).toLowerCase();
                p_data.push( [ parsed_key, summary_data.user_data.avail_roles[key] ] );
            }
            
            var data = google.visualization.arrayToDataTable( p_data );

            var options = {
                title : 'Total Users by Role',
                pieHole : 0.4,
                // is3D : true,
            };

            var chart = new google.visualization.PieChart(document.getElementById('summary-roles-chart'));
            chart.draw( data, options );
        }
        google.charts.setOnLoadCallback( drawChart );
    },

    // draws userschart by role
    drawSummaryChart : function(){

        function drawChart() {
            // build data array
            var p_data = [
                ['', '', { role: 'style' } ],
                ['Total Users', summary_data.user_data.total_users, '#7A0CFF' ],
                ['Total Logins', parseInt( summary_data.total_logins ), '#4D616E' ],
            ];
            var data = google.visualization.arrayToDataTable( p_data );
            var options = {
                title : 'Users Data',
                legend : 'none',
            };
            var chart = new google.visualization.ColumnChart( document.getElementById('summary-chart' ) );
            chart.draw( data, options );
        }
        google.charts.setOnLoadCallback( drawChart );
    },

    // draw login filter by role chart
    drawLoginByRole : function() {

        function drawChart() {
            //build data array
            var p_data = [
                ['Role', 'Total']
            ];
            for ( let key in summary_data.logins_per_role ) {
                p_data.push( [ key.charAt(0).toUpperCase() + key.slice(1).toLowerCase(), parseInt( summary_data.logins_per_role[ key ] ) ] );
            }

            var data = new google.visualization.arrayToDataTable( p_data );
            
            var options = {
                legend : {
                    position : 'none',
                },
                chart : {
                    title : 'Total logins',
                    subtitle : 'filtered by role',
                },
                axes : {
                    x : {
                      0 : { 
                            side: 'top', 
                            label: 'Current Roles' 
                        }, // Top x-axis.
                    },
                },
            }; // end options var
            
            var chart = new google.charts.Bar( document.getElementById( 'summary-logins-chart' ) );
            chart.draw(data, google.charts.Bar.convertOptions( options ) );
        }
        google.charts.load('current', {'packages':['bar']});
        google.charts.setOnLoadCallback( drawChart );
    }
}

jQuery(document).ready( function($){
    drawcharts.drawSummaryChart();
    drawcharts.drawUsersByRoleChart();
    drawcharts.drawLoginByRole();
});