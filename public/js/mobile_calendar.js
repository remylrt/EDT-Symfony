var app = new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    vuetify: new Vuetify({
        lang: {
            current: 'fr'
        },
        breakpoint: {
            mobileBreakpoint: 'sm' // This is equivalent to a value of 960
        },
        }),
    data: {
        windowsHeigt: window.innerHeight,
        appName: "EDT",
        today: new Date(),
        darkMode: false,
        events: [
            {
                "id": 1,
                "name": "M1102: Introduction à l'algorithmique",
                "type": "Cours",
                "salle": "125",
                "start": "2021-03-08 08:30:00",
                "end": "2021-03-08 12:30:00"
            },
            {
                "id": 2,
                "name": "M1102: Introduction à l'algorithmique",
                "type": "TP",
                "salle": "126",
                "start": "2021-03-10 12:07:00",
                "end": "2021-03-24 12:07:00"
            },
            {
                "id": 3,
                "name": "M1102: Introduction à l'algorithmique",
                "type": "TP",
                "salle": "124",
                "start": "2021-03-08 15:21:00",
                "end": "2021-03-08 15:22:00"
            }
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
        },
        showEvent ({ nativeEvent, event }) {
            console.log(event)

            nativeEvent.stopPropagation()
        },
    },
})