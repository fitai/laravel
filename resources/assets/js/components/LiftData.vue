<template>
    <div id="data-container" class="data-container flexbox">
        <div class="tab">
            <span id="collarID" class="count-number">{{ collarID }}</span> <span class="count-text">collar</span>
        </div>
        <div class="tab">
            <span id="lift-type" class="count-number lift-type">{{ liftType }}</span> <span class="count-text">lift</span>
        </div>
        <div class="tab">
            <span id="lift-weight" class="count-number">{{ liftWeight }}</span> <span class="count-text">lbs</span>
        </div>
        <div class="tab">
            <span id="rep-count" class="count-number">{{ repCount }}</span> <span class="count-text">reps</span>
        </div>
        <div class="tab">
            <span id="active" class="count-number">{{ collarActive }}</span> <span class="count-text">active</span>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['collarID', 'liftWeight', 'liftType', 'repCount', 'collarActive', 'athleteID'],
        mounted() {
            console.log('LiftData mounted');
            this.$emit('add-athlete', this.athleteID);
        }
    }
</script>
