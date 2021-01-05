<template>
    <div class="mp-field mp-field" :class="{'is-inline': inline || isCheckbox, 'message-tooltip': messageTooltip || isCheckbox, 'is-checkbox': isCheckbox}">
        <label :for="id" v-if="label" ref="label">
            {{ label }}
            <span class="dashicons dashicons-editor-help" v-if="messageTooltip"></span>
        </label>

        <div class="input" :class="{'is-error': error}">
            <slot></slot>

            <p class="message" v-if="message && ! $slots['message']">
                {{ message }}
            </p>

            <slot name="message"></slot>

            <p class="message error-message" v-if="error">
                {{ error }}
            </p>
        </div>
    </div>
</template>

<script>
    const $ = window.jQuery;
    import tippy from 'tippy.js';

    export default {

        props: {
            message: String,
            label: String,
            error: String,
            messageTooltip: Boolean,
            inline: {
                type: Boolean,
                default: true
            }
        },

        data() {
            return {
                id: '',
                isCheckbox: false
            }
        },

        mounted() {
            const input = this.$el.querySelector('input, select');

            if(input && input.id) {
                this.id = input.id;
            }

            if(input && input.type === 'checkbox') {
                this.isCheckbox = true;
            }

            if(this.messageTooltip) {
                tippy($(this.$refs.label).find('.dashicons').get(0), {
                    interactive: true,
                    appendTo: () => document.body,
                    content: this.$slots['message'] ? $(this.$slots['message'][0].elm).html() : this.message,
                    allowHTML: true,
                });
            }
        },

        methods: {

        }

    }
</script>

<style lang="scss" scoped>

    .mp-field {
        position: relative;

        + .mp-field {
            margin-top: 8px;
        }

        &.is-inline {
            display: grid;
            grid-template-columns: 200px 1fr;

            > label {
                vertical-align: top;
                padding: 20px 10px 20px 0;
                width: 200px;
            }
        }

        label {
            line-height: 1.3;
            font-weight: 600;
        }

        .input {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;

            &.is-error {
                select,
                input,
                textarea {
                    border-color: #a00;
                }
            }

        }

        .message {
            margin-top: 4px;
            margin-bottom: 0;
            font-size: 14px;
            color: #666;

            &.error-message {
                color: #a00;
                font-weight: 400;
            }
        }

        &.message-tooltip {
            .message:not(.error-message) {
                display: none;
            }
        }

        &.is-checkbox {
            display: flex;
            align-items: center;
            flex-direction: row-reverse;
            justify-content: flex-end;

            > label {
                padding: 10px 0;
            }

            .input {
                display: block;
                line-height: 1;
                -webkit-margin-end: 8px;

                input {
                    margin: 0;
                }
            }
        }

    }

</style>