
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// Include Moment.js
window.moment = require('moment');
window.moment = require('moment-timezone');

// Set time zone to UTC
moment.tz('UTC');


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
        trackerID: '', // tracker_id
        liftType: '', // lift_type
        liftWeight: '', // lift_weight
        maxReps: '', // init_num_reps
        repCount: 0, // calc_reps
        finalReps: 0, // final_num_reps
        liftComments: '', // user_comment
        trackerActive: false,
        athleteID: '', // athlete_id
        liftID: '', // lift_id
        liftOptions: [],
        adminWatch: false,
        currentVelocity: 0.0,
        testLift: false, // test_lift
        typeData: [],
        repCountEdit: 0,
        lastLift: [],
        nextLift: null,
        scheduledLiftID: null,
        currentTime: '',
        jsErrors: false,
        scheduleDate: null,
        scheduleTime: null
    },
    methods: {
        updateError(data) {
            console.log('updating jsErrors');
            this.jsErrors = data;
            setTimeout( function() {
                $('#js-errors').effect('shake');
            }, 300);
        },
        clearError() {
            this.jsErrors = false;
        },
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
            console.log('adding lift data to summary');
            this.liftWeight = $lift.lift_weight;
            this.liftType = $lift.lift_type;
            this.liftComments = $lift.user_comment;
            this.liftID = $lift.lift_id;
            this.maxReps = $lift.init_num_reps;
            this.finalReps = $lift.final_num_reps;
            this.typeData = $lift.type_data;
            this.trackerID = $lift.tracker_id;

            // Check to see if the final_num_reps has been set
            if ($lift.final_num_reps > 0) {
                console.log('final_num_reps > 0');
                this.repCountEdit = $lift.final_num_reps;
            } else {
                // If the final_num_reps has not been set, then use the initial rep count
                console.log('init_num_reps');
                this.repCountEdit = $lift.init_num_reps;
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
                        $('#velocity_chart').addClass('active');

                        // Show end lift button
                        $('#end-lift').show();

                        // Make noise for video recordings
                        beep();
                    })
                    .catch(error => {
                        this.updateError('Lift creation failed: ' + error.response.data.message);
                        console.log('Creating a lift has failed');
                        console.log(error.response);

                        // Hide spinner
                        $('#spinner-overlay').fadeOut().hide();
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

                // Disable update button
                $('#lift-edit-submit').prop('disabled', true);

                 // Update DB
                axios.patch('/lift/update', {
                    lift_id: this.liftID,
                    lift_type: this.liftType,
                    lift_weight: this.liftWeight,
                    final_num_reps: this.repCountEdit,
                    user_comment: this.liftComments
                })
                .then(response => {
                    console.log('Lift data updated');
                    console.log(response.data);

                    // Update typeData
                    var liftOptions = this.liftOptions;

                    // Loop through liftOptions to find the correct liftType
                    for (var option in liftOptions) {
                        if (liftOptions[option].name_display == this.liftType) {
                            // update typeData
                            this.typeData = liftOptions[option];
                        }
                    }

                    // Hide lift form
                    $('#lift-overlay').hide();

                    // Hide spinner
                    $('#spinner-overlay').fadeOut().hide();

                    // Enable update button
                    $('#lift-edit-submit').prop('disabled', false);
                })
                .catch(error => {
                    this.updateError('Update failed: ' + error.response.data.message);
                    console.log('Updating lift has failed');
                    console.log(error.response.data);

                    // Hide spinner
                    $('#spinner-overlay').fadeOut().hide();

                    // Enable update button
                    $('#lift-edit-submit').prop('disabled', false);
                });
            }
        },
        endLift() {
            console.log('Ending Lift');
            // console.log('testLift: ' + this.testLift);

            // Disable button
            $('#end-lift').prop('disabled', true);

            // Show spinner
            $('#spinner-overlay').css('display', 'flex').hide().fadeIn();

            // Post to controller and stop Lift
            axios.post('/lift/stop', {
                liftID: this.liftID,
                trackerID: this.trackerID,
                testLift: this.testLift,
                calcReps: this.repCount,
                scheduledLiftID: this.scheduledLiftID
            })
            .then(response => {
                console.log(response.data);
                window.location.href = "/lift/summary/"+this.liftID;
            })
            .catch(error => {
                    this.updateError('Ending lift failed: ' + error.response.data.message);
                    console.log('Ending lift has failed');
                    console.log(error.response.data);

                    // Hide spinner
                    $('#spinner-overlay').fadeOut().hide();

                    // Enable update button
                    $('#end-lift').prop('disabled', false);
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
        presetLift(type, variation, equipment, tracker, weight, reps) {
            console.log('Preset loading');
            this.liftWeight = weight;
            this.maxReps = reps;
            this.$children[0].type=type;
            this.$children[0].variation=variation;
            this.$children[0].equipment=equipment;
            this.trackerID = tracker;

        },
        setAdminTracker() {
            this.adminWatch = true;
            drawLine();
            this.liftType = "";
            this.liftWeight = "";
            this.repCount = "";
        },
        addLiftOptions(options) {
            this.liftOptions = options;
        },
        updateTime() {
            let self = this;

            // Set initial value for currentTime
            self.currentTime = moment.tz('America/New_York').format();

            // Create loop that updates currentTime every minute
            setInterval(function() {
                var time = moment.tz('America/New_York').format();
                // console.log('updating currentTime to ' + time);
                self.currentTime = time;
            }, 1000);
        },
        getNextLift() {
            console.log('Checking for scheduled lifts');

            // Get User's next lift
            axios.get('/lift/next')
                .then(response => {
                    this.nextLift = response.data;
            });
        },
        useNextLift() {
            if (this.nextLift) {
                console.log('using nextLift data');

                // Update lift data fields
                this.liftWeight = this.nextLift.lift_weight;
                this.maxReps = this.nextLift.reps;
                this.$children[0].type = this.nextLift.lift_type;
                this.$children[0].variation = this.nextLift.lift_variation;
                this.$children[0].equipment = this.nextLift.lift_equipment;
                this.trackerID = this.nextLift.tracker_id;

                // update scheduledLiftID
                this.scheduledLiftID = this.nextLift.id;

            } else {
                console.log('Next lift not scheduled');
            }
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
                        this.trackerActive = packet.header.active;

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


        // Get User's last lift
        axios.get('/lift/last')
            .then(response => {
                this.lastLift = response.data;
                $('#time-since-last-lift').css({opacity: 1});
                $('#time-since-last-lift-mobile').css({opacity: 1});
            });

        // Trigger updateTime method
        this.updateTime();

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

        function updateLiftFields(type, variation, equipment, tracker, weight, reps) {
            this.$children[0].type=type;
            this.$children[0].variation=variation;
            this.$children[0].equipment=equipment;
            this.trackerID = tracker;
            this.liftWeight = weight;
            this.maxReps = reps;
        }

        // Pre-assigned lift setups
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
                    // updateLiftFields('Squat', 'Back', 'BB', 555, 245, 5);
                    this.liftWeight = 245;
                    this.maxReps = 5;
                    this.$children[0].type="Squat";
                    this.$children[0].variation="Back";
                    this.$children[0].equipment="BB";
                    this.trackerID = 555;
                    console.log('sqb245 loaded');
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

        // Dynamic Lift parameters
        if (getUrlParameter('liftWeight')) {
            var weight = getUrlParameter('liftWeight'); 
            this.liftWeight = weight;
        }
        if (getUrlParameter('trackerID')) {
            var trackerID = getUrlParameter('trackerID'); 
            this.trackerID = trackerID;
        }
        if (getUrlParameter('maxReps')) {
            var reps = getUrlParameter('maxReps'); 
            this.maxReps = reps;
        }
        if (getUrlParameter('liftType')) {
            var type = getUrlParameter('liftType'); 
            this.$children[0].type = type;
        }
        if (getUrlParameter('liftEq')) {
            var equipment = getUrlParameter('liftEq'); 
            this.$children[0].equipment = equipment;
        }
        if (getUrlParameter('liftVariation')) {
            var variation = getUrlParameter('liftVariation'); 
            this.$children[0].variation = variation;
        }
        if (getUrlParameter('nameSafe')) {
            var nameSafe = getUrlParameter('nameSafe'); 

            axios.post('/lift/get-type', { nameSafe: nameSafe })
                .then(response => {
                    var liftType = response.data;
                    this.$children[0].type = liftType.type;
                    this.$children[0].variation = liftType.variation;
                    this.$children[0].equipment = liftType.equipment;
                });
        }

    },
    computed: {
        filteredteam() {
            return this.team.filter( (athlete) => {
                return athlete.search_string.indexOf(this.search.toLowerCase()) > -1;
            });
        },
        // repsActual: {
        //     get: function() {

        //         // If finalReps has been updated, then use this number
        //         if (this.finalReps > 0) {
        //             this.repCountEdit = parseInt(this.finalReps);
        //             console.log('rCE = fR');
        //             return this.finalReps;
        //         }

        //         // If it hasn't been updated, use the initial reps input at the start of the lift
        //         this.repCountEdit = parseInt(this.maxReps);
        //         console.log('rCE = mR');
        //         return this.maxReps;
        //     },
        //     set: function(val) {
        //         // this.finalReps = parseInt(val);
        //         this.repCountEdit = parseInt(val);
        //         console.log('rCE = val');
        //     }
        // },

        // Create URL parameters for Next Rep button
        nextRepParams: function() {
            var params = "/lift/?";

            if (this.liftWeight) {
                var weight = this.liftWeight;
                params += "liftWeight=" + weight + "&";
            }

            console.log('checking reps');
            var reps = this.finalReps;

            if (reps < 1) {
                reps = this.maxReps;
            }
            params += "maxReps=" + reps + "&";

            if (this.trackerID) {
                var trackerID = this.trackerID;
                params += "trackerID=" + trackerID + "&";
            }

            if (this.typeData.type) {
                var type = this.typeData.type;
                params += "liftType=" + type + "&";
            }

            if (this.typeData.variation) {
                var variation = this.typeData.variation;
                params += "liftVariation=" + variation + "&";
            }

            if (this.typeData.equipment) {
                var equipment = this.typeData.equipment;
                params += "liftEq=" + equipment + "&";
            }

            return params;
        },

        // calculate the time since athlete's last lift
        timeSinceLastLift: function() {

            var lastLift = moment.tz(this.lastLift.ended_at, 'America/New_York');
            var time = '';

            // return moment(lastLift).from(this.currentTime);
            var diff = moment(this.currentTime).diff(lastLift);
            if (diff < 3600000) {
                time = moment(diff).format('mm:ss');
            } else {
                time = moment(lastLift).from(this.currentTime);
            }

            return time;

        },

        // Create option to start next scheduled lift
        showNextLift: function() {
            var scheduled = null;

            if (this.nextLift) {
                var equipment = this.nextLift.lift_equipment;
                var type = this.nextLift.lift_type;
                var variation = this.nextLift.lift_variation;
                var weight = this.nextLift.lift_weight;
                var reps = this.nextLift.reps;
                var tracker = this.nextLift.tracker_id;

                var name = type + " " + variation + " - " + equipment;

                scheduled = {
                    name: name,
                    reps: reps,
                    tracker: tracker
                }
            }

            return scheduled;
        }
    }
});