var app = new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    vuetify: new Vuetify({
        lang: {
            current: 'fr'
        }
        }),
    data: {
        windowsHeigt: window.innerHeight,
        appName: "EDT",
        todayDate: new Date().toLocaleDateString(),
        today: new Date(),
        events: [
            {
                name: 'Weekly Meeting',
                start: '2021-03-08 09:45',
                end: '2021-03-08 10:00',
            },
        ],
    },
    methods: {

    }
    })