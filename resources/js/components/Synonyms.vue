<template>
    <div class="mp-synonyms">
        <div class="list">

            <div v-for="set in form" :key="set.id" class="item">
                <div class="inputs">
                    <mp-field :label="$trans('Primary Term')" :inline="false">
                        <input type="text" v-model="set.synonym">
                    </mp-field>

                    <mp-field :label="$trans('Alternatives')" :inline="false">
                        <v-select v-model="set.words" :options="[]" multiple taggable push-tags>
                            <template #no-options="{ search, searching, loading }">
                                {{ 'Type word(s) and press Enter' | __ }}
                            </template>
                        </v-select>
                    </mp-field>
                </div>

                <div class="actions">
                    <a href="#"
                       @click.prevent="removeSet(set.id)"
                       class="delete-action">
                        <span class="dashicons dashicons-trash"></span>
                    </a>
                </div>
            </div>

        </div>

        <div class="add-action">
            <mp-button type="button"
                       @click.native.prevent="addSet"
                       large primary>
                {{ 'Add Set' | __ }}
            </mp-button>
        </div>
    </div>
</template>

<script>
    import {v4 as uuid} from 'uuid';

    export default {

        props: {
            value: Array,
        },

        data() {
            return {
                form: this.value
            }
        },

        methods: {
            addSet() {
                this.form.push({
                    'id': uuid(),
                    'synonym': '',
                    'words': []
                });

                this.$emit('value', this.form);
            },

            removeSet(id) {
                const index = this.form.findIndex(set => set.id === id);

                if(index < 0) {
                    return;
                }

                this.$delete(this.form, index);
                this.$emit('value', this.form);
            }

        }

    }
</script>

<style lang="scss" scoped>

    .mp-synonyms {

        .add-action {
            margin-top: 8px;
        }

        .item {
            border-radius: 4px;
            border: 1px solid #7e8993;
            background-color: #fff;
            color: #32373c;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;

            + .item {
                margin-top: 8px;
            }

            .inputs {
                flex-grow: 1;
                flex-shrink: 1;
                display: flex;
                align-items: flex-start;
                -webkit-margin-end: 8px;

                .v-select {
                    width: 100%;
                }

                > .mp-field {
                    margin: 0;

                    &:last-child {
                        -webkit-margin-start: 8px;
                        flex-grow: 1;
                    }
                }
            }

            .delete-action {
                text-decoration: none;
                color: #a00;
            }

        }

    }

</style>