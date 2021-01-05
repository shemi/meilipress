<template>
    <mp-popup :is-open="isActive" hide-close-button class="mp-ajax-sync-popup">
        <div class="header">
            <p class="title">{{ 'Syncing all the documents of {indexName}' | __({indexName}) }}</p>
            <p class="danger-message">{{ 'Do not close this window' | __ }}</p>
        </div>

        <div class="progress">
            <p class="progress-message">
                <span class="spinner" :class="{'is-active': loading}"></span>
                <span class="text">{{ 'Syncing batch #{current} out of {total}' | __(state) }}</span>
            </p>

            <div class="progress-bar">
                <div class="indicator" :style="{width: ((state.current / state.total) * 100) + '%'}"></div>
            </div>
        </div>

        <div class="actions">
            <a href="" @click.prevent="cancel" class="cancel">
                {{ canceling ? 'Canceling...' : 'Cancel' | __ }}
            </a>
        </div>

    </mp-popup>
</template>

<script>
    export default {

        props: {

        },

        data() {
            return {
                isActive: false,
                loading: false,
                canceling: false,
                indexName: '',
                indexId: '',
                state: {
                    current: 0,
                    total: 0
                }
            }
        },

        mounted() {

        },

        methods: {

            show(name, state) {
                this.update(state);
                this.indexName = name;
                this.isActive = true;
            },

            hide() {
                this.update({current: 0, total: 0});
                this.indexName = '';
                this.loading = false;
                this.isActive = false;
            },

            update(state) {
                this.state = state;
            },

            loadingStart() {
                this.loading = true;
            },

            loadingStop() {
                this.loading = false;
            },

            cancel() {
                this.$api.cancelReindex();
            }

        }

    }
</script>

<style lang="scss" scoped>

    .mp-ajax-sync-popup {

        .header{
            text-align: center;

            .title {
                font-size: 1.3em;
                margin-top: 0;
                margin-bottom: 4px;
            }

            .danger-message {
                margin: 0;
                padding: 4px;
                background-color: red;
                color: white;
                border-radius: 4px;
                font-weight: 700;
            }
        }

        .progress {
            margin-top: 8px;

            .progress-message {
                display: flex;
                align-items: center;
                margin: 0;
                font-size: 1.1em;

                .spinner {
                    margin: 0;
                    -webkit-margin-end: 8px;
                    float: none;
                }
            }
        }

        .progress-bar {
            position: relative;
            width: 100%;
            height: 22px;
            overflow: hidden;
            border-radius: 40px;
            background-color: lightgrey;
            margin-top: 4px;

            .indicator {
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                background-color: #1dc74c;
            }
        }

        .actions {
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;

            .cancel {
                color: red;
            }
        }

    }

</style>