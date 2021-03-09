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
           let dateStr = this.today;
            this.today = new Date(new Date(dateStr).setDate(new Date(dateStr).getDate() - 1));
            this.getCours();
        },
        getNextDate(){
            let dateStr = this.today;
            this.today = new Date(new Date(dateStr).setDate(new Date(dateStr).getDate() + 1));
            this.getCours();
        },
        backToCurrentDate(){
            this.today = new Date()
            this.getCours();
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
            axios.get(this.apiBase + '/cours/' + date )
                .then(response => {
                    this.events = response.data;
                })
                .catch(error => {
                    console.log(error);
                })
        },

        getProfesseurs: function () {
            axios.get(this.apiBase + '/professeurs')
                .then(response => {
                    this.professeurs = response.data;
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