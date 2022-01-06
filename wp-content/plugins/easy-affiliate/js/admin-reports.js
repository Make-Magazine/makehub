var EsafAdminReports = (function ($) {

  var reports = {

    chart: null,
    working: false,

    initialize: function () {
      reports.setup_chart();
      reports.setup_daterangepicker();
    },

    setup_chart: function () {
      var stats = $('#esaf-reports-affiliate-stats').data('stats');

      if(!window.Chart || typeof Chart !== 'function' || !stats || !stats.length) {
        return;
      }

      var cfg = {
        data: {
          datasets: reports.get_chart_datasets(stats)
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
                padding: 20,
                major: {
                  enabled: true,
                  fontStyle: 'bold'
                },
                source: 'data',
                autoSkip: true,
                autoSkipPadding: 50
              },
              gridLines: {
                borderDash: [3, 1],
                tickMarkLength: 0,
                color: '#e4e4e4'
              }
            }],
            yAxes: [{
              ticks: {
                padding: 10,
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
                borderDash: [3, 1],
                tickMarkLength: 0,
                color: '#e4e4e4'
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

      reports.chart = new Chart($('#esaf-reports-affiliate-stats-chart-canvas')[0], cfg);
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

      var point_radius = clicks.length > 60 ? 1 : 4,
        border_width = clicks.length > 60 ? 1 : 4;

      datasets.push({
        backgroundColor: '#6fabce',
        borderColor: '#6fabce',
        data: clicks,
        type: 'line',
        pointRadius: point_radius,
        fill: false,
        lineTension: 0.01,
        borderWidth: border_width,
        label: EsafAdminReportsL10n.clicks
      });

      datasets.push({
        backgroundColor: '#edc240',
        borderColor: '#edc240',
        data: uniques,
        type: 'line',
        pointRadius: point_radius,
        fill: false,
        lineTension: 0.01,
        borderWidth: border_width,
        label: EsafAdminReportsL10n.uniques
      });

      datasets.push({
        backgroundColor: '#cb4b4b',
        borderColor: '#cb4b4b',
        data: transactions,
        type: 'line',
        pointRadius: point_radius,
        fill: false,
        lineTension: 0.01,
        borderWidth: border_width,
        label: EsafAdminReportsL10n.sales
      });

      return datasets;
    },

    setup_daterangepicker: function () {
      var $stats = $('#esaf-reports-affiliate-stats'),
        start_date = $stats.data('start'),
        end_date = $stats.data('end');

      if(!window.moment || !$.fn.daterangepicker || !start_date || !end_date) {
        return;
      }

      start_date = moment(start_date);
      end_date = moment(end_date);

      $('#esaf-reports-date-range-current').text(start_date.format('MMMM D, YYYY') + ' - ' + end_date.format('MMMM D, YYYY'));

      $('#esaf-reports-date-range').daterangepicker({
        ranges: {
          [EsafAdminReportsL10n.last_30_days]: [start_date, end_date],
          [EsafAdminReportsL10n.last_month]: [moment(end_date).subtract(1, 'month').startOf('month'), moment(end_date).subtract(1, 'month').endOf('month')],
          [EsafAdminReportsL10n.two_months_ago]: [moment(end_date).subtract(2, 'month').startOf('month'), moment(end_date).subtract(2, 'month').endOf('month')],
          [EsafAdminReportsL10n.three_months_ago]: [moment(end_date).subtract(3, 'month').startOf('month'), moment(end_date).subtract(3, 'month').endOf('month')],
          [EsafAdminReportsL10n.last_6_months]: [moment(end_date).subtract(6, 'month'), end_date],
          [EsafAdminReportsL10n.this_year]: [moment(end_date).startOf('year'), end_date],
          [EsafAdminReportsL10n.last_year]: [moment(end_date).subtract(1, 'year').startOf('year'), moment(end_date).subtract(1, 'year').endOf('year')]
        },
        startDate: start_date,
        endDate: end_date,
        maxDate: moment(),
        alwaysShowCalendars: true,
        autoApply: true,
        autoUpdateInput: true,
        showCustomRangeLabel: false,
        opens: 'left'
      }).on('apply.daterangepicker', function (ev, picker) {
        reports.update_report_data(picker.startDate, picker.endDate);
      });
    },

    update_report_data: function (start_date, end_date) {
      if(reports.working) {
        return;
      }

      reports.working = true;
      $('#esaf-reports-date-range').find('i').attr('class', 'ea-icon ea-icon-spinner animate-spin');
      $('#esaf-reports-affiliate-stats').find('.notice').remove();
      $('#esaf-reports-date-range-current').text(start_date.format('MMMM D, YYYY') + ' - ' + end_date.format('MMMM D, YYYY'));

      $.ajax({
        method: 'GET',
        url: EsafAdminReportsL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_get_report_data',
          _ajax_nonce: EsafAdminReportsL10n.get_report_data_nonce,
          start: start_date.format('YYYY-MM-DD'),
          end: end_date.format('YYYY-MM-DD')
        }
      }).done(function (response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            var datasets = reports.get_chart_datasets(response.data.stats);
            reports.chart.config.data.datasets[0] = datasets[0];
            reports.chart.config.data.datasets[1] = datasets[1];
            reports.chart.config.data.datasets[2] = datasets[2];
            reports.chart.update();
            $('#esaf-reports-affiliate-stats-table').html(response.data.table_html);
          }
          else {
            reports.update_report_data_error(response.data);
          }
        }
        else {
          reports.update_report_data_error();
        }
      }).fail(function () {
        reports.update_report_data_error();
      }).always(function () {
        reports.working = false;
        $('#esaf-reports-date-range').find('i').attr('class', 'ea-icon ea-icon-angle-down');
      });
    },

    update_report_data_error: function (message) {
      $('<div class="notice notice-error">').append(
        $('<p>').text(message || EsafAdminReportsL10n.error_updating_report_data)
      ).insertBefore('#esaf-reports-affiliate-stats-chart');
    }

  };

  $(reports.initialize);

  return reports;

})(jQuery);
