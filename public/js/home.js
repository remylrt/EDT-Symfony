var app = new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    vuetify: new Vuetify({
        lang: {
            current: 'fr'
        },
    }),
    data: {
        showClassroomDialog: false
    },
    methods: {
       showClassrooms(){ this.showClassroomDialog = true }
    },
})