<template>
    <div id="lift-data" class="lift-data">
        <div class="data-item">
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
            <h3 class="title">Reps:</h3>
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
        </div>
    </div>
</template>

<script>
    export default {
        props: ['summary', 'liftTypes', 'liftWeight', 'liftType', 'liftComments', 'repCount'],
        data() {
            return {
                comments: this.summary.user_comment,
                weight: this.summary.lift_weight,
                type: this.summary.lift_type,
                reps: ''
            }
        },
        mounted() {
            this.$emit('addlift', this.summary);

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
