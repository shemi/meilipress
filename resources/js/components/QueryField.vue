<template>
    <div class="mp-query-builder-container">
        <div class="mp-query-builder" v-if="form.queries.length > 0">
            <div class="header">
                <mp-field :label="$trans('Relation')"
                          class="relation-field"
                          :message="$trans('The MySQL keyword used to join the clauses of the query.')">
                    <select v-model="form.relation" :id="'query_relation'+_uid">
                        <option value="AND">{{ 'AND (default)' | __ }}</option>
                        <option value="OR">{{ 'OR' | __ }}</option>
                    </select>
                </mp-field>

                <slot name="headerColumns"></slot>
            </div>

            <div class="queries">

                <div class="query" v-for="(query, index) in form.queries">
                    <div class="query-fields">
                        <slot :query="query" :index="index"></slot>
                    </div>

                    <div class="query-actions">
                        <a href="#" class="delete-button" @click.prevent="deleteQuery(index)">
                            {{ 'Delete' | __ }}
                        </a>
                    </div>
                </div>

            </div>

            <div class="add-button">
                <mp-button type="button" :large="false" @click.native.prevent="addQuery">
                    {{ "Add Query" | __ }}
                </mp-button>
            </div>
        </div>

        <div class="empty-message" v-else>
            <p>{{ 'The {queryName} is disabled, add a query to enable it by clicking the button below' | __({queryName}) }}</p>
            <mp-button type="button" :large="false" @click.native.prevent="addQuery">
                {{ "Add Query" | __ }}
            </mp-button>
        </div>

    </div>
</template>

<script>
    export default {

        props: {
            queryDefaultFields: Object,
            value: Object,
            queryName: String
        },

        data() {
            return {
                form: this.value
            }
        },

        methods: {
            addQuery() {
                if(! this.form.queries) {
                    this.form.queries = [];
                }

                this.form.queries.push({
                    ...this.queryDefaultFields
                });
            },

            deleteQuery(index) {
                if(! Array.isArray(this.form.queries)) {
                    return;
                }

                this.form.queries.splice(index, 1);
            }
        }

    }
</script>

<style lang="scss" scoped>

    .mp-query-builder-container {
        border-radius: 4px;
        border: 1px solid #7e8993;
        background-color: #fff;
        color: #32373c;
        padding: 8px;
        width: 100%;
    }

    .mp-query-builder {
        width: 100%;

        .header .mp-field {
            padding-bottom: 8px;
            grid-template-columns: 100px 1fr;
        }

        .queries {
            border-top: 1px solid #7e8993;
            border-bottom: 1px solid #7e8993;
            padding: 8px 0;
            margin: 8px 0;
        }

        .add-button {
            padding-top: 8px;
        }

        .query {
            background-color: #f1f1f1;
            border: 1px solid #7e8993;
            padding: 8px;
            border-radius: 4px;
            display: flex;
            align-items: center;

            .query-fields {
                flex-grow: 1;
                flex-shrink: 1;
            }

            .delete-button {
                color: #a00;
            }

            .query-actions {
                flex-grow: 0;
                flex-shrink: 0;
                -webkit-margin-start: 4px;
            }

            + .query {
                margin-top: 8px;
            }
        }

    }

    .empty-message {
        p {
            margin-top: 0;
            font-size: 1.2em;
        }
    }

</style>