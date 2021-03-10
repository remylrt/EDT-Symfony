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
        apiBase: 'http://localhost:8000/api',
        salles: [],
    },
    methods: {
        getSalles(){
            axios.get(this.apiBase + '/salles' )
                .then(response => {
                    this.salles = response.data;
                })
                .catch(error => {
                    console.log(error);
                })
        },

    },
    mounted() {
        this.getSalles();
    }
})