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
        darkMode: false,
        showSmallSizeDialog: false,
        currentClassInformations: {},
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
                "end": "2021-03-10 12:58:00"
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

        apiBase: 'http://localhost:8000/api',
        professeurs: [],
        professeurCourant: null,
        avis: [],
        nouvelAvis: {},
        errors: [],
        mesAvis: [],
        showNoticeDialog: false,
        showCreateNoticeDialog: false
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
        showEventDetails ({ nativeEvent, event }) {
            console.log(event)

            this.currentClassInformations = {
                id: event.id,
                name: event.name,
                salle: event.salle,
                start: event.start,
                end: event.end,
            }



            this.showSmallSizeDialog = true;


            nativeEvent.stopPropagation()
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
                    this.mesAvis.push(response.data);
                })
                .catch(error => {
                    this.errors = Object.values(error.response.data);
                });
        },
        deleteAvis: function (avis) {
            axios.delete(this.apiBase + '/avis/' + avis.id)
                .then(response => {
                    this.avis.slice(this.avis.indexOf(avis), 1);
                    this.mesAvis.splice(this.mesAvis.indexOf(avis), 1);
                })
                .catch(error => {
                    console.log(error);
                });
        }
    },
    mounted() {
        this.getProfesseurs();
    }
})