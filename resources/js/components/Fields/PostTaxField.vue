<template>
    <mp-index-field v-model="form"
                    :settings="settings"
                    :local-name-label="$trans('Taxonomy')"
                    @remove="$emit('remove', form.id)"
                    :order="order"
                    :index="index"
                    class="mp-index-post-field">

        <mp-field slot="localName" :label="$trans('Taxonomy')" :message="$trans('Post taxonomy')" message-tooltip :inline="false">
            <select v-model="form.localName">
                <option value="">{{ 'Select taxonomy' | __ }}</option>
                <option v-for="tax in taxonomies" :value="tax.id">{{ tax.label }}</option>
            </select>
        </mp-field>

        <div class="row">
            <mp-field :label="$trans('Fields')"
                      :inline="false">
                <select v-model="form.fields">
                    <option value="">{{ 'Select return field' | __ }}</option>
                    <option v-for="(desc, key) in settings.fields" :value="key">{{ key }}: {{ desc }}</option>
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
                form: this.value,
                taxonomies: []
            }
        },

        mounted() {
            this.fetchTaxonomies();
        },

        methods: {
            async fetchTaxonomies() {
                const res = await this.$api.post('index/fields/getTaxonomies', {
                    post_type: this.index.query.post_type
                });

                this.taxonomies = res.data;
            }
        }

    }
</script>

<style scoped>

</style>