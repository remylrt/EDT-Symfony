var app = new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    vuetify: new Vuetify({
        lang: {
            current: 'fr'
        }
    }),
    data: {
        appName: "EDT",
        today: new Date(),
        darkMode: false,
        showSmallSizeDialog: false,
        currentClassInformations: {},
        events: [

        ],
        isLoadingClass: true,
        apiBase: 'http://localhost:8000/api',
        ready: false
     },
    methods: {
        showEventDetails ({ nativeEvent, event }) {
            this.currentClassInformations = {
                id: event.id,
                name: event.name,
                professeur: event.professeur,
                type: event.type,
                salle: event.salle,
                start: event.start,
                end: event.end,
            }
            this.showSmallSizeDialog = true;
            nativeEvent.stopPropagation()
        },
        getOnlyHourOfDate(date){
            let newDate = new Date(date);
            let hours = newDate.getHours();
            let minute = newDate.getMinutes()
            if(minute === 0){
                minute = "00";
            }
            if(minute < 10){
                minute = "0" + minute;
            }
            let time = `${ hours }:${ minute }`;
            return time;
        },
        getCours(){
            let year = this.today.getFullYear();
            let month = this.today.getMonth();
            month++
            if(month < 10){
                month = "0" + month;
            }
            let day = this.today.getDate();
            if(day < 10){
                day = "0" + day;
            }
            let date = `${ year }-${ month }-${ day }`
            axios.get(this.apiBase + '/cours/weekly/' + date )
                .then(response => {
                    this.events = response.data;
                    this.isLoadingClass = false;
                })
                .catch(error => {
                    console.log(error);
                    this.isLoadingClass = false;
                })
        },
        exportCalendarAsICS: function () {
            let year = this.today.getFullYear();
            let month = this.today.getMonth() + 1;
            if (month < 10) { month = "0" + month; }

            let day = this.today.getDate();

            if (day < 10) { day = "0" + day; }

            let date = `${ year }-${ month }-${ day }`;

            axios.get(this.apiBase + '/cours/weekly/' + date)
                .then(response => {
                    coursSemaine = response.data;

                    let cal = ics();

                    coursSemaine.forEach(event => {
                        cal.addEvent(`${ event.type } de ${ event.name }`, `Avec ${ event.professeur } en salle ${ event.salle }.`, 'IUT de Bayonne et du Pays Basque', event.start, event.end);
                    });

                    cal.download("EmploiDuTemps");
                })
                .catch(error => {
                    console.log(error);
                })

        }
    },
    computed: {
        cal () {
            return this.ready ? this.$refs.calendar : null
        },
        nowY () {
            return this.cal ? this.cal.timeToY(this.cal.times.now) + 'px' : '-10px'
        },
    },
    mounted() {
        this.getCours();
        this.ready = true;
    }
})