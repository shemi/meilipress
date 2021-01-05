<template>
    <div class="mp-index-field" :class="{'is-open': isOpen}">
        <div class="glance">
            <div class="index">
                <span>{{ order + 1 }}</span>
            </div>

            <div class="info">
                <h4>
                    {{ settings.fieldName }}:
                    {{ form.description }}
                </h4>
                <div class="text">
                    <p>{{ localNameLabel | __ }}: <b>{{ form.localName }}</b></p>
                    <p>{{ 'Index Name' | __ }}: <b>{{ form.indexName }}</b></p>
                </div>
                <div class="badges">
                    <span class="dashicons dashicons-admin-network" :class="{'is-active': form.distinct}"></span>
                    <span class="dashicons dashicons-search" :class="{'is-active': form.searchable}"></span>
                    <span class="dashicons dashicons-visibility" :class="{'is-active': form.displayable}"></span>
                    <span class="dashicons dashicons-filter" :class="{'is-active': form.facetingable}"></span>
                </div>
            </div>

            <div class="actions">
                <div class="order-arrows">
                    <a href="#" class="move-up"
                       @click.prevent="moveUp"
                       :class="{'disabled': order <= 0}">
                        <span class="dashicons dashicons-arrow-up-alt2"></span>
                    </a>

                    <a href="#" class="move-down"
                       @click.prevent="moveDown"
                       :class="{'disabled': order + 1 >= index.fields.length}">
                        <span class="dashicons dashicons-arrow-down-alt2"></span>
                    </a>
                </div>

                <a href="#" class="edit-action" @click.prevent="isOpen = ! isOpen">
                    <span class="dashicons dashicons-edit edit-action"></span>
                </a>

                <span class="dashicons dashicons-lock" v-if="! settings.removable"></span>
                <a href="#" class="delete-action"
                   v-if="settings.removable"
                   @click.prevent="$emit('remove')">
                    <span class="dashicons dashicons-trash"></span>
                </a>
            </div>
        </div>

        <div class="form">
            <div class="row">
                <mp-field :label="$trans('Description')"
                          :message="$trans('Describe the field contents (visible only to administrators)')" message-tooltip :inline="false">
                    <input type="text" v-model="form.description">
                </mp-field>
            </div>
            <div class="row">
                <mp-field :label="$trans('Index Name')"
                          :message="$trans('The index attribute name')" message-tooltip :inline="false">
                    <input type="text" v-model="form.indexName">
                </mp-field>

                <slot name="localName" :form="form">
                    <mp-field :label="localNameLabel" :message="localNameMessage" message-tooltip :inline="false">
                        <input type="text" v-model="form.localName">
                    </mp-field>
                </slot>
            </div>

            <slot></slot>

            <div class="row">
                <mp-field :label="$trans('is searchable?')" :message="$trans('The values of the fields whose attributes are added to the searchable-attributes list are searched for matching query words.')" message-tooltip :inline="false">
                    <input type="checkbox" :id="form.id+'is_searchable'" v-model="form.searchable">
                </mp-field>

                <mp-field :label="$trans('is displayable?')" :message="$trans('The fields whose attributes are added to the displayed-attributes list are displayed in each matching document.')" message-tooltip :inline="false">
                    <input type="checkbox" :id="form.id+'is_displayable'" v-model="form.displayable">
                </mp-field>

                <mp-field :label="$trans('is faceting?')" :message="$trans('The attributes that can be used as facets for faceted search.')" message-tooltip :inline="false">
                    <input type="checkbox" :id="form.id+'is_faceting'" v-model="form.facetingable">
                </mp-field>
            </div>
        </div>
    </div>
</template>

<script>

    export default {

        props: {
            value: Object,
            settings: Object,
            order: Number,
            index: Object,
            localNameLabel: {
                type: String,
                default: "Local Name"
            },
            localNameMessage: {
                type: String,
                default: "Local Name"
            }
        },

        data() {
            return {
                form: this.value,
                isOpen: this.settings.isOpen || false,
            }
        },

        methods: {
            moveUp() {
                if(this.order - 1 < 0) {
                    return;
                }

                this.reorder(this.order, this.order - 1);
            },

            moveDown() {
                if(this.order + 1 > this.index.fields.lenght) {
                    return;
                }

                this.reorder(this.order, this.order + 1);
            },

            reorder(oldIndex, newIndex) {
                let arr = this.index.fields;

                if (newIndex >= arr.length) {
                    let k = newIndex - arr.length + 1;

                    while (k--) {
                        arr.push(undefined);
                    }
                }

                arr.splice(newIndex, 0, arr.splice(oldIndex, 1)[0]);

                this.$set(this.index, 'fields', arr);
            }
        }

    }
</script>

<style lang="scss" scoped>

    .mp-index-field {
        .glance {
            border-radius: 4px;
            border: 1px solid #7e8993;
            background-color: #fff;
            color: #32373c;
            padding: 8px;
            display: flex;
            align-items: center;

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

                .text {
                    display: flex;

                    p + p{
                        &:before {
                            content: "|";
                            margin: 0 4px;
                        }
                    }
                }

                .badges {
                    .dashicons {
                        opacity: 0.6;

                        &.is-active {
                            opacity: 1;
                            color: #007cba;
                        }
                    }
                }
            }

            .actions {
                display: flex;

                a {
                    text-decoration: none;
                }

                .edit-action {
                    margin: 0 8px;
                }

                .delete-action {
                    color: #a00;
                }
            }

            h4,
            p {
               margin: 0;
            }
        }

        + .mp-index-field {
            margin-top: 8px;
        }

        .form {
            display: none;
            border-radius: 0 0 4px 4px;
            border: 1px solid #7e8993;
            border-top: 0;
            background-color: #fff;
            color: #32373c;
            padding: 8px;

            .row {
                display: flex;
                align-items: center;

                + .row {
                    margin-top: 8px;
                }

                .mp-field {
                    margin: 0;
                    flex-grow: 1;
                    flex-shrink: 1;

                    + .mp-field {
                        -webkit-margin-start: 4px;
                    }

                    ::v-deep input:not([type="checkbox"]),
                    ::v-deep select {
                        width: 100%;
                    }
                }
            }
        }

        &.is-open {
            .glance {
                border-radius: 4px 4px 0 0;
                border-bottom: 0;
                background-color: #f1f1f1;
            }

            .form {
                display: block;
            }
        }

        .order-arrows {
            a {
                &.disabled {
                    cursor: default;
                    color: black;
                    opacity: 0.5;
                }
            }
        }

    }

</style>