<template>
    <div id="lift-data" class="lift-data">
        <div class="data-item">
            <h3 class="title">Exercise:</h3>
            <div id="lift-type" class="summary-item">
                <span id="summary-lift-type">{{ liftType }}</span> <span id="lift-type-edit" class="summary-edit" onClick="editSummary('lift-type');"><i class="dripicons-document-edit"></i></span>
            </div>
        </div>
        <div class="data-item">
            <h3 class="title">Weight:</h3>
            <div id="weight" class="summary-item"><span id="summary-weight">{{ liftWeight }}</span> <span id="weight-edit" class="summary-edit" onClick="editSummary('weight');"><i class="dripicons-document-edit"></i></span></div>
        </div>
        <div class="data-item">
            <h3 class="title">Reps:</h3>
            <div id="reps" class="summary-item"><span id="summary-reps">{{ repCount }}</span> <span id="reps-edit" class="summary-edit" onClick="editSummary('reps');"><i class="dripicons-document-edit"></i></span></div>
        </div>
        <div class="data-item">
            <h3 class="title">Comments:</h3>
            <div id="comments" class="summary-item">
                <p id="summary-comments">{{ liftComments }}</p>
                <span id="comments-edit" class="summary-edit" onClick="editSummary('comments');"><i class="dripicons-document-edit"></i></span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['summary', 'liftWeight', 'liftType', 'liftComments', 'repCount'],
        mounted() {
            this.$emit('addlift', this.summary);
        }
    }
</script>
