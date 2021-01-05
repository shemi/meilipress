<template>
    <query-field :query-default-fields="queryDefaultFields" v-model="form" :query-name="$trans('Taxonomy Query')">
        <template v-slot:default="{query, index}">
            <div class="tax-query-fields">
                <mp-field :label="$trans('Taxonomy')"
                          :inline="false"
                          :message="$trans('Taxonomy being queried (e.g. category, post_format, movie).')"
                          class="mp-taxonomy-field"
                          message-tooltip>
                    <v-select :options="taxonomies"
                              :reduce="taxonomy => taxonomy.id"
                              v-model="query.taxonomy">
                    </v-select>
                </mp-field>

                <mp-field :label="$trans('Terms')"
                          :inline="false"
                          :message="$trans('Term or terms to filter by. Single string or comma-separated.')"
                          class="mp-terms-field"
                          message-tooltip>
                    <input type="text"
                           :id="_uid+'_tax_query_terms_'+index"
                           v-model="query.terms">
                </mp-field>

                <mp-field :label="$trans('Field')" :inline="false"
                          message-tooltip
                          class="mp-field-field"
                          :message="$trans('The field to match terms against.')">
                    <select v-model="query.field"
                            :id="_uid+'_tax_query_field_'+index">
                        <option value="term_id">term_id {{ '(default)' | __ }}</option>
                        <option value="slug">slug</option>
                        <option value="name">name</option>
                        <option value="term_taxonomy_id">term_taxonomy_id</option>
                    </select>
                </mp-field>

                <mp-field :label="$trans('Operator')" :inline="false" message-tooltip
                          :message="$trans('MySQL operator used with terms in the WHERE clause')">
                    <select v-model="query.operator"
                            :id="_uid+'_tax_query_operator_'+index">
                        <option value="AND">AND</option>
                        <option value="IN">IN {{ '(default)' | __ }}</option>
                        <option value="NOT IN">NOT IN</option>
                        <option value="EXISTS">EXISTS</option>
                        <option value="NOT EXISTS">NOT EXISTS</option>
                    </select>
                </mp-field>

                <mp-field :label="$trans('Include Children')" :inline="false" message-tooltip
                          :message="$trans('Whether to include child terms, or not.')">
                    <select v-model="query.include_children"
                            :id="_uid+'_tax_query_include_children_'+index">
                        <option :value="true">{{ 'Yes (default)' | __ }}</option>
                        <option :value="false">{{ 'No' | __ }}</option>
                    </select>
                </mp-field>
            </div>
        </template>
    </query-field>
</template>

<script>
    import QueryField from "./QueryField";

    export default {

        props: {
            queryDefaultFields: {
                type: Object,
                default: () => ({...MeiliPress.index_data.taxQueryDefaultForm})
            },
            value: Object
        },

        data() {
            return {
                taxonomies: MeiliPress.index_data.taxonomies,
                form: this.value
            }
        },

        methods: {

        },

        components: {QueryField}

    }
</script>

<style lang="scss" scoped>

    .tax-query-fields {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(2, 1fr);
        grid-gap: 4px;

        .mp-field {
            margin-top: 0;
        }

        .mp-taxonomy-field {
            grid-area: 1 / 1 / 2 / 3;
        }

        .mp-terms-field {
            grid-area: 1 / 3 / 2 / 5;
        }

        .mp-field-field {
            grid-area: 2 / 1 / 3 / 3;
        }

        ::v-deep input:not(.vs__search),
        ::v-deep select,
        ::v-deep  .v-select {
            width: 100%;
        }

    }

</style>