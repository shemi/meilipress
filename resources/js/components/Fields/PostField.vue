<template>
    <mp-index-field v-model="form"
                    :settings="settings"
                    @remove="$emit('remove', form.id)"
                    :order="order"
                    :index="index"
                    :local-name-label="$trans('Post Attribute')"
                    class="mp-index-post-field">

        <mp-field slot="localName" :label="$trans('Post Attribute')" message-tooltip :inline="false">
            <select v-model="form.localName"
                    @change="setType"
                    :disabled="! settings.removable">
                <option value="">{{ 'Select property' | __ }}</option>
                <option v-for="(cast, prop) in settings.properties" :value="prop">{{ prop }}</option>
            </select>

            <p class="message" slot="message">
                {{ 'WP_Post attribute name' | __ }}
                <a href="https://developer.wordpress.org/reference/classes/wp_post/" target="_blank">
                    {{ 'See wordpress documentation for mor info' }}
                </a>
            </p>
        </mp-field>

        <div class="row">
            <mp-field slot="localName" :label="$trans('Attribute Type')" :message="$trans('The type that the attribute will cast to')" message-tooltip :inline="false">
                <select v-model="form.type" disabled>
                    <option value="">{{ 'Select type' | __ }}</option>
                    <option v-for="type in settings.types" :value="type">{{ type }}</option>
                </select>
            </mp-field>
        </div>

    </mp-index-field>
</template>

<script>
    export default {

        props: {
            value: Object,
            settings: Object,
            index: Object,
            order: Number,
        },

        data() {
            return {
                form: this.value
            }
        },

        methods: {
            setType() {
                this.form.type = this.settings.properties[this.form.localName];
            }
        }

    }
</script>

<style scoped>

</style>