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
        isLoadingTeachers: true,
        isLoadingNotices: true,
        apiBase: 'http://localhost:8000/api',
        professeurs: [],
        professeurCourant: null,
        avis: [],
        nouvelAvis: {},
        errors: [],
        mesAvis: [],
        showNoticeDialog: false,
        showCreateNoticeDialog: false,
        collapseNavbar: true,
    },
    methods: {
        getPreviousDate(){
            this.isLoadingClass = true;
            this.isLoadingTeachers = true;
            let dateStr = this.today;
            this.today = new Date(new Date(dateStr).setDate(new Date(dateStr).getDate() - 1));
            this.getCours();
            this.getProfesseurs();
        },
        getNextDate(){
            this.isLoadingClass = true;
            this.isLoadingTeachers = true;
            let dateStr = this.today;
            this.today = new Date(new Date(dateStr).setDate(new Date(dateStr).getDate() + 1));
            this.getCours();
            this.getProfesseurs();
        },
        backToCurrentDate(){
            this.isLoadingClass = true;
            this.isLoadingTeachers = true;
            this.today = new Date()
            this.getCours();
            this.getProfesseurs();
        },
        showEventDetails ({ nativeEvent, event }) {
            console.log(event)

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
        getOnlyHourseOfDate(date){
            let newDate = new Date(date);
            let hours = newDate.getHours();
            let minute = newDate.getMinutes()
            if(minute === 0){
                minute = "00";
            }
            let time = `${ hours }:${ minute }`;
            return time;
        },
        getFormattedTodaysDate() {
            let year = this.today.getFullYear();
            let month = this.today.getMonth() + 1;

            if(month < 10){month = "0" + month;}

            let day = this.today.getDate();

            if(day < 10){day = "0" + day;}

            let formattedDate = `${ year }-${ month }-${ day }`;

            return formattedDate;
        },
        getCours(){
            let date = this.getFormattedTodaysDate();

            axios.get(this.apiBase + '/cours/' + date )
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
            let date = this.getFormattedTodaysDate();

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

        },
        getProfesseurs: function () {
            let date = this.getFormattedTodaysDate();

            axios.get(this.apiBase + '/professeurs/daily/' + date)
                .then(response => {
                    this.professeurs = response.data;
                    this.isLoadingTeachers = false;
                })
                .catch(error => {
                    console.log(error);
                })
        },
        showNotice(avisCourant){
            this.showNoticeDialog = true;
            this.getAvis(avisCourant);
        },
        showCreateNotice(professeur){
            this.showCreateNoticeDialog = true;
            this.professeurCourant = professeur;
        },

        getAvis: function (professeur) {
            //this.nouvelAvis = this.newAvis();
            this.errors = [];
            axios.get(this.apiBase + '/avis/' + professeur.id)
                .then(response => {
                    this.professeurCourant = professeur;
                    this.avis = response.data;
                })
                .catch(error => {
                    console.log(error);
                })
        },
        newAvis: function () {
            return {
                note: 0,
                commentaire: '',
                emailEtudiant: '',
            };
        },
        postAvis: function () {
            this.errors = [];
            axios.post(this.apiBase + '/avis/' + this.professeurCourant.id, this.nouvelAvis)
                .then(response => {
                    this.avis.unshift(response.data);
                    this.nouvelAvis = this.newAvis();
                    this.showCreateNoticeDialog = false;
                    this.professeurCourant = {};

                    this.mesAvis.push(response.data);
                })
                .catch(error => {
                    this.errors = Object.values(error.response.data);
                });
        },
        deleteAvis: function (avis) {
            axios.delete(this.apiBase + '/avis/' + avis.id)
                .then(response => {
                    this.avis.splice(this.avis.indexOf(avis), 1);
                    this.mesAvis.splice(this.mesAvis.indexOf(avis), 1);
                })
                .catch(error => {
                    console.log(error);
                });
        }
    },
    mounted() {
        this.getProfesseurs();
        this.getCours();
    }
})