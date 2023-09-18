let chart;
const website = 'https://simplyscheduleappointments.com/pricing/';

async function generateChart(duration) {
  let date;
  if (duration === 7) {
    date = 'http://127.0.0.1:8000/api/web-response7/date';
  } else if (duration === 25) {
    date = 'http://127.0.0.1:8000/api/web-response25/date';
  }

  let [dateResponse, response, mobileLabResponse] = await Promise.all([
    axios.post(date, { website: website }),
    getEndpointData(duration, 'desktop_lab_LARGEST_CONTENTFUL_PAINT'),
    getEndpointData(duration, 'mobile_lab_LARGEST_CONTENTFUL_PAINT'),
  ]);

  let dateData = dateResponse.data;
  let data = response.data.map(value => value / 1000); 
  let mobileLabData = mobileLabResponse.data.map(value => value / 1000);

  let columns = [
    ['x'].concat(dateData),
    ['DesktopLabData'].concat(data),
    ['MobileLabData'].concat(mobileLabData)
  ];

  if (chart) {
    chart.destroy();
  }

  chart = c3.generate({
    bindto: '#chart',
    data: {
      x: 'x',
      columns: columns,
      type: 'line',
      axes: {
        DesktopLabData: 'y',
        MobileLabData: 'y',
      },
      names: {
        DesktopLabData: 'Desktop Lab Data',
        MobileLabData: 'Mobile Lab Data',
      },
      colors: {
        DesktopLabData: 'orange',
        MobileLabData: 'blue',
      },
    },
    axis: {
      x: {
        type: 'timeseries',
        tick: {
          format: '%Y-%m-%d',
          rotate: -45,
          multiline: false,
          values: dateData
        }
      },
      y: {
        label: {
          text: 'Results (s)',
          position: 'outer-middle'
        }
      },
    },
    grid: {
      y: {
        lines: [
          { value: 2.5, text: 'Good', class: 'good-line', position: 'start', class: 'good-line' },
          { value: 4.0, text: 'Bad', class: 'bad-line', position: 'start', class: 'bad-line' }
        ]
      },
      focus: {
        edge: {
          show: false
        }
      }
    },
    legend: {
      position: 'top',
      hide: ['good', 'bad'],
      item: {
        tile: {
          width: 15,
          height: 2
        }
      }
    },
    transition: {
      duration: 0
    },
  });
}

async function getEndpointData(duration, endpointSuffix) {
  let endpoint;
  if (duration === 7) {
    endpoint = `http://127.0.0.1:8000/api/web-response7/${endpointSuffix}`;
  } else if (duration === 25) {
    endpoint = `http://127.0.0.1:8000/api/web-response25/${endpointSuffix}`;
  }

  try {
    return await axios.post(endpoint, { website: website });
  } catch (error) {
    console.error(`Error while fetching ${endpoint} data:`, error);
    return { data: [] };
  }
}

document.getElementById('durationSelect').addEventListener('change', function () {
  let duration = parseInt(this.value);
  generateChart(duration);
});

generateChart(7);

const areAllValuesZero = (arr) => {
  return arr.every(value => value === 0);
}
