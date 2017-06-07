
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));
Vue.component('athlete', require('./components/Athlete.vue'));
Vue.component('team', require('./components/Team.vue'));

const app = new Vue({
    el: '#app',
    data: {
        search: '',
    	team: []
    },
    methods: {

    	getTeam() {
    		axios.get('/team').then(response =>  {
                var temp = response.data;

                // Loop through each athlete and createa search string for easy Vue filtering
                for (var i=0; i < response.data.length; i++) {
                    var lowerString = '';
                    lowerString = temp[i].athlete_first_name + ' ' + temp[i].athlete_last_name;
                    lowerString = lowerString.toLowerCase();
                    
                    // push search string to object
                    temp[i].search_string = lowerString;
                }

                // bind athletes to Vue
    			this.team = temp;
    		});
    	}

    },
    mounted() {

        socket.on('lifts:Test', function(data) {
            console.log(data);
        }.bind(this));

    },
    computed: {
        filteredteam() {
            return this.team.filter( (athlete) => {
                return athlete.search_string.indexOf(this.search.toLowerCase()) > -1;
            });
        }
    }
});