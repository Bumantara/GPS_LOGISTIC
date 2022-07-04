function data_chart(bg_id, runway_id) {
    $(document).ready(function() {
      $.getJSON("get_departure_accuracy_data.php", {
        bg_id: bg_id,
        runway_id: runway_id
      }, function(data) {
        var dataKPI5 = data.pop();
        var dataKPI4 = data.pop();
        var chart = new CanvasJS.Chart("chartContainer", {
          dataPointWidth: 20,
          animationEnabled: true,
          backgroundColor: "#181C20",
          axisX: {
            tickThickness: false,
            lineColor: "#ffffff",
            lineThickness: false,
            tickLength: 15,
            labelFontColor: "#ffffff",
            labelFontSize: 18,
            interval: 1
          },
          axisY: {
            gridColor: "#2e3134",
            titleFontColor: "#ffffff",
            labelFontColor: "#ffffff",
            lineThickness: false,
            tickThickness: false,
            interval: 20,
            minimum: 0,
            maximum: 100,
            labelFontSize: 18,
            suffix: "%"
          },
          legend: {
            cursor: "pointer",
            itemclick: toggleDataSeries,
            fontColor: "white",
          },
          data: [{
              type: "column",
              name: "KPI4",
              legendText: "KPI4",
              color: "#4472C4",
              showInLegend: true,
              dataPoints: dataKPI4
            },
            {
              type: "column",
              name: "KPI5",
              legendText: "KPI5",
              color: "#B07AD8",
              showInLegend: true,
              dataPoints: dataKPI5
            }
          ]
        });

        chart.render();
        setTimeout(data_chart, 3000);

        function toggleDataSeries(e) {
          if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
            e.dataSeries.visible = false;
          } else {
            e.dataSeries.visible = true;
          }
          chart.render();
        }
      })

    });
  }