<template>
    <query-field :query-default-fields="queryDefaultFields" v-model="form" :query-name="$trans('Meta Query')">
        <template v-slot:default="{query, index}">
            <div class="meta-query-fields">
                <mp-field :label="$trans('Key')"
                          :inline="false"
                          :message="$trans('Meta key to filter by.')"
                          message-tooltip>
                    <input type="text"
                           :id="_uid+'_meta_query_key_'+index"
                           v-model="query.key">
                </mp-field>

                <mp-field :label="$trans('Value')"
                          :inline="false"
                          :message="$trans('Meta value to filter by.')"
                          message-tooltip>
                    <input type="text"
                           :id="_uid+'_meta_query_value_'+index"
                           v-model="query.value">
                </mp-field>

                <mp-field :label="$trans('Compare')" :inline="false"
                          message-tooltip
                          class="mp-field-field"
                          :message="$trans('Operator used for comparing the value.')">
                    <select v-model="query.compare"
                            :id="_uid+'_tax_query_field_'+index">
                        <option value="=">= {{ '(default)' | __ }}</option>
                        <option value="!=">!=</option>
                        <option value=">">></option>
                        <option value=">=">>=</option>
                        <option value="<"><</option>
                        <option value="<="><=</option>
                        <option value="LIKE">LIKE</option>
                        <option value="NOT LIKE">NOT LIKE</option>
                        <option value="BETWEEN">BETWEEN</option>
                        <option value="NOT BETWEEN">NOT BETWEEN</option>
                        <option value="EXISTS">EXISTS</option>
                        <option value="NOT EXISTS">NOT EXISTS</option>
                        <option value="REGEXP">REGEXP</option>
                        <option value="NOT REGEXP">NOT REGEXP</option>
                        <option value="RLIKE">RLIKE</option>
                    </select>
                </mp-field>

                <mp-field :label="$trans('Type')" :inline="false" message-tooltip
                          :message="$trans('Meta value type.')">
                    <select v-model="query.type"
                            :id="_uid+'_meta_query_type_'+index">
                        <option value="NUMERIC">NUMERIC</option>
                        <option value="BINARY">BINARY</option>
                        <option value="DATE">DATE</option>
                        <option value="CHAR">CHAR {{ '(default)' | __ }}</option>
                        <option value="DATETIME">DATETIME</option>
                        <option value="DECIMAL">DECIMAL</option>
                        <option value="SIGNED">SIGNED</option>
                        <option value="TIME">TIME</option>
                        <option value="UNSIGNED">UNSIGNED</option>
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
                default: () => ({...MeiliPress.index_data.metaQueryDefaultForm})
            },
            value: Object
        },

        data() {
            return {
                form: this.value
            }
        },

        methods: {

        },

        components: {QueryField}

    }
</script>

<style lang="scss" scoped>

    .meta-query-fields {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(2, 1fr);
        grid-gap: 4px;

        .mp-field {
            margin-top: 0;
        }

        ::v-deep input,
        ::v-deep select {
            width: 100%;
        }
    }

</style>