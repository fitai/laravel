<template>
    <div class="team flexbox">
        <athlete v-for="athlete in filteredteam" v-bind:athlete="athlete" :key="athlete.id"></athlete>
    </div> 
</template>

<script>
    export default {
        props: ['filteredteam'],
        mounted() {
            console.log('Team component mounted.');
            this.$emit('loadteam');
        }
    }
</script>
