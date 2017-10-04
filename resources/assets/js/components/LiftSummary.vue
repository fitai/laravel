<template>
    <div id="summary-data">
        <div id="summary-data-edit" class="summary-data-edit">
            <span id="universal-edit" class="summary-edit">Edit Lift Data <i class="dripicons-document-edit"></i></span>
        </div>
        <div id="lift-data" class="flexbox wrap flexcenter lift-data">
            <div class="data-box lift-id center">
                <div class="data">{{ liftID }}</div>
                <div class="label">Lift ID</div>
            </div>
            <div class="data-box lift-type center">
                <div class="data">{{ liftType }}</div>
                <div class="label">Lift Type</div>
            </div>
            <div class="data-box lift-weight center">
                <div class="data">{{ liftWeight }}</div>
                <div class="label">lbs</div>
            </div>
            <div class="data-box lift-rep-count center">
                <div class="data">{{ repCountEdit }}</div>
                <div class="label">Rep Count</div>
            </div>
            <div class="data-box lift-max-reps center">
                <div class="data">{{ maxReps }}</div>
                <div class="label">Init Reps</div>
            </div>
            <div class="data-box tracker-id center">
                <div class="data">{{ trackerID }}</div>
                <div class="label">Tracker ID</div>
            </div>
    <!--         <div class="data-item">
                <h3 class="title">Exercise:</h3>
                <div id="lift-type" class="summary-item">
                    <span id="summary-lift-type">{{ liftType }}</span> <span id="lift-type-edit" class="summary-edit"><i class="dripicons-document-edit" @click="editField('lift-type')"></i></span>
                </div>
                <div id="lift-type-input" style="display: none;">
                    <select v-model="type">
                        <option v-for="option in liftTypes" v-bind:value="option.name_display">
                            {{ option.name_display }}
                        </option>
                    </select>
                    <i class="dripicons-checkmark" @click="updateField('lift-type')"></i><i class="dripicons-cross" @click="cancelEdit('lift-type')"></i>
                </div>
            </div>
            <div class="data-item">
                <h3 class="title">Weight:</h3>
                <div id="weight" class="summary-item">
                    <span id="summary-weight">{{ liftWeight }}</span> <span id="weight-edit" class="summary-edit"><i class="dripicons-document-edit" @click="editField('weight')"></i></span>
                </div>
                <div id="weight-input" style="display: none;">
                    <input v-model="weight" type="number"><i class="dripicons-checkmark" @click="updateField('weight')"></i><i class="dripicons-cross" @click="cancelEdit('weight')"></i>
                </div>
            </div>
            <div class="data-item">
                <h3 class="title">Max Reps:</h3>
                <div id="max-reps" class="summary-item">
                    <span id="summary-max-reps">{{ maxReps }}</span>
                </div>
            </div>
            <div class="data-item">
                <h3 class="title">Calculated Reps:</h3>
                <div id="reps" class="summary-item">
                    <span id="summary-reps">{{ repCount }}</span> <span id="reps-edit" class="summary-edit"><i class="dripicons-document-edit" @click="editField('reps')"></i></span>
                </div>
                <div id="reps-input" style="display: none;">
                    <input v-model="reps" type="number"><i class="dripicons-checkmark" @click="updateField('reps')"></i><i class="dripicons-cross" @click="cancelEdit('reps')"></i>
                </div>
            </div>
            <div class="data-item">
                <h3 class="title">Comments:</h3>
                <div id="comments" class="summary-item">
                    <pre><p id="summary-comments">{{ liftComments }}</p></pre>
                    <span id="comments-edit" class="summary-edit"><i class="dripicons-document-edit" @click="editField('comments')"></i></span>
                </div>
                <div id="comments-input" style="display: none;">
                    <textarea v-model="comments"></textarea><i class="dripicons-checkmark" @click="updateField('comments')"></i><i class="dripicons-cross" @click="cancelEdit('comments')"></i>
                </div>
            </div> -->
        </div>
        <div class="data-box comments center">
            <div class="label">Comments</div>
            <div class="data">{{ liftComments }}</div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['liftID', 'summary', 'liftTypes', 'liftWeight', 'liftType', 'liftComments', 'repCountEdit', 'maxReps'],
        data() {
            return {
                comments: this.summary.user_comment,
                weight: this.summary.lift_weight,
                type: this.summary.lift_type,
                reps: '',
                trackerID: this.summary.tracker_id
            }
        },
        mounted() {
            // emit lift to parent
            this.$emit('addlift', this.summary);

            // emit lift types to parent
            this.$emit('addliftoptions', this.liftTypes);

            if (this.summary.final_num_reps > 0) {
                this.reps = this.summary.final_num_reps;
            } else {
                this.reprepsCount = this.summary.init_num_reps;
            }
        },
        methods: {

            editField(field) {
                $('#'+field).hide();
                $('#'+field+'-input').show();
            },
            cancelEdit(field) {
                $('#'+field).show();
                $('#'+field+'-input').hide();
            },
            updateField(field) {
                var val = '';
                var prop = '';

                // Get prop and value
                switch(field) {
                    case 'comments':
                        prop = 'liftComments';
                        val = this.comments;
                        break;
                    case 'reps':
                        prop = 'repCount';
                        val = this.reps;
                        break;
                    case 'weight':
                        prop = 'liftWeight';
                        val = this.weight;
                        break;
                    case 'lift-type':
                        prop = 'liftType';
                        val = this.type;
                        break;
                }
                
                // Emit to parent to update DB and Vue instance
                this.$emit('updatefield', prop, val, field);
            }

        }

    }
</script>
