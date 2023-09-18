let chart;
const website = 'https://laravel.com';

async function generateChart(duration) {
  const dateEndpoint = `http://127.0.0.1:8000/api/response/date/${duration}`;
  const endpoints = [
    'desktop_lab_LARGEST_CONTENTFUL_PAINT',
    // 'mobile_lab_LARGEST_CONTENTFUL_PAINT',
    // 'mobile_LARGEST_CONTENTFUL_PAINT_MS',
    // 'desktop_LARGEST_CONTENTFUL_PAINT_MS'
  ];

  try {
    const dateResponse = await axios.post(dateEndpoint, { website });
    const endpointResponses = await Promise.all(
      endpoints.map(async (endpoint) => {
        return await retryFetch(async () => await getEndpointData(duration, endpoint));
      })
    );

    const dateData = dateResponse.data;
    const columns = [['x'].concat(dateData)];

    endpointResponses.forEach((response, index) => {
      const columnName = endpoints[index].replace('_', '');
      const formattedData = response.data.map(value => value / 1000);
      columns.push([columnName].concat(formattedData));
    });

    if (chart) {
      chart.destroy();
    }

    chart = c3.generate({
      bindto: '#chart',
      data: {
        x: 'x',
        columns: columns,
        type: 'line',
        colors: {
          DesktopLabData: 'purple',
          MobileLabData: 'blue',
          MobileFieldData: 'red',
          DesktopFieldData: 'orange'
        },
        axes: {
          DesktopLabData: 'y',
          MobileLabData: 'y',
          MobileFieldData: 'y',
          DesktopFieldData: 'y2'
        },
        names: {
          DesktopLabData: 'Desktop Lab Data',
          MobileLabData: 'Mobile Lab Data',
          MobileFieldData: 'Mobile Field Data',
          DesktopFieldData: 'Desktop Field Data'
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
            { value: 2.500, text: 'Good', class: 'good-line', position: 'start', class: 'good-line' }
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
        hide: ['good'],
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
      point: {
        show: false
      }
    });

  } catch (error) {
    console.error('Error fetching data:', error);
  }
}

async function retryFetch(fetchFunction, retries = 3, delay = 1000) {
  try {
    return await fetchFunction();
  } catch (error) {
    if (retries === 0) {
      throw error;
    }
    await new Promise(resolve => setTimeout(resolve, delay));
    return await retryFetch(fetchFunction, retries - 1, delay * 2);
  }
}

async function getEndpointData(duration, endpointSuffix) {
  let endpoint = `http://127.0.0.1:8000/api/response/${endpointSuffix}/${duration}`;
  
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
