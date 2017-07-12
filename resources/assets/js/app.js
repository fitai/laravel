
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
Vue.component('lift-summary', require('./components/LiftSummary.vue'));
Vue.component('lift-data', require('./components/LiftData.vue'));
Vue.component('lift-select', require('./components/LiftSelect.vue'));

const app = new Vue({
    el: '#app',
    data: {
        search: '',
    	team: [],
        collarID: '',
        liftType: '',
        liftWeight: '',
        repCount: '',
        liftComments: '',
        collarActive: 'False',
        athleteID: '',
        liftID: '',
        liftOptions: [],
        adminWatch: false
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
    	},
        addAthlete($id) {
            console.log('adding athleteID: ' + $id);
            this.athleteID = $id;
        },
        addLift($lift) {
            this.liftWeight = $lift.lift_weight;
            this.liftType = $lift.lift_type;
            this.liftComments = $lift.user_comment;
            this.liftID = $lift.lift_id;

            if ($lift.final_num_reps > 0) {
                this.repCount = $lift.final_num_reps;
            } else {
                this.repCount = $lift.init_num_reps;
            }
        },
        newLift($event) {
            $event.preventDefault();
            console.log('Submitting lift data...');
            var validate = $('form#lift-new').valid();
            if (validate == true) {         
                console.log('Form validation successful...');

                axios.post('/lift/store', this.$data)
                    .then(response => {
                        console.log(response.data);
                        this.liftID = response.data['liftID'];
                        $('#overlay').hide();
                        beep();
                    });
                // if (secDelay == null || secDelay == '') {
                //     secDelay = 0;
                // }
                // liftDelay(secDelay);
            }
        },
        endLift() {
            console.log('Ending Lift');

            // Post to controller and stop Lift
            axios.post('/lift/stop', {
                collarID: this.collarID
            })
            .then(response => {
                console.log(response.data);
                window.location.href = "/lift/summary/"+this.liftID;
            });
        },
        updateSummaryField(prop, val, field) {
            console.log(prop + " : " + val);

            // Update DB
            axios.patch('/lift/update', {
                lift_id: this.liftID,
                prop: prop,
                val: val
            })
            .then(response => {
                console.log(response.data);

                // Update instance
                switch(prop) {
                    case 'liftComments':
                        this.liftComments = val;
                        break;
                    case 'repCount':
                        this.repCount = val;
                        break;
                    case 'liftWeight':
                        this.liftWeight = val;
                        break;
                    case 'liftType':
                        this.liftType = val;
                        break;
                }

                // Hide the edit field and show the saved value
                $('#'+field).show();
                $('#'+field+'-input').hide();
            });
        },
        updateLiftType(name) {
            this.liftType = name;
            console.log('updated liftType');
        },
        setCollarID(id) {
            this.collarID = id;
            console.log('collarID updated');
        },
        setAdminCollar() {
            this.adminWatch = true;
            drawLine();
            this.liftType = "";
            this.liftWeight = "";
            this.repCount = "";
        }
    },
    mounted() {

        // Socket.io listener
        socket.on('lifts', function(data) {
            var now = new Date().getTime();
            // console.log(data + ' - time: ' +  now);

            // Parse data
            var packet = JSON.parse(data);

            // Make sure data is JSON
            if (packet) {

                // Update charts and lift data
                if (packet.header.collar_id == this.collarID) {

                    // console.log(data + ' - time: ' +  now);

                    // If this is the Admin - Watch screen, then fill in lift data
                    if (this.adminWatch) {

                        this.liftType = packet.header.lift_type;
                        this.liftWeight = packet.header.lift_weight;
                        this.repCount = packet.header.calc_reps;

                        // update charts
                        updateCharts();

                    } else if (packet.header.active == true) {

                         // Change collar status to active
                        this.collarActive = packet.header.active;

                        // If collar is active, then update charts
                        updateCharts();

                    }

                    function updateCharts() {
                        // Get Power and Velocity values
                        var power = mean(packet.content.p_rms);
                        var velocity = mean(packet.content.v_rms);

                        // Update the charts with data
                        updateGauge(velocity);
                        updateLine(velocity);
                        updateColumn(power); 
                    }

                    function mean(obj) {
                        var sum = obj.reduce(function(acc, val) {
                            return acc + val;
                        }, 0);
                        var length = obj.length;

                        return sum/length;

                    }

                }

            }
            
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