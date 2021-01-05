<template>
    <mp-meta-box :title="$trans('Search Test')" class="mp-search-meta-box">

        <div class="mp-search-input">
            <label for="mp-search-metabox-field" class="screen-reader-text">
                {{ 'Search' | __ }}
            </label>
            <input type="search"
                   id="mp-search-metabox-field"
                   @input="search"
                   :placeholder="$trans('Type To Search Documents')"
                   v-model="term">
        </div>

        <div class="results" v-if="hasResults">

            <p class="hits-title">
                {{ 'Found {count} documents in {time}ms' | __({count, time}) }}
            </p>

            <div class="hits">

                <div class="hit" v-for="hit in hits">
                    <p v-for="(value, key) in hit" :title="value">
                        <b>{{ key }}:</b>&nbsp;
                        <span>{{ value }}</span>
                    </p>
                </div>

            </div>

        </div>

    </mp-meta-box>
</template>

<script>
    export default {

        props: {
            indexId: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                term: '',
                hasResults: false,
                columns: [],
                hits: [],
                count: 0,
                time: 0
            }
        },

        methods: {
            async search() {
                if(! this.indexId || ! this.term) {
                    this.hasResults = false;
                    this.hits = [];
                    this.count = 0;
                    this.time = 0;

                    return;
                }

                const res = await this.$api.post('indexes/search', {
                    index: this.indexId,
                    term: this.term
                });

                if(! res.success) {
                    return;
                }

                const {hits_count, hits, time} = res.data;

                this.hits = hits;
                this.count = hits_count;
                this.time = time;
                this.hasResults = true;
            }
        }

    }
</script>

<style lang="scss" scoped>

    .mp-search-meta-box {

        .mp-search-input {
            width: 100%;

            input {
                width: 100%;
            }
        }

        .results {
            overflow: hidden;
            width: 100%;
            display: flex;
            flex-direction: column;

            .hits-title {
                text-align: center;
                font-weight: bold;
            }

            .hits {
                max-height: 500px;
                overflow: auto;

                .hit {
                    + .hit {
                        margin-top: 8px;
                        padding-top: 8px;
                        border-top: 1px solid #7e8993;
                    }

                    p {
                        display: flex;
                        margin: 0;

                        + p {
                            margin-top: 4px;
                        }
                    }

                    span {
                        display: block;
                        width: 100%;
                        overflow: hidden;
                        white-space: nowrap;
                        text-overflow: ellipsis;
                    }
                }
            }
        }

    }

</style>