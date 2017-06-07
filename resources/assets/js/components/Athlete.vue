<template>
    <div class="athlete">
        <a :href="'/switch/athlete/' + athlete.athlete_id">
            <img src="/images/blank-person.png" width="200" height="200">
            <br>
            <span>{{ athlete.athlete_first_name }} {{ athlete.athlete_last_name }}</span>
        </a>
    </div> 
</template>

<script>
    export default {
        props: ['athlete'],
        data() {
            return {
                url: 'test'
            }
        }
    }
</script>
