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
        getPreviousDate(){
            var dateStr = this.today;
            var days = 1;
            var result = new Date(new Date(dateStr).setDate(new Date(dateStr).getDate() - days));
            this.today = result.toISOString().substr(0, 10);
        },
        getNextDate(){
            var dateStr = this.today;
            var days = 1;
            var result = new Date(new Date(dateStr).setDate(new Date(dateStr).getDate() + days));
            this.today = result.toISOString().substr(0, 10);
        },
        backToCurrentDate(){
            this.today = new Date()
        }
    }
    })