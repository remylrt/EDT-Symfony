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
           let dateStr = this.today;
            this.today = new Date(new Date(dateStr).setDate(new Date(dateStr).getDate() - 1));
        },
        getNextDate(){
            let dateStr = this.today;
            this.today = new Date(new Date(dateStr).setDate(new Date(dateStr).getDate() + 1));
        },
        backToCurrentDate(){
            this.today = new Date()
        }
    }
    })