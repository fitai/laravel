
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
        trackerID: '',
        liftType: '',
        liftWeight: '',
        maxReps: '',
        repCount: 0,
        finalReps: 0,
        liftComments: '',
        trackerActive: false,
        athleteID: '',
        liftID: '',
        liftOptions: [],
        adminWatch: false,
        currentVelocity: 0.0,
        testLift: false
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
            this.maxReps = $lift.init_num_reps;
            this.finalReps = $lift.final_num_reps;

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

                // Show spinner
                $('#spinner-overlay').css('display', 'flex').hide().fadeIn();

                // Disable button
                $('#lift-new-submit').prop('disabled', true);

                axios.post('/lift/store', this.$data)
                    .then(response => {
                        console.log(response.data);
                        this.liftID = response.data['liftID'];

                        // Hide lift form
                        $('#lift-overlay').hide();

                        // Hide spinner
                        $('#spinner-overlay').fadeOut().hide();

                        // Show end lift button
                        $('#end-lift').show();

                        // Make noise for video recordings
                        beep();
                    });
                // if (secDelay == null || secDelay == '') {
                //     secDelay = 0;
                // }
                // liftDelay(secDelay);
            }
        },
        editLift($event) {
            $event.preventDefault();
            console.log('Editing lift data...');

            var validate = $('form#lift-edit').valid();
            if (validate == true) {         
                console.log('Form validation successful...');

                // Update rep count on summary
                this.repCount = this.finalReps;

                // Show spinner
                $('#spinner-overlay').css('display', 'flex').hide().fadeIn();

                // Disable button
                $('#lift-edit-submit').prop('disabled', true);

                 // Update DB
                axios.patch('/lift/update', {
                    lift_id: this.liftID,
                    lift_type: this.liftType,
                    lift_weight: this.liftWeight,
                    final_num_reps: this.finalReps,
                    user_comment: this.liftComments
                })
                .then(response => {
                    console.log(response.data);

                    // Hide lift form
                    $('#lift-overlay').hide();

                    // Hide spinner
                    $('#spinner-overlay').fadeOut().hide();
                });

            }
        },
        endLift() {
            console.log('Ending Lift');
            console.log('testLift: ' + this.testLift);

            // Disable button
            $('#end-lift').prop('disabled', true);

            // Show spinner
            $('#spinner-overlay').css('display', 'flex').hide().fadeIn();

            // Post to controller and stop Lift
            axios.post('/lift/stop', {
                liftID: this.liftID,
                trackerID: this.trackerID,
                testLift: this.testLift
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
        setTrackerID(id) {
            this.trackerID = id;
            console.log('trackerID updated');
        },
        setAdminTracker() {
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
                if (packet.header.tracker_id == this.trackerID) {

                    // console.log(data + ' - time: ' +  now);

                    // If this is the Admin - Watch screen, then fill in lift data
                    if (this.adminWatch) {

                        console.log(packet);

                        this.liftType = packet.header.lift_type;
                        this.liftWeight = packet.header.lift_weight;
                        this.repCount = packet.header.calc_reps;

                        // update charts
                        updateCharts();

                    } else if (packet.header.active == true) {

                         // Change tracker status to active
                        this.trackerActive = packet.header.active;
                        this.repCount = packet.header.calc_reps;

                        // Update charts and get velocity to update Vue
                        var vel = updateCharts();
                        this.currentVelocity = vel.toFixed(2);

                    }

                    function updateCharts() {
                        // Get Power and Velocity values
                        var power = mean(packet.content.p_rms);
                        var velocity = mean(packet.content.v_rms);

                        // Update the charts with data
                        updateGauge(velocity);
                        updateLine(velocity);
                        updateColumn(power); 

                        return(velocity);
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

        // Check for test parameter on lift
        function getUrlParameter(sParam) {
            var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        }

        if (getUrlParameter('test') == 1) {
            this.liftWeight = 35;
            this.maxReps = 10;
            this.$children[0].type="Deadlift";
            this.$children[0].variation="Standard";
            this.$children[0].equipment="BB";
        }
        if (getUrlParameter('tim')) {
            switch(getUrlParameter('tim')) {
                case 'sqb245':
                    this.liftWeight = 245;
                    this.maxReps = 5;
                    this.$children[0].type="Squat";
                    this.$children[0].variation="Back";
                    this.$children[0].equipment="BB";
                    this.trackerID = 555;
                    break;
                 case 'sqf245':
                    this.liftWeight = 245;
                    this.maxReps = 5;
                    this.$children[0].type="Squat";
                    this.$children[0].variation="Front";
                    this.$children[0].equipment="BB";
                    this.trackerID = 555;
                    break;
                 case 'dsb245':
                    this.liftWeight = 245;
                    this.maxReps = 5;
                    this.$children[0].type="Deadlift";
                    this.$children[0].variation="Standard";
                    this.$children[0].equipment="BB";
                    this.trackerID = 555;
                    break;
                 case 'dub245':
                    this.liftWeight = 245;
                    this.maxReps = 5;
                    this.$children[0].type="Deadlift";
                    this.$children[0].variation="Sumo";
                    this.$children[0].equipment="BB";
                    this.trackerID = 555;
                    break;
                case 'drb245':
                    this.liftWeight = 245;
                    this.maxReps = 5;
                    this.$children[0].type="Deadlift";
                    this.$children[0].variation="Romanian";
                    this.$children[0].equipment="BB";
                    this.trackerID = 555;
                    break;
                case 'dtt245':
                    this.liftWeight = 245;
                    this.maxReps = 5;
                    this.$children[0].type="Deadlift";
                    this.$children[0].variation="Trap Bar";
                    this.$children[0].equipment="TB";
                    this.trackerID = 555;
                    break;
                case 'bsb195':
                    this.liftWeight = 195;
                    this.maxReps = 5;
                    this.$children[0].type="Bench";
                    this.$children[0].variation="Standard";
                    this.$children[0].equipment="BB";
                    this.trackerID = 555;
                    break;
                case 'bcb185':
                    this.liftWeight = 185;
                    this.maxReps = 5;
                    this.$children[0].type="Bench";
                    this.$children[0].variation="Close Grip";
                    this.$children[0].equipment="BB";
                    this.trackerID = 555;
                    break;
                case 'bsd90':
                    this.liftWeight = 90;
                    this.maxReps = 5;
                    this.$children[0].type="Bench";
                    this.$children[0].variation="Standard";
                    this.$children[0].equipment="DB";
                    this.trackerID = 555;
                    break;
                case 'bib185':
                    this.liftWeight = 185;
                    this.maxReps = 5;
                    this.$children[0].type="Bench";
                    this.$children[0].variation="Incline";
                    this.$children[0].equipment="BB";
                    this.trackerID = 555;
                    break;
                case 'bid80':
                    this.liftWeight = 80;
                    this.maxReps = 5;
                    this.$children[0].type="Bench";
                    this.$children[0].variation="Incline";
                    this.$children[0].equipment="DB";
                    this.trackerID = 555;
                    break;
            }
        }

    },
    computed: {
        filteredteam() {
            return this.team.filter( (athlete) => {
                return athlete.search_string.indexOf(this.search.toLowerCase()) > -1;
            });
        }
    }
});