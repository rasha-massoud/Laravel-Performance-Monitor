let chart;
const website = 'https://laravel.com/';

async function generateChart(duration) {
  const endpoint = `http://127.0.0.1:8000/api/response`;  

  // Adjust the endpoint based on duration
  const dateEndpoint = `${endpoint}/date/${duration}`;
  const desktopLabEndpoint = `${endpoint}/desktop_lab_LARGEST_CONTENTFUL_PAINT/${duration}`;
  const mobileLabEndpoint = `${endpoint}/mobile_lab_LARGEST_CONTENTFUL_PAINT/${duration}`;
  const mobileEndpoint = `${endpoint}/mobile_LARGEST_CONTENTFUL_PAINT_MS/${duration}`;
  const desktopEndpoint = `${endpoint}/desktop_LARGEST_CONTENTFUL_PAINT_MS/${duration}`;

  try {
    const [dateResponse, desktopLabResponse, mobileLabResponse, mobileResponse, desktopResponse] = await Promise.all([
      axios.post(dateEndpoint, { website: website }),
      axios.post(desktopLabEndpoint, { website: website }),
      axios.post(mobileLabEndpoint, { website: website }),
      axios.post(mobileEndpoint, { website: website }),
      axios.post(desktopEndpoint, { website: website })
    ]);

    let dateData = dateResponse.data;
    let data = desktopLabResponse.data.map(value => value / 1000);
    let mobileLabData = mobileLabResponse.data.map(value => value / 1000);
    let mobileData = mobileResponse.data.map(value => value / 1000);
    let desktopData = desktopResponse.data.map(value => value / 1000);

    const columns = [
      ['x'].concat(dateData),
      ['DesktopLabData'].concat(data),
      ['MobileLabData'].concat(mobileLabData)
    ];

    if (!areAllValuesZero(mobileData)) {
      columns.push(['MobileFieldData'].concat(mobileData));
    }

    columns.push(['DesktopFieldData'].concat(desktopData));

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
          DesktopFieldData: 'y'
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
          tick: {
            values: [0, 1, 2, 3, 4],
          },
          label: {
            text: 'Results (s)',
            position: 'outer-middle'
          }
        },
      },
      grid: {
        y: {
          lines: [
            { value: 1.0, class: 'grid-line', position: 'start' },
            { value: 2.0, class: 'grid-line', position: 'start' },
            { value: 2.500, text: 'Good', class: 'good-line', position: 'start', class: 'good-line' },
            { value: 3.0, class: 'grid-line', position: 'start' },
            { value: 4.0, text: 'Bad', class: 'bad-line', position: 'start', class: 'bad-line' }
          ]
        },
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
      point: {
        show: false
      }
    });
  } catch (error) {
    console.error('Error while fetching data:', error);
  }
}

document.getElementById('durationSelect').addEventListener('change', function (event) {
  event.preventDefault();
  let duration = parseInt(this.value);
  generateChart(duration);
});

generateChart(7);

const areAllValuesZero = (arr) => {
  return arr.every(value => value === 0);
}
