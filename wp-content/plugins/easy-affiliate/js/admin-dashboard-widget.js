var EsafDashboardWidget = (function ($) {

  var widget = {

    initialize: function () {
      widget.setup_chart();
    },

    setup_chart: function () {
      var stats = $('#esaf-admin-dashboard-widget-chart').data('stats');

      if(!window.Chart || typeof Chart !== 'function' || !stats || !stats.length) {
        return;
      }

      var cfg = {
        data: {
          datasets: widget.get_chart_datasets(stats)
        },
        options: {
          maintainAspectRatio: false,
          legend: {
            display: false
          },
          animation: {
            duration: 0
          },
          scales: {
            xAxes: [{
              type: 'time',
              distribution: 'series',
              bounds: 'ticks',
              offset: true,
              time: {
                unit: 'day',
                tooltipFormat: 'll'
              },
              ticks: {
                major: {
                  enabled: true,
                  fontStyle: 'bold'
                },
                source: 'data',
                autoSkip: true,
                autoSkipPadding: 50
              },
            }],
            yAxes: [{
              ticks: {
                beginAtZero: true,
                autoSkip: true,
                autoSkipPadding: 50,
                userCallback: function (label) {
                  // when the floored value is the same as the value we have a whole number
                  if(Math.floor(label) === label) {
                    return label;
                  }
                }
              },
              gridLines: {
                drawBorder: false
              },
              scaleLabel: {
                display: false
              }
            }]
          },
          tooltips: {
            intersect: false,
            mode: 'index'
          }
        }
      };

      new Chart($('#esaf-admin-dashboard-widget-chart-canvas')[0], cfg);
    },

    get_chart_datasets: function(stats) {
      var clicks = [],
        uniques = [],
        transactions = [],
        datasets = [],
        day;

      for(var i = 0; i < stats.length; i++) {
        day = stats[i];

        clicks.push({ x: day.date, y: day.clicks });
        uniques.push({ x: day.date, y: day.uniques });
        transactions.push({ x: day.date, y: day.transactions });
      }

      datasets.push({
        backgroundColor: '#6fabce',
        borderColor: '#6fabce',
        data: clicks,
        type: 'line',
        pointRadius: 4,
        fill: false,
        lineTension: 0.01,
        borderWidth: 4,
        label: EsafAdminDashboardWidgetL10n.clicks
      });

      datasets.push({
        backgroundColor: '#edc240',
        borderColor: '#edc240',
        data: uniques,
        type: 'line',
        pointRadius: 4,
        fill: false,
        lineTension: 0.01,
        borderWidth: 4,
        label: EsafAdminDashboardWidgetL10n.uniques
      });

      datasets.push({
        backgroundColor: '#cb4b4b',
        borderColor: '#cb4b4b',
        data: transactions,
        type: 'line',
        pointRadius: 4,
        fill: false,
        lineTension: 0.01,
        borderWidth: 4,
        label: EsafAdminDashboardWidgetL10n.sales
      });

      return datasets;
    }

  };

  $(widget.initialize)

  return widget;

})(jQuery);
