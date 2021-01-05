<template>
    <div class="mp-ranking-rules">

        <div class="rules">

            <div class="rule" v-for="(rule, index) in form">
                <div class="index">
                    <span>{{ index + 1 }}</span>
                </div>

                <div class="info">
                    <h4 class="name">{{ rule }}</h4>
                    <p class="description">{{ getDescription(rule) }}</p>
                </div>

                <div class="actions">
                    <div class="order-arrows">
                        <a href="#" class="move-up"
                           @click.prevent="moveUp(rule)"
                           :class="{'disabled': index <= 0}">
                            <span class="dashicons dashicons-arrow-up-alt2"></span>
                        </a>

                        <a href="#" class="move-down"
                           @click.prevent="moveDown(rule)"
                           :class="{'disabled': index + 1 >= form.length}">
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </a>
                    </div>

                    <a href="#" class="delete-action" @click.prevent="deleteRule(rule)">
                        <span class="dashicons dashicons-trash"></span>
                    </a>
                </div>
            </div>
        </div>

        <div class="add-rule">
            <mp-field :label="$trans('Select a rule to add')" :inline="false">
                <select id="mp_index_ranking_select_rule" v-model="selectedRuleToAdd">
                    <option value="">Select Rule</option>
                    <option v-for="rule in rulesToAdd" :value="rule">{{ rule }}</option>
                </select>
            </mp-field>

            <mp-field :label="$trans('Select a field')"
                      v-if="customRules.indexOf(selectedRuleToAdd) >= 0"
                      :inline="false">
                <select id="mp_index_ranking_select_field" v-model="selectedFieldToAdd">
                    <option value="">Select Rule</option>
                    <option v-for="field in fieldsToAdd" :value="field">{{ field }}</option>
                </select>
            </mp-field>

            <mp-button type="button"
                       @click.native.prevent="addRule"
                       :disabled="addButtonDisabled" large primary>
                {{ 'Add Rule' | __ }}
            </mp-button>
        </div>

    </div>
</template>

<script>
    export default {

        props: {
            value: Array,
            indexForm: Object,
        },

        data() {
            return {
                form: this.value,
                rulesDescriptions: MeiliPress.index_data.rankingRulesDescriptions,
                customRules: ['desc', 'asc'],
                allowedFieldTypes: ['date', 'int', 'float', 'bool'],
                selectedRuleToAdd: '',
                selectedFieldToAdd: ''
            }
        },

        methods: {

            getDescription(rule) {
                const regex = /^[^\(\)]+/;
                let ruleDescriptionKey = (regex.exec(rule) || [])[0];

                if(! ruleDescriptionKey || ! this.rulesDescriptions[ruleDescriptionKey]) {
                    return "";
                }

                return this.rulesDescriptions[ruleDescriptionKey];
            },

            moveUp(rule) {
                const oldIndex = this.form.indexOf(rule);

                if(oldIndex - 1 < 0) {
                    return;
                }

                this.reorder(oldIndex, oldIndex - 1);
            },

            moveDown(rule) {
                const oldIndex = this.form.indexOf(rule);

                if(oldIndex + 1 > this.form.lenght) {
                    return;
                }

                this.reorder(oldIndex, oldIndex + 1);
            },

            reorder(oldIndex, newIndex) {
                let arr = this.form;

                if (newIndex >= arr.length) {
                    let k = newIndex - arr.length + 1;

                    while (k--) {
                        arr.push(undefined);
                    }
                }

                arr.splice(newIndex, 0, arr.splice(oldIndex, 1)[0]);
                this.form = arr;

                this.$emit('value', this.form);
            },

            deleteRule(rule) {
                const index = this.form.indexOf(rule);

                if(index < 0) {
                    return;
                }

                this.$delete(this.form, index);
                this.$emit('value', this.form);
            },

            addRule() {
                if(this.addButtonDisabled) {
                    return;
                }

                let rule = this.selectedRuleToAdd;

                if(this.customRules.indexOf(rule) >= 0) {
                    rule = `${rule}(${this.selectedFieldToAdd})`;
                }

                this.form.push(rule);
                this.$emit('value', this.form);

                this.selectedFieldToAdd = '';
                this.selectedRuleToAdd = '';
            }

        },

        computed: {

            addButtonDisabled() {
                if(! this.selectedRuleToAdd) {
                    return true;
                }

                return this.customRules.indexOf(this.selectedRuleToAdd) >= 0 && !this.selectedFieldToAdd;
            },

            rulesToAdd() {
                let rules = [];

                for(let rule of Object.keys(this.rulesDescriptions)) {
                    if(this.form.indexOf(rule) < 0) {
                        rules.push(rule);
                    }
                }

                return rules;
            },

            fieldsToAdd() {
                const regex = /\(([^)]+)\)/;
                let fields = [];
                let existsFields = this.form.map((rule) => {
                    const matches = regex.exec(rule);

                    if(matches && matches[1]) {
                        return matches[1];
                    }

                    return undefined;
                })
                .filter(f => f);

                for(let field of this.indexForm.fields) {
                    if(this.allowedFieldTypes.indexOf(field.form.type) < 0) {
                        continue;
                    }

                    if(existsFields.indexOf(field.form.indexName) >= 0) {
                        continue;
                    }

                    fields.push(field.form.indexName);
                }

                return fields;
            }

        }

    }
</script>

<style lang="scss" scoped>

    .mp-ranking-rules {

        .rule {
            border-radius: 4px;
            border: 1px solid #7e8993;
            background-color: #fff;
            color: #32373c;
            padding: 8px;
            display: flex;
            align-items: center;

            + .rule {
                margin-top: 8px;
            }

            .index {
                flex-grow: 0;
                flex-shrink: 0;
                -webkit-margin-end: 8px;

                span {
                    width: 2em;
                    height: 2em;
                    border-radius: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background-color: #f1f1f1;
                    border: 1px solid #7e8993;
                }
            }

            .info {
                flex-grow: 1;
                flex-shrink: 1;

                h4 {
                    margin: 0;
                }
            }

            .actions {
                flex-shrink: 0;
                flex-grow: 0;
                display: flex;

                a {
                    text-decoration: none;
                }

                .delete-action {
                    color: #a00;
                    -webkit-margin-start: 8px;
                }

                .order-arrows {
                    flex-shrink: 0;
                    flex-grow: 0;

                    a {
                        &.disabled {
                            cursor: default;
                            color: black;
                            opacity: 0.5;
                        }
                    }
                }
            }

        }

        .add-rule {
            display: flex;
            margin-top: 8px;
            align-items: flex-end;

            .mp-field {
                width: 150px;

                select {
                    width: 100%;
                }

                + .mp-field {
                    -webkit-margin-start: 8px;
                }
            }

            .mp-button {
                -webkit-margin-start: 8px;
            };
        }

    }

</style>