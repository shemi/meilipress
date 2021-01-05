<template>
    <mp-index-field v-model="form"
                    :settings="settings"
                    class="mp-index-post-meta-field"
                    @remove="$emit('remove', form.id)"
                    :order="order"
                    :index="index"
                    :local-name-label="$trans('Meta Key')"
                    :local-name-message="$trans('The post meta key')">

        <div class="row">
            <mp-field slot="localName" :label="$trans('Meta value type')" :message="$trans('The type that the meta value will cast to')" message-tooltip :inline="false">
                <select v-model="form.type">
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
        }

    }
</script>

<style scoped>

</style>