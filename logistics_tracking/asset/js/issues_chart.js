function issues_chart(bg_id, runway_id, range_id) {
    $(document).ready(function () {
        $.getJSON("get_issues_data.php", {
            runway_id: runway_id,
            bg_id: bg_id,
            range_id: range_id
        }, function (data) {

            // function compareDataPointYAscend(dataPoint1, dataPoint2) {
            //     return dataPoint1.y - dataPoint2.y;
            // }

            function compareDataPointYDescend(dataPoint1, dataPoint2) {
                return dataPoint2.y - dataPoint1.y;
            }

            var dataPoints = data;

            var chart1 = new CanvasJS.Chart("chartContainer1", {
                animationEnabled: true,
                backgroundColor: "#181C20",
                axisY: {
                    labelFontColor: "#ffffff",
                    gridColor: "#2e3134",
                    lineThickness: false,
                    tickThickness: false,
                    lineColor: "#2e3134",
                    labelFontSize: 18,
                },
                axisY2: {
                    maximum: 100,
                    minimum: 0,
                    suffix: "%",
                    labelFontColor: "#ffffff",
                    lineThickness: false,
                    tickThickness: false,
                    labelFontSize: 18,
                },
                axisX: {
                    tickThickness: false,
                    tickLength: 15,
                    labelFontColor: "#ffffff",
                    labelFontSize: 18,
                    lineColor: "#2e3134"
                },
                data: [{
                        type: "column",
                        axisYType: "primary",
                        dataPoints: dataPoints
                    },
                    // {
                    //     type: "line",
                    //     markerType: "none",
                    //     name: "KPI5",
                    //     legendText: "KPI5",
                    //     color: "#ED7D31",
                    //     showInLegend: false,
                    //     axisYType: "secondary",
                    //     dataPoints: [{
                    //             y: 1
                    //         },
                    //         {
                    //             y: 1
                    //         },
                    //         {
                    //             y: 1
                    //         },
                    //         {
                    //             y: 1
                    //         }
                    //     ]
                    // }
                ]
            });

            chart1.options.data[0].dataPoints.sort(compareDataPointYDescend);

            chart1.render();
            createPareto();	

            setTimeout(issues_chart, 30000);


            function createPareto() {
                var dps = [];
                var yValue, yTotal = 0,
                    yPercent = 0;

                for (var i = 0; i < chart1.data[0].dataPoints.length; i++)
                    yTotal += chart1.data[0].dataPoints[i].y;

                for (var i = 0; i < chart1.data[0].dataPoints.length; i++) {
                    yValue = chart1.data[0].dataPoints[i].y;
                    yPercent += (yValue / yTotal * 100);
                    dps.push({
                        label: chart1.data[0].dataPoints[i].label,
                        y: yPercent
                    });
                }

                chart1.addTo("data", {
                    type: "line",
                    yValueFormatString: "0.##\"%\"",
                    dataPoints: dps
                });
                chart1.data[1].set("axisYType", "secondary", false);
                chart1.axisY[0].set("maximum", yTotal);
                chart1.axisY2[0].set("maximum", 100);
            }
        })

    });
}