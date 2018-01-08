<template>
    <div id="lift-select" class="lift-select">
        <h3>Lift Type</h3>
        <div class="flexbox wrap">
            <div class="lift-option xs-100 md-45 lg-30">
                <label for="type" class="field-title">Type</label>
                <select name="type" v-model="type" required>
                    <option disabled>Type</option>
                    <option v-for="type in typeOptions" v-bind:value="type.type">
                        {{ type.type }}
                    </option>
                </select>
            </div>
            <div class="lift-option xs-100 md-45 lg-30">
                <label for="variation" class="field-title">Variation</label>
                <select name="variation" v-model="variation" required>    
                    <option disabled>Variation</option>
                    <option v-for="variation in variationsAvailable" v-bind:value="variation">
                        {{ variation }}
                    </option>               
                </select>
            </div>
            <div class="lift-option xs-100 md-45 lg-30">
                <label for="equipment" class="field-title">Equipment</label>
                <select name="equipment" v-model="equipment" required>      
                    <option disabled>Equipment</option>
                    <option v-for="equipment in equipmentAvailable" v-bind:value="equipment">
                        {{ equipment }}
                    </option>              
                </select>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['typeOptions', 'equipmentOptions', 'variationOptions', 'options'],
        data() {
            return {
                type: '',
                variation: '',
                equipment: ''
            }
        },
        mounted() {
            console.log('Lift-select mounted');
            // this.$emit('loadteam');
            this.$emit('getnextlift');
        },
        computed: {
            typesAvailable: function() {
                var types = this.options;
                return types;
            },
            variationsAvailable: function() {
                var variationArr = [];
                var typeSelected = this.type;

                // filter variations available by selected type
                function matchType(item) {
                    if (item.type == typeSelected) {
                        return item;
                    }
                }
                var variations = this.options.filter(matchType);

                // Loop through fitlered options and remove duplicate values
                variations.forEach(function(item) {
                    if (!variationArr.includes(item.variation)) {
                        variationArr.push(item.variation);
                    }
                });

                return variationArr;
            },
            equipmentAvailable: function() {
                var equipmentArr = [];
                var variatonSelected = this.variation;

                // filter equipment available by selected variation
                function matchVariation(item) {
                    if (item.variation == variatonSelected) {
                        return true;
                    }
                }
                var equipment = this.options.filter(matchVariation);

                // Loop through fitlered options and remove duplicate values
                equipment.forEach(function(item) {
                    if (!equipmentArr.includes(item.equipment)) {
                        equipmentArr.push(item.equipment);
                    }
                });

                return equipmentArr;
            },
            liftName: function() {
                var variation = this.variation;
                var equipment = this.equipment;
                var type = this.type;
                var fullName = '';

                // Filter options for exact match
                function matchAll(item) {
                    if ( (item.variation == variation) && (item.type == type) && (item.equipment == equipment) ) {
                        return true;
                    }
                }
                var match = this.options.filter(matchAll);

                // Build lift name string if match is found
                if (match[0]) {
                    fullName = match[0].type + " " + match[0].variation + " - " + match[0].equipment;
                }

                return fullName;
            }
        },
        watch: {
            liftName: function(val) {
                this.$emit('updatelifttype', val);
            } 
        }
    }
</script>
