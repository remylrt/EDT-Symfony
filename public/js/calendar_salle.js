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
        salle: null,
        isLoadingClass: true,
        apiBase: 'http://localhost:8000/api',
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
            axios.get(this.apiBase + '/salles/' + this.salle )
                .then(response => {
                    this.events = response.data[0].cours;
                    this.isLoadingClass = false;
                })
                .catch(error => {
                    console.log(error);
                    this.isLoadingClass = false;
                })
        },
        exportCalendarAsICS: function () {
            axios.get(this.apiBase + '/salles/' + this.salle)
                .then(response => {
                    coursSemaine = response.data[0].cours;
                    let cal = ics();

                    coursSemaine.forEach(event => {
                        cal.addEvent(`${ event.type } de ${ event.name }`, `Avec ${ event.professeur } en salle ${ event.salle }.`, 'IUT de Bayonne et du Pays Basque', event.start, event.end);
                    });

                    cal.download("EmploiDuTemps");
                })
                .catch(error => {
                    console.log(error);
                })
        },
    },
    mounted() {
        this.salle = window.location.pathname.split("/")[window.location.pathname.split("/").length - 1];
        this.getCours();
    }
})