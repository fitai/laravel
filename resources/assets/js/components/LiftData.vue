<template>
    <div class="flexbox wrap flexcenter lift-data">
        <div class="data-box lift-type center">
            <div class="data">{{ liftType }}</div>
            <div class="label">Lift Type</div>
        </div>
        <div class="data-box lift-weight center">
            <div class="data">{{ liftWeight }}</div>
            <div class="label">lbs</div>
        </div>
        <div class="data-box lift-current-reps center">
            <div class="data">{{ repCount }}</div>
            <div class="label">Current Rep</div>
        </div>
        <div class="data-box lift-max-reps center">
            <div class="data">{{ maxReps }}</div>
            <div class="label">Max Reps</div>
        </div>
        <div class="data-box collar-id center">
            <div class="data">{{ collarID }}</div>
            <div class="label">Tracker ID</div>
        </div>
        <div class="data-box lift-status center">
            <div class="data">{{ collarActive }}</div>
            <div class="label">Active</div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['collarID', 'liftWeight', 'liftType', 'maxReps', 'repCount', 'collarActive', 'athleteID', 'rfidCollarID'],
        mounted() {
            console.log('LiftData mounted');
            this.$emit('add-athlete', this.athleteID);

            if (this.rfidCollarID !== 0) {
                this.$emit('set-collar-id', this.rfidCollarID);
            }
        }
    }
</script>
