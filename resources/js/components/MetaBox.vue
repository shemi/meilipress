<template>
    <div class="postbox" :class="{'has-footer': !! $slots['footer']}">
        <div class="postbox-header">
            <h2>{{ title }}</h2>
            <div class="handle-actions hide-if-no-js">
              <slot name="header-actions"></slot>
            </div>
        </div>

        <div class="inside">
            <slot></slot>
        </div>

        <div class="footer" v-if="$slots.footer">
            <slot name="footer"></slot>
        </div>
    </div>
</template>

<script>
    export default {

        props: {
            title: String
        }

    }
</script>

<style lang="scss" scoped>

    .postbox {
        &.has-footer {
            .inside {
                margin-bottom: 0;
            }
        }

        .index-state {
            p {
                margin: 0;

                .dashicons {
                    -webkit-margin-end: 4px;
                }

                + p {
                    margin-top: 8px;
                }
            }
        }

    }

    h2 {
        font-size: 14px;
        padding: 8px 12px;
        margin: 0;
        line-height: 1.4;
    }

    .footer {
        padding: 10px;
        clear: both;
        border-top: 1px solid #ddd;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

</style>