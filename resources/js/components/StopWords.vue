<template>
    <div class="mp-stop-words">
        <mp-field :label="$trans('Stop Words')"
                  message-tooltip
                  :message="$trans('Each stop word in a new line')">
            <textarea rows="20"
                      ref="textarea"
                      @input="stopWordsToArray"
                      @keydown="setAutoHeight"
                      :style="{height: height}"
                      v-model="stopWords"></textarea>
        </mp-field>
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
                form: this.value,
                stopWords: '',
                height: 'auto',
            }
        },

        watch: {
            value: {
                immediate: true,
                handler() {
                    this.valueToString();
                }
            }
        },

        methods: {

            valueToString() {
                this.stopWords = (this.value || []).join('\n');

                this.$nextTick(() => {
                    this.setAutoHeight();
                });
            },

            stopWordsToArray() {
                let value = this.stopWords.split('\n');

                this.$emit('input', value);
            },

            setAutoHeight() {
                let oldHeight = this.$refs.textarea.clientHeight;
                this.height = 'auto';

                this.$nextTick(() => {
                    let totalHeight = this.$refs.textarea.scrollHeight;

                    if (oldHeight !== totalHeight) {
                        this.height = `${(totalHeight + 10)}px`;
                    }
                });
            },

        }

    }
</script>

<style lang="scss" scoped>

    .mp-stop-words {

        textarea {
            width: 100%;
        }

    }

</style>