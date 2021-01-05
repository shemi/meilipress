<template>
    <query-field :query-default-fields="queryDefaultFields" v-model="form" :query-name="$trans('Date Query')">
        <template v-slot:headerColumns>
            <mp-field :label="$trans('Column')"
                      :message="$trans('Posts column to query against. Default: ‘post_date’.')">
                <select v-model="form.column" :id="'date_query_column'+_uid">
                    <option value="post_date">post_date {{ '(default)' | __ }}</option>
                    <option value="post_date_gmt">post_date_gmt</option>
                    <option value="post_modified">post_modified</option>
                    <option value="post_modified_gmt">post_modified_gmt</option>
                </select>
            </mp-field>
        </template>

        <template v-slot:default="{query, index}">
            <div class="date-query-fields">
                <mp-field :label="$trans('Before Date')" :inline="false" message-tooltip>
                    <input type="text"
                           :id="_uid+'_date_query_date_before_'+index"
                           v-model="query.before">
                    <p class="message" slot="message" v-html="$trans('Textual date/time description.')"></p>
                </mp-field>

                <mp-field :label="$trans('After Date')" :inline="false" message-tooltip>
                    <input type="text"
                           :id="_uid+'_date_query_date_after_'+index"
                           v-model="query.after">
                    <p class="message" slot="message" v-html="$trans('Textual date/time description.')"></p>
                </mp-field>

                <mp-field :label="$trans('Inclusive')" :inline="false" message-tooltip
                          :message="$trans('Include results from dates specified, or not.')">
                    <select v-model="query.inclusive" :id="_uid+'_date_query_date_inclusive_'+index">
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
                default: () => ({...MeiliPress.index_data.dateQueryDefaultForm})
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

    .date-query-fields {
        display: flex;
        align-items: center;

        .mp-field {
            margin-top: 0;

            + .mp-field {
                -webkit-margin-start: 4px;
            }
        }

    }

</style>